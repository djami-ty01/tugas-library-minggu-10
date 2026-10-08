<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\WindowsEscpos;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class StrukController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('kasir');
    }

    public function print(Request $request, WindowsEscpos $windowsEscpos): View|RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:999'],
            'payment' => ['required', 'numeric', 'min:0'],
        ]);

        $quantities = collect($validated['items'])->keyBy('id');
        $products = Product::query()
            ->whereIn('id', $quantities->keys())
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        if ($products->count() !== $quantities->count()) {
            throw ValidationException::withMessages([
                'items' => 'Salah satu produk sudah tidak aktif. Muat ulang halaman kasir.',
            ]);
        }

        foreach ($products as $product) {
            if ((int) $quantities->get($product->id)['qty'] > $product->stock) {
                throw ValidationException::withMessages([
                    'items' => "Stok {$product->name} tidak mencukupi.",
                ]);
            }
        }

        $items = $products->map(function (Product $product) use ($quantities): array {
            $qty = (int) $quantities->get($product->id)['qty'];
            $unitPrice = (float) $product->price;

            return [
                'name' => $product->name,
                'qty' => $qty,
                'price' => $unitPrice,
                'subtotal' => $unitPrice * $qty,
            ];
        })->values();

        $total = $items->sum('subtotal');
        $payment = (float) $validated['payment'];

        if ($payment < $total) {
            return back()
                ->withInput()
                ->withErrors(['payment' => 'Uang yang dibayar belum cukup.']);
        }

        $receiptNumber = 'TRX-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4));
        $printedAt = now();
        $printerError = null;

        if (config('services.receipt.enabled')) {
            try {
                $windowsEscpos->printReceipt(
                    config('services.receipt.printer'),
                    $receiptNumber,
                    $items->all(),
                    $total,
                    $payment,
                    $payment - $total,
                    $request->user()->name,
                    $printedAt->format('d-m-Y H:i:s')
                );
            } catch (Exception $exception) {
                Log::error('Gagal mencetak struk ke printer kasir.', [
                    'printer' => config('services.receipt.printer'),
                    'receipt_number' => $receiptNumber,
                    'exception' => $exception->getMessage(),
                ]);

                $printerError = 'Cetak langsung gagal: '.$exception->getMessage();
            }
        }

        return view('struk', [
            'receiptNumber' => $receiptNumber,
            'items' => $items,
            'total' => $total,
            'payment' => $payment,
            'change' => $payment - $total,
            'cashier' => $request->user()->name,
            'printedAt' => $printedAt,
            'printerError' => $printerError,
        ]);
    }

}
