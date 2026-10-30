<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin', [
            'productCount' => Product::count(),
            'activeProductCount' => Product::where('is_active', true)->count(),
            'todayOrderCount' => Order::whereDate('paid_at', today())->count(),
            'todaySales' => Order::whereDate('paid_at', today())->sum('total_amount'),
            'recentOrders' => Order::with('cashier')->latest('paid_at')->limit(5)->get(),
            'lowStockProducts' => Product::query()
                ->where('is_active', true)
                ->where('stock', '<=', 10)
                ->orderBy('stock')
                ->limit(4)
                ->get(),
        ]);
    }
}
