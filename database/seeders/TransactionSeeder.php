<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\CashierShift;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Seed the application's sample transactions.
     */
    public function run(): void
    {
        $kasir = User::query()->where('email', 'kasir@booth.test')->first();

        if (! $kasir) {
            return;
        }

        $products = Product::query()->get()->keyBy('name');
        $shift = CashierShift::query()
            ->where('user_id', $kasir->id)
            ->where('opened_at', '2026-04-22 08:00:00')
            ->first();

        $transactions = [
            [
                'invoice_number' => 'TRX-20260422-001',
                'transaction_date' => '2026-04-22 09:15:00',
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'notes' => 'Transaksi pagi.',
                'items' => [
                    ['product' => 'Es Teh', 'qty' => 2],
                    ['product' => 'Pentol Bakar', 'qty' => 1],
                ],
                'paid_amount' => 25000,
            ],
            [
                'invoice_number' => 'TRX-20260422-002',
                'transaction_date' => '2026-04-22 12:40:00',
                'payment_method' => 'qris',
                'payment_status' => 'confirmed',
                'notes' => 'QRIS sudah dikonfirmasi kasir.',
                'items' => [
                    ['product' => 'Es Jeruk', 'qty' => 1],
                    ['product' => 'Sosis Bakar', 'qty' => 2],
                ],
                'paid_amount' => null,
            ],
            [
                'invoice_number' => 'TRX-20260422-003',
                'transaction_date' => '2026-04-22 18:10:00',
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'notes' => 'Transaksi sore.',
                'items' => [
                    ['product' => 'Keripik Kentang', 'qty' => 1],
                    ['product' => 'Es Teh', 'qty' => 1],
                    ['product' => 'Sosis Bakar', 'qty' => 1],
                ],
                'paid_amount' => 25000,
            ],
        ];

        foreach ($transactions as $seed) {
            $details = [];
            $subtotal = 0;

            foreach ($seed['items'] as $item) {
                $product = $products->get($item['product']);

                if (! $product) {
                    continue;
                }

                $lineSubtotal = $product->price * $item['qty'];
                $subtotal += $lineSubtotal;

                $details[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $lineSubtotal,
                ];
            }

            if ($details === []) {
                continue;
            }

            $paidAmount = $seed['payment_method'] === 'qris'
                ? $subtotal
                : ($seed['paid_amount'] ?? $subtotal);

            $transaction = Transaction::updateOrCreate(
                ['invoice_number' => $seed['invoice_number']],
                [
                    'user_id' => $kasir->id,
                    'shift_id' => $shift?->id,
                    'transaction_date' => $seed['transaction_date'],
                    'payment_method' => $seed['payment_method'],
                    'subtotal' => $subtotal,
                    'total_amount' => $subtotal,
                    'paid_amount' => $paidAmount,
                    'change_amount' => $seed['payment_method'] === 'cash'
                        ? max($paidAmount - $subtotal, 0)
                        : 0,
                    'payment_status' => $seed['payment_status'],
                    'notes' => $seed['notes'],
                ],
            );

            $transaction->details()->delete();
            $transaction->details()->createMany($details);
        }
    }
}
