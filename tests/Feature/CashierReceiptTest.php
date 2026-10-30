<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\WindowsEscpos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class CashierReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.receipt.printer' => 'MP80MPro',
            'services.receipt.printers' => ['MP80MPro', 'Office Printer'],
        ]);
    }

    public function test_cashier_can_pay_and_open_a_receipt_with_server_calculated_totals(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'name' => 'Kopi',
            'price' => 5000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $printer = Mockery::mock(WindowsEscpos::class);
        $printer->shouldReceive('printReceipt')
            ->once()
            ->with(
                'Office Printer',
                Mockery::type('string'),
                Mockery::on(fn (array $items): bool => $items[0]['name'] === 'Kopi' && $items[0]['qty'] === 2),
                10000.0,
                12000.0,
                2000.0,
                $cashier->name,
                Mockery::type('string')
            );
        $this->app->instance(WindowsEscpos::class, $printer);

        $response = $this->actingAs($cashier)
            ->post(route('struk.cetak'), [
                'items' => [
                    ['id' => $product->id, 'qty' => 2, 'price' => 1, 'name' => 'Harga palsu'],
                ],
                'payment' => 12000,
            ])
            ->assertOk()
            ->assertViewIs('struk')
            ->assertViewHas('total', 10000.0)
            ->assertViewHas('payment', 12000.0)
            ->assertViewHas('change', 2000.0)
            ->assertViewHas('order')
            ->assertSee('Kopi')
            ->assertSee('Dialog cetak browser akan dibuka.')
            ->assertSee('Buka PDF')
            ->assertSee('window.print()')
            ->assertDontSee('Harga palsu');

        $this->postJson(route('orders.print', $response->viewData('order')), [
            'printer' => 'Office Printer',
        ])->assertOk()
            ->assertJsonPath('message', 'Struk berhasil dikirim ke printer.');
    }

    public function test_checkout_can_return_receipt_for_the_cashier_page_modal(): void
    {
        config(['services.receipt.enabled' => false]);

        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'name' => 'Teh',
            'price' => 4000,
            'stock' => 3,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)
            ->postJson(route('struk.cetak'), [
                'items' => [['id' => $product->id, 'qty' => 2]],
                'payment' => 10000,
            ])
            ->assertOk()
            ->assertJsonPath('printerError', null)
            ->assertJsonPath('broadcastError', null);

        $this->assertStringStartsWith('TRX-', $response->json('receiptNumber'));
        $this->assertSame(
            route('orders.invoice.download', \App\Models\Order::firstOrFail()),
            $response->json('invoiceDownloadUrl')
        );
        $this->assertSame(
            route('orders.print', \App\Models\Order::firstOrFail()),
            $response->json('printUrl')
        );
        $this->assertStringContainsString('Teh', $response->json('receiptHtml'));
        $this->assertStringContainsString('Rp 8.000', $response->json('receiptHtml'));
        $this->assertStringContainsString('Rp 2.000', $response->json('receiptHtml'));

        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_cashier_page_contains_the_receipt_popup(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->get(route('kasir'))
            ->assertOk()
            ->assertSee('id="receiptModal"', false)
            ->assertSee('id="printReceiptBtn"', false)
            ->assertSee('Pilih Printer Browser')
            ->assertSee('Unduh Invoice PDF')
            ->assertSee('id="receiptPrinter"', false)
            ->assertSee('Pilih Printer Browser')
            ->assertSee('Cetak ke Printer Struk')
            ->assertSee('Office Printer')
            ->assertSee("Accept: 'application/json'", false);
    }

    public function test_printer_failure_keeps_the_receipt_available_for_browser_printing(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'name' => 'Kopi',
            'price' => 5000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $printer = Mockery::mock(WindowsEscpos::class);
        $printer->shouldReceive('printReceipt')
            ->once()
            ->andThrow(new \RuntimeException('Printer share tidak tersedia.'));
        $this->app->instance(WindowsEscpos::class, $printer);
        Log::shouldReceive('error')->once();
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $checkout = $this->actingAs($cashier)
            ->post(route('struk.cetak'), [
                'items' => [['id' => $product->id, 'qty' => 1]],
                'payment' => 5000,
            ])
            ->assertOk();

        $this->postJson(route('orders.print', $checkout->viewData('order')), [
            'printer' => 'MP80MPro',
        ])->assertUnprocessable()
            ->assertJsonPath('message', 'Cetak gagal. Periksa nama printer dan koneksi printer yang dipilih.');
    }

    public function test_cashier_cannot_select_an_unconfigured_printer(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $order = \App\Models\Order::create([
            'receipt_number' => 'TRX-UNCONFIGURED',
            'user_id' => $cashier->id,
            'total_amount' => 5000,
            'payment_amount' => 5000,
            'change_amount' => 0,
            'paid_at' => now(),
        ]);

        $this->actingAs($cashier)
            ->postJson(route('orders.print', $order), ['printer' => 'Unknown printer'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('printer');
    }

    public function test_cashier_cannot_complete_a_payment_below_the_server_calculated_total(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'name' => 'Susu',
            'price' => 8000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($cashier)
            ->from(route('kasir'))
            ->post(route('struk.cetak'), [
                'items' => [['id' => $product->id, 'qty' => 1]],
                'payment' => 5000,
            ])
            ->assertRedirect(route('kasir'))
            ->assertSessionHasErrors('payment');
    }

    public function test_cashier_sees_products_even_when_out_of_stock_and_cannot_checkout_more_than_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $availableProduct = Product::create([
            'name' => 'Roti',
            'price' => 3000,
            'stock' => 2,
            'is_active' => true,
        ]);
        $outOfStockProduct = Product::create([
            'name' => 'Air Mineral',
            'price' => 2000,
            'stock' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($cashier)
            ->get(route('kasir'))
            ->assertOk()
            ->assertSee('Roti')
            ->assertSee('Air Mineral')
            ->assertSee('Stok: 0 (habis)');

        $this->from(route('kasir'))
            ->post(route('struk.cetak'), [
                'items' => [['id' => $availableProduct->id, 'qty' => 3]],
                'payment' => 10000,
            ])
            ->assertRedirect(route('kasir'))
            ->assertSessionHasErrors('items');

        $this->assertSame(2, $availableProduct->fresh()->stock);
        $this->assertSame(0, $outOfStockProduct->fresh()->stock);
    }

    public function test_opening_the_checkout_url_directly_returns_to_the_cashier_page(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->get(route('struk.cetak.form'))
            ->assertRedirect(route('kasir'));
    }
}
