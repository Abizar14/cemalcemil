<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cashiers cannot access admin dashboard.
     */
    public function test_cashier_cannot_access_admin_dashboard(): void
    {
        $cashier = User::factory()->cashier()->create();

        $response = $this->actingAs($cashier)->get(route('dashboard'));

        $response->assertForbidden();
    }

    /**
     * Cashiers cannot access category management.
     */
    public function test_cashier_cannot_access_category_management(): void
    {
        $cashier = User::factory()->cashier()->create();

        $response = $this->actingAs($cashier)->get(route('categories.index'));

        $response->assertForbidden();
    }

    /**
     * Cashiers can only open their own transactions.
     */
    public function test_cashier_cannot_open_other_users_transaction(): void
    {
        $owner = User::factory()->cashier()->create();
        $otherCashier = User::factory()->cashier()->create();
        $transaction = Transaction::create([
            'user_id' => $owner->id,
            'invoice_number' => 'TRX-20260422-0099',
            'transaction_date' => now(),
            'payment_method' => 'cash',
            'subtotal' => 10000,
            'total_amount' => 10000,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'payment_status' => 'paid',
            'transaction_status' => 'completed',
            'notes' => null,
        ]);

        $response = $this->actingAs($otherCashier)->get(route('transactions.show', $transaction));

        $response->assertForbidden();
    }
}
