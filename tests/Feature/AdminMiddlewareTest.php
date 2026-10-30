<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin'))
            ->assertRedirect(route('login'));
    }

    public function test_regular_user_is_forbidden_from_admin_page(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->get(route('admin'))
            ->assertForbidden();
    }

    public function test_admin_can_open_admin_page(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('admin'))
            ->assertOk()
            ->assertSee('Selamat Datang di Halaman Admin');
    }

    public function test_admin_dashboard_shows_store_metrics_recent_orders_and_low_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'kasir', 'name' => 'Kasir Pagi']);

        Product::create([
            'name' => 'Susu Stok Rendah',
            'price' => 12000,
            'stock' => 4,
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'Produk Nonaktif',
            'price' => 5000,
            'stock' => 2,
            'is_active' => false,
        ]);

        $order = Order::create([
            'receipt_number' => 'TRX-ADMIN-001',
            'user_id' => $cashier->id,
            'total_amount' => 125000,
            'payment_amount' => 130000,
            'change_amount' => 5000,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin'))
            ->assertOk()
            ->assertSee('Selamat Datang di Halaman Admin')
            ->assertSee('Total produk')
            ->assertSee('Penjualan hari ini')
            ->assertSee('125.000')
            ->assertSee('1', false)
            ->assertSee('TRX-ADMIN-001')
            ->assertSee('Kasir Pagi')
            ->assertSee('Susu Stok Rendah')
            ->assertSee(route('orders.invoice', $order));
    }

    public function test_cashier_can_open_cashier_page_but_not_admin_page(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($cashier)
            ->get(route('kasir'))
            ->assertOk()
            ->assertSee('Selamat Datang, Kasir');

        $this->actingAs($cashier)
            ->get(route('admin'))
            ->assertForbidden();
    }
}
