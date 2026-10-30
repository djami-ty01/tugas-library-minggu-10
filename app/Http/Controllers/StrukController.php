<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\Product;
use App\Services\WindowsEscpos;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class StrukController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('kasir');
    }

    public function print(Request $request): View|RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:999'],
            'payment' => ['required', 'numeric', 'min:0'],
        ]);

        $quantities = collect($validated['items'])->keyBy('id');
        $payment = (float) $validated['payment'];

        $transaction = DB::transaction(function () use ($quantities, $payment, $request): array {
            $products = Product::query()
                ->whereIn('id', $quantities->keys())
                ->where('is_active', true)
                ->orderBy('id')
                ->lockForUpdate()
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
                $quantity = (int) $quantities->get($product->id)['qty'];
                $unitPrice = (float) $product->price;

                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'qty' => $quantity,
                    'price' => $unitPrice,
                    'subtotal' => $unitPrice * $quantity,
                ];
            })->values();

            $total = $items->sum('subtotal');
            if ($payment < $total) {
                throw ValidationException::withMessages([
                    'payment' => 'Uang yang dibayar belum cukup.',
                ]);
            }

            $printedAt = now();
            $receiptNumber = 'TRX-'.$printedAt->format('Ymd-His').'-'.Str::upper(Str::random(4));
            $order = Order::create([
                'receipt_number' => $receiptNumber,
                'user_id' => $request->user()->id,
                'total_amount' => $total,
                'payment_amount' => $payment,
                'change_amount' => $payment - $total,
                'paid_at' => $printedAt,
            ]);

            $order->items()->createMany($items->map(fn (array $item): array => [
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'quantity' => $item['qty'],
                'unit_price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ])->all());

            foreach ($products as $product) {
                $product->decrement('stock', (int) $quantities->get($product->id)['qty']);
            }

            return compact('order', 'items', 'total', 'printedAt', 'receiptNumber');
        });

        $order = $transaction['order'];
        $items = $transaction['items'];
        $total = $transaction['total'];
        $printedAt = $transaction['printedAt'];
        $receiptNumber = $transaction['receiptNumber'];
        $printerError = null;

        $broadcastError = null;
        try {
            event(new OrderPlaced(
                orderId: $order->id,
                receiptNumber: $receiptNumber,
                total: (float) $order->total_amount,
                cashierName: $request->user()->name,
            ));
        } catch (Throwable $exception) {
            Log::error('Gagal mengirim notifikasi transaksi melalui Reverb.', [
                'order_id' => $order->id,
                'receipt_number' => $receiptNumber,
                'exception' => $exception->getMessage(),
            ]);

            $broadcastError = 'Notifikasi real-time gagal dikirim. Pastikan server Reverb sedang berjalan.';
        }

        $receiptData = [
            'receiptNumber' => $receiptNumber,
            'items' => $items,
            'total' => $total,
            'payment' => $payment,
            'change' => $payment - $total,
            'cashier' => $request->user()->name,
            'printedAt' => $printedAt,
            'printerError' => $printerError,
            'broadcastError' => $broadcastError,
            'order' => $order,
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'receiptHtml' => view('partials.receipt-content', $receiptData)->render(),
                'receiptNumber' => $receiptNumber,
                'invoiceDownloadUrl' => route('orders.invoice.download', $order),
                'printUrl' => route('orders.print', $order),
                'printerError' => $printerError,
                'broadcastError' => $broadcastError,
            ]);
        }

        return view('struk', $receiptData);
    }

    public function printOrder(Request $request, Order $order, WindowsEscpos $windowsEscpos): JsonResponse
    {
        $validated = $request->validate([
            'printer' => ['required', 'string', Rule::in(config('services.receipt.printers', []))],
        ]);

        if (! config('services.receipt.enabled')) {
            return response()->json([
                'message' => 'Cetak langsung belum diaktifkan. Gunakan dialog cetak browser.',
            ], 422);
        }

        $order->load(['items', 'cashier']);
        $items = $order->items->map(fn ($item): array => [
            'name' => $item->product_name,
            'qty' => $item->quantity,
            'price' => (float) $item->unit_price,
            'subtotal' => (float) $item->subtotal,
        ])->all();

        try {
            $windowsEscpos->printReceipt(
                $validated['printer'],
                $order->receipt_number,
                $items,
                (float) $order->total_amount,
                (float) $order->payment_amount,
                (float) $order->change_amount,
                $order->cashier->name,
                $order->paid_at->format('d-m-Y H:i:s')
            );
        } catch (Exception $exception) {
            Log::error('Gagal mencetak struk ke printer kasir.', [
                'printer' => $validated['printer'],
                'order_id' => $order->id,
                'receipt_number' => $order->receipt_number,
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Cetak gagal. Periksa nama printer dan koneksi printer yang dipilih.',
            ], 422);
        }

        return response()->json(['message' => 'Struk berhasil dikirim ke printer.']);
    }

    public function invoice(Order $order)
    {
        return $this->makeInvoicePdf($order)->stream('invoice_'.$order->receipt_number.'.pdf');
    }

    public function downloadInvoice(Order $order)
    {
        return $this->makeInvoicePdf($order)->download('invoice_'.$order->receipt_number.'.pdf');
    }

    private function makeInvoicePdf(Order $order)
    {
        $order->load('items');
        $order->total = $order->total_amount;

        return Pdf::loadView('invoice.invoice', ['invoice' => $order]);
    }
}
