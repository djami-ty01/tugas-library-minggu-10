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

        config(['services.receipt.printer' => 'MP80MPro']);
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
                'MP80MPro',
                Mockery::type('string'),
                Mockery::on(fn (array $items): bool => $items[0]['name'] === 'Kopi' && $items[0]['qty'] === 2),
                10000.0,
                12000.0,
                2000.0,
                $cashier->name,
                Mockery::type('string')
            );
        $this->app->instance(WindowsEscpos::class, $printer);

        $this->actingAs($cashier)
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
            ->assertSee('Kopi')
            ->assertSee('Struk berhasil dikirim ke printer.')
            ->assertDontSee('Harga palsu');
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

        $this->actingAs($cashier)
            ->post(route('struk.cetak'), [
                'items' => [['id' => $product->id, 'qty' => 1]],
                'payment' => 5000,
            ])
            ->assertOk()
            ->assertSee('Cetak langsung gagal:')
            ->assertSee('Membuka dialog cetak Windows sebagai alternatif.');
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
