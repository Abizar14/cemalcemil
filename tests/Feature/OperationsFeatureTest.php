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

class OperationsFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cashiers can open and close their shifts with an automatic recap.
     */
    public function test_cashier_can_open_and_close_shift(): void
    {
        $cashier = User::factory()->cashier()->create();

        $openResponse = $this->actingAs($cashier)->post(route('shifts.store'), [
            'opening_cash' => 100000,
            'opening_notes' => 'Modal awal pagi.',
        ]);

        $shift = CashierShift::query()->first();

        $this->assertNotNull($shift);
        $openResponse->assertRedirect();
        $this->assertSame('open', $shift->status);

        Transaction::create([
            'user_id' => $cashier->id,
            'shift_id' => $shift->id,
            'invoice_number' => 'TRX-20260423-0001',
            'transaction_date' => now(),
            'payment_method' => 'cash',
            'subtotal' => 25000,
            'total_amount' => 25000,
            'paid_amount' => 30000,
            'change_amount' => 5000,
            'payment_status' => 'paid',
            'transaction_status' => 'completed',
        ]);

        CashFlow::create([
            'user_id' => $cashier->id,
            'shift_id' => $shift->id,
            'flow_date' => now(),
            'type' => 'out',
            'amount' => 5000,
            'source' => 'Belanja es batu',
            'description' => 'Pengeluaran kecil.',
        ]);

        $closeResponse = $this->actingAs($cashier)->patch(route('shifts.close'), [
            'closing_cash_actual' => 120000,
            'closing_notes' => 'Shift sore selesai.',
        ]);

        $closeResponse->assertRedirect(route('shifts.show', $shift));
        $this->assertDatabaseHas('cashier_shifts', [
            'id' => $shift->id,
            'status' => 'closed',
            'closing_cash_expected' => 120000,
            'closing_cash_actual' => 120000,
            'cash_difference' => 0,
        ]);
    }

    /**
     * Admins can export the current report into PDF.
     */
    public function test_admin_can_export_daily_report_pdf(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('reports.daily.pdf', [
            'preset' => 'custom',
            'date_from' => now()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    /**
     * Transactions have a dedicated printable receipt page.
     */
    public function test_transaction_receipt_print_page_can_be_opened(): void
    {
        $cashier = User::factory()->cashier()->create();
        $shift = CashierShift::create([
            'user_id' => $cashier->id,
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
            'name' => 'Es Kopi Susu',
            'price' => 18000,
            'is_active' => true,
        ]);
        $transaction = Transaction::create([
            'user_id' => $cashier->id,
            'shift_id' => $shift->id,
            'invoice_number' => 'TRX-20260424-0001',
            'transaction_date' => now(),
            'payment_method' => 'cash',
            'subtotal' => 18000,
            'total_amount' => 18000,
            'paid_amount' => 20000,
            'change_amount' => 2000,
            'payment_status' => 'paid',
            'transaction_status' => 'completed',
        ]);
        $transaction->details()->create([
            'product_id' => $product->id,
            'product_name' => 'Es Kopi Susu',
            'qty' => 1,
            'price' => 18000,
            'subtotal' => 18000,
        ]);

        $response = $this->actingAs($cashier)->get(route('transactions.print', $transaction));

        $response->assertOk();
        $response->assertSee($transaction->invoice_number);
        $response->assertSee('Rincian Pembelian');
        $response->assertSee('Print Lagi');
    }

    /**
     * Admins can download a SQLite backup snapshot.
     */
    public function test_admin_can_download_database_backup(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('backups.database'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/x-sqlite3');
        $response->assertHeader('content-disposition');
    }
}
