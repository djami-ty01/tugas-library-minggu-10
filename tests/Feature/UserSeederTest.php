<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_admin_and_cashier_accounts_with_the_requested_password(): void
    {
        $this->seed(\Database\Seeders\UserSeeder::class);

        $admin = User::where('email', 'admin@minimarket.test')->firstOrFail();
        $cashier = User::where('email', 'kasir@minimarket.test')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertSame('kasir', $cashier->role);
        $this->assertTrue(Hash::check('admin123', $admin->password));
        $this->assertTrue(Hash::check('admin123', $cashier->password));
        $this->assertSame(2, User::count());
    }
}
