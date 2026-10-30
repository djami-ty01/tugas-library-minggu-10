<?php

namespace Tests\Feature;

use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_successful_checkout_persists_the_order_and_broadcasts_its_details(): void
    {
        config(['services.receipt.enabled' => false]);
        Event::fake([OrderPlaced::class]);

        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'name' => 'Kopi',
            'price' => 5000,
            'stock' => 4,
            'is_active' => true,
        ]);

        $this->actingAs($cashier)
            ->post(route('struk.cetak'), [
                'items' => [['id' => $product->id, 'qty' => 2]],
                'payment' => 12000,
            ])
            ->assertOk()
            ->assertSee('Dialog cetak browser akan dibuka.')
            ->assertSee('Buka PDF')
            ->assertSee('window.print()');

        $order = Order::with('items')->firstOrFail();

        $this->assertSame($cashier->id, $order->user_id);
        $this->assertSame('10000.00', $order->total_amount);
        $this->assertSame('12000.00', $order->payment_amount);
        $this->assertSame('2000.00', $order->change_amount);
        $this->assertSame(1, $order->items->count());
        $this->assertSame('Kopi', $order->items->first()->product_name);
        $this->assertSame(2, $order->items->first()->quantity);
        $this->assertSame(2, $product->fresh()->stock);

        Event::assertDispatched(OrderPlaced::class, fn (OrderPlaced $event): bool => (
            $event->orderId === $order->id
            && $event->receiptNumber === $order->receipt_number
            && $event->total === 10000.0
            && $event->cashierName === $cashier->name
        ));
    }

    public function test_an_unsuccessful_checkout_does_not_persist_or_broadcast_an_order(): void
    {
        Event::fake([OrderPlaced::class]);

        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'name' => 'Teh',
            'price' => 5000,
            'stock' => 3,
            'is_active' => true,
        ]);

        $this->actingAs($cashier)
            ->from(route('kasir'))
            ->post(route('struk.cetak'), [
                'items' => [['id' => $product->id, 'qty' => 2]],
                'payment' => 5000,
            ])
            ->assertRedirect(route('kasir'))
            ->assertSessionHasErrors('payment');

        $this->assertSame(0, Order::count());
        $this->assertSame(3, $product->fresh()->stock);
        Event::assertNotDispatched(OrderPlaced::class);
    }

    public function test_order_broadcast_channel_is_private_and_contains_receipt_details(): void
    {
        $event = new OrderPlaced(7, 'TRX-123', 25000.0, 'Kasir 1');
        $channel = $event->broadcastOn();

        $this->assertInstanceOf(ShouldBroadcastNow::class, $event);
        $this->assertSame('private-orders', $channel->name);
        $this->assertSame([
            'order_id' => 7,
            'receipt_number' => 'TRX-123',
            'total' => 25000.0,
            'cashier_name' => 'Kasir 1',
        ], $event->broadcastWith());
    }

    public function test_only_authenticated_staff_can_subscribe_to_order_notifications(): void
    {
        config([
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb' => [
                'driver' => 'reverb',
                'key' => 'test-key',
                'secret' => 'test-secret',
                'app_id' => 'test-app',
                'options' => [
                    'host' => '127.0.0.1',
                    'port' => 8080,
                    'scheme' => 'http',
                    'useTLS' => false,
                ],
            ],
        ]);
        Broadcast::purge();
        require base_path('routes/channels.php');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->postJson('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-orders',
            ])
            ->assertOk()
            ->assertJsonStructure(['auth']);

        $cashier = User::factory()->create(['role' => 'kasir']);
        $this->actingAs($cashier)
            ->postJson('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-orders',
            ])
            ->assertOk()
            ->assertJsonStructure(['auth']);

        $unknownRole = User::factory()->create(['role' => 'manager']);
        $this->actingAs($unknownRole)
            ->postJson('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-orders',
            ])
            ->assertForbidden();

        auth()->logout();
        $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-orders',
        ])->assertUnauthorized();
    }

    public function test_cashier_can_open_a_persisted_order_invoice_pdf(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $order = Order::create([
            'receipt_number' => 'TRX-123',
            'user_id' => $cashier->id,
            'total_amount' => 5000,
            'payment_amount' => 5000,
            'change_amount' => 0,
            'paid_at' => now(),
        ]);
        $order->items()->create([
            'product_name' => 'Kopi',
            'quantity' => 1,
            'unit_price' => 5000,
            'subtotal' => 5000,
        ]);

        $this->actingAs($cashier)
            ->get(route('orders.invoice', $order))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'inline; filename=invoice_TRX-123.pdf');
    }

    public function test_cashier_can_download_a_persisted_order_invoice_pdf(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $order = Order::create([
            'receipt_number' => 'TRX-456',
            'user_id' => $cashier->id,
            'total_amount' => 5000,
            'payment_amount' => 5000,
            'change_amount' => 0,
            'paid_at' => now(),
        ]);
        $order->items()->create([
            'product_name' => 'Kopi',
            'quantity' => 1,
            'unit_price' => 5000,
            'subtotal' => 5000,
        ]);

        $this->actingAs($cashier)
            ->get(route('orders.invoice.download', $order))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=invoice_TRX-456.pdf');
    }
}
