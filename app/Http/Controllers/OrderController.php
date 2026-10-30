<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\OrderPlaced;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Simulasi data order (atau sesuaikan dengan model Order Anda)
        $order = (object) [
            'id' => rand(100, 999),
            'total' => 150000,
        ];

        // Trigger Event Broadcast Reverb
        event(new OrderPlaced($order, auth()->user()));

        return response()->json([
            'message' => 'Order berhasil dibuat dan event telah dipicu!',
            'order' => $order
        ]);
    }
}