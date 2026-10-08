<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class WindowsEscpos
{
    /**
     * @param array<int, array{name: string, qty: int, price: float, subtotal: float}> $items
     */
    public function printReceipt(
        string $printerName,
        string $receiptNumber,
        array $items,
        float $total,
        float $payment,
        float $change,
        string $cashier,
        string $printedAt
    ): void {
        $printer = new Printer(new WindowsPrintConnector($printerName));

        try {
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("POLIJE MART\n");
            $printer->setEmphasis(false);
            $printer->text("Jl. Mastrip No. 164, Jember\n");
            $printer->text("--------------------------------\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No   : {$receiptNumber}\n");
            $printer->text("Kasir: {$cashier}\n");
            $printer->text("Tgl  : {$printedAt}\n");
            $printer->text("--------------------------------\n");

            foreach ($items as $item) {
                $printer->text($item['name']."\n");
                $printer->text(
                    $item['qty'].' x Rp '.number_format($item['price'], 0, ',', '.')
                    .' = Rp '.number_format($item['subtotal'], 0, ',', '.')."\n"
                );
            }

            $printer->text("--------------------------------\n");
            $printer->setEmphasis(true);
            $printer->text('TOTAL   : Rp '.number_format($total, 0, ',', '.')."\n");
            $printer->setEmphasis(false);
            $printer->text('BAYAR   : Rp '.number_format($payment, 0, ',', '.')."\n");
            $printer->text('KEMBALI : Rp '.number_format($change, 0, ',', '.')."\n");
            $printer->text("--------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima kasih telah berbelanja\n");
            $printer->feed(3);
            $printer->cut();
        } finally {
            $printer->close();
        }
    }
}