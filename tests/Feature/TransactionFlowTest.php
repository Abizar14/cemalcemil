<?php

namespace Tests\Feature;

use App\Models\CashFlow;
use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cash transactions can be created from the cashier form.
     */
    public function test_authenticated_user_can_create_cash_transaction(): void
    {
        $user = User::factory()->cashier()->create();
        $shift = CashierShift::create([
            'user_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => 50000,
            'status' => 'open',
        ]);
        $category = Category::create([
            'name' => 'Minuman',
            'description' => 'Kategori minuman.',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Es Teh Jumbo',
            'price' => 8000,
            'is_active' => true,
            'track_stock' => true,
            'stock_quantity' => 10,
            'stock_alert_threshold' => 2,
        ]);

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'payment_method' => 'cash',
            'paid_amount' => 20000,
            'notes' => 'Transaksi tunai.',
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ],
        ]);

        $transaction = Transaction::query()->with('details')->first();

        $this->assertNotNull($transaction);
        $response->assertRedirect(route('transactions.show', $transaction));
        $this->assertSame($shift->id, $transaction->shift_id);
        $this->assertSame('cash', $transaction->payment_method);
        $this->assertSame('paid', $transaction->payment_status);
        $this->assertSame('completed', $transaction->transaction_status);
        $this->assertEquals(16000, (float) $transaction->total_amount);
        $this->assertEquals(4000, (float) $transaction->change_amount);
        $this->assertCount(1, $transaction->details);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 8,
        ]);
    }

    /**
     * Pending QRIS transactions can be confirmed.
     */
    public function test_pending_qris_transaction_can_be_confirmed(): void
    {
        $user = User::factory()->cashier()->create();
        $shift = CashierShift::create([
            'user_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => 30000,
            'status' => 'open',
        ]);
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'invoice_number' => 'TRX-20260422-0001',
            'transaction_date' => now(),
            'payment_method' => 'qris',
            'subtotal' => 25000,
            'total_amount' => 25000,
            'paid_amount' => 25000,
            'change_amount' => 0,
            'payment_status' => 'pending',
            'transaction_status' => 'completed',
            'notes' => null,
        ]);

        $response = $this->actingAs($user)->patch(route('transactions.confirm-qris', $transaction));

        $response->assertRedirect(route('transactions.show', $transaction));
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'payment_status' => 'confirmed',
        ]);
    }

    /**
     * Completed transactions can be updated.
     */
    public function test_completed_transaction_can_be_updated(): void
    {
        $user = User::factory()->admin()->create();
        $shift = CashierShift::create([
            'user_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => 150000,
            'status' => 'open',
        ]);
        $category = Category::create([
            'name' => 'Makanan',
            'description' => 'Kategori makanan.',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Burger Mini',
            'price' => 15000,
            'is_active' => true,
            'track_stock' => true,
            'stock_quantity' => 10,
            'stock_alert_threshold' => 2,
        ]);
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'invoice_number' => 'TRX-20260422-0002',
            'transaction_date' => now(),
            'payment_method' => 'cash',
            'subtotal' => 15000,
            'total_amount' => 15000,
            'paid_amount' => 20000,
            'change_amount' => 5000,
            'payment_status' => 'paid',
            'transaction_status' => 'completed',
            'notes' => null,
        ]);
        $transaction->details()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'qty' => 1,
            'price' => 15000,
            'subtotal' => 15000,
        ]);
        $product->update([
            'stock_quantity' => 9,
        ]);

        $response = $this->actingAs($user)->put(route('transactions.update', $transaction), [
            'payment_method' => 'cash',
            'paid_amount' => 50000,
            'notes' => 'Transaksi diupdate.',
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ],
        ]);

        $response->assertRedirect(route('transactions.show', $transaction));
        $transaction->refresh();

        $this->assertEquals(30000, (float) $transaction->total_amount);
        $this->assertEquals(20000, (float) $transaction->change_amount);
        $this->assertSame('Transaksi diupdate.', $transaction->notes);
        $this->assertEquals(2, $transaction->details()->first()->qty);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 8,
        ]);
    }

    /**
     * Completed transactions can be cancelled with a reason.
     */
    public function test_completed_transaction_can_be_cancelled(): void
    {
        $user = User::factory()->admin()->create();
        $shift = CashierShift::create([
            'user_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => 25000,
            'status' => 'open',
        ]);
        $category = Category::create([
            'name' => 'Snack',
            'description' => 'Kategori snack.',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Kentang Goreng',
            'price' => 10000,
            'is_active' => true,
            'track_stock' => true,
            'stock_quantity' => 4,
            'stock_alert_threshold' => 1,
        ]);
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'invoice_number' => 'TRX-20260422-0003',
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
        $transaction->details()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'qty' => 1,
            'price' => 10000,
            'subtotal' => 10000,
        ]);
        $product->update([
            'stock_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->patch(route('transactions.cancel', $transaction), [
            'cancel_reason' => 'Pesanan dibatalkan pelanggan.',
        ]);

        $response->assertRedirect(route('transactions.show', $transaction));
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'transaction_status' => 'cancelled',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 4,
        ]);
    }

    /**
     * Daily report shows minus values when operational net is negative.
     */
    public function test_daily_report_can_show_minus_amount(): void
    {
        $user = User::factory()->admin()->create();

        CashFlow::create([
            'user_id' => $user->id,
            'flow_date' => now(),
            'type' => 'out',
            'amount' => 50000,
            'source' => 'Belanja bahan',
            'description' => 'Operasional.',
        ]);

        $response = $this->actingAs($user)->get(route('reports.daily', [
            'preset' => 'custom',
            'date_from' => now()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertSee('Nilai Minusan');
        $response->assertSee('50.000');
    }

    /**
     * Transactions cannot be created without an active shift.
     */
    public function test_transaction_creation_requires_an_active_shift(): void
    {
        $user = User::factory()->cashier()->create();
        $category = Category::create([
            'name' => 'Minuman',
            'description' => 'Kategori minuman.',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Americano',
            'price' => 12000,
            'is_active' => true,
        ]);

        $response = $this->from(route('transactions.create'))
            ->actingAs($user)
            ->post(route('transactions.store'), [
                'payment_method' => 'cash',
                'paid_amount' => 20000,
                'items' => [
                    ['product_id' => $product->id, 'qty' => 1],
                ],
            ]);

        $response->assertRedirect(route('transactions.create'));
        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('transactions', 0);
    }

    /**
     * Transaction history defaults to today's data and can filter by a chosen date.
     */
    public function test_transaction_history_can_filter_by_transaction_date(): void
    {
        $user = User::factory()->admin()->create();
        $shift = CashierShift::create([
            'user_id' => $user->id,
            'opened_at' => now()->startOfDay(),
            'opening_cash' => 50000,
            'status' => 'open',
        ]);

        $todayTransaction = Transaction::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'invoice_number' => 'TRX-TODAY-0001',
            'transaction_date' => now(),
            'payment_method' => 'cash',
            'subtotal' => 10000,
            'total_amount' => 10000,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'payment_status' => 'paid',
            'transaction_status' => 'completed',
        ]);

        $yesterdayTransaction = Transaction::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'invoice_number' => 'TRX-YESTERDAY-0001',
            'transaction_date' => now()->subDay(),
            'payment_method' => 'cash',
            'subtotal' => 15000,
            'total_amount' => 15000,
            'paid_amount' => 20000,
            'change_amount' => 5000,
            'payment_status' => 'paid',
            'transaction_status' => 'completed',
        ]);

        $todayResponse = $this->actingAs($user)->get(route('transactions.index'));

        $todayResponse->assertOk();
        $todayResponse->assertSee($todayTransaction->invoice_number);
        $todayResponse->assertDontSee($yesterdayTransaction->invoice_number);

        $yesterdayResponse = $this->actingAs($user)->get(route('transactions.index', [
            'transaction_date' => now()->subDay()->toDateString(),
        ]));

        $yesterdayResponse->assertOk();
        $yesterdayResponse->assertSee($yesterdayTransaction->invoice_number);
        $yesterdayResponse->assertDontSee($todayTransaction->invoice_number);
    }
}
