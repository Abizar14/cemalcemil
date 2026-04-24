<?php

namespace Database\Seeders;

use App\Models\CashFlow;
use App\Models\CashierShift;
use App\Models\User;
use Illuminate\Database\Seeder;

class CashFlowSeeder extends Seeder
{
    /**
     * Seed the application's cash flow records.
     */
    public function run(): void
    {
        $cashier = User::query()->where('email', 'kasir@booth.test')->first();

        if (! $cashier) {
            return;
        }

        $shift = CashierShift::query()
            ->where('user_id', $cashier->id)
            ->where('opened_at', '2026-04-22 08:00:00')
            ->first();

        $cashFlows = [
            [
                'flow_date' => '2026-04-22 08:00:00',
                'type' => 'in',
                'amount' => 100000,
                'source' => 'Modal Awal',
                'description' => 'Modal buka booth.',
            ],
            [
                'flow_date' => '2026-04-22 13:00:00',
                'type' => 'out',
                'amount' => 20000,
                'source' => 'Belanja Es Batu',
                'description' => 'Pembelian operasional siang hari.',
            ],
            [
                'flow_date' => '2026-04-22 19:30:00',
                'type' => 'out',
                'amount' => 15000,
                'source' => 'Parkir',
                'description' => 'Biaya parkir booth.',
            ],
        ];

        foreach ($cashFlows as $cashFlow) {
            CashFlow::updateOrCreate(
                [
                    'user_id' => $cashier->id,
                    'flow_date' => $cashFlow['flow_date'],
                    'type' => $cashFlow['type'],
                ],
                [
                    'shift_id' => $shift?->id,
                    'amount' => $cashFlow['amount'],
                    'source' => $cashFlow['source'],
                    'description' => $cashFlow['description'],
                ],
            );
        }
    }
}
