<?php

namespace Database\Seeders;

use App\Models\CashierShift;
use App\Models\User;
use Illuminate\Database\Seeder;

class CashierShiftSeeder extends Seeder
{
    /**
     * Seed sample cashier shifts.
     */
    public function run(): void
    {
        $cashier = User::query()->where('email', 'kasir@booth.test')->first();

        if (! $cashier) {
            return;
        }

        CashierShift::updateOrCreate(
            [
                'user_id' => $cashier->id,
                'opened_at' => '2026-04-22 08:00:00',
            ],
            [
                'closed_at' => '2026-04-22 20:00:00',
                'opening_cash' => 100000,
                'closing_cash_expected' => 180000,
                'closing_cash_actual' => 180000,
                'cash_difference' => 0,
                'opening_notes' => 'Shift demo untuk data awal aplikasi.',
                'closing_notes' => 'Penutupan shift demo.',
                'status' => 'closed',
            ],
        );
    }
}
