<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CashierShift;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $paymentMethod = (string) $request->string('payment_method');
        $paymentStatus = (string) $request->string('payment_status');
        $transactionStatus = (string) $request->string('transaction_status');
        $allDates = $request->boolean('all_dates');
        $transactionDate = $allDates
            ? ''
            : $this->normalizeTransactionDateFilter((string) $request->string('transaction_date'));

        $transactions = Transaction::query()
            ->with(['user', 'shift'])
            ->withCount('details')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('invoice_number', 'like', "%{$search}%");
            })
            ->when(in_array($paymentMethod, ['cash', 'qris'], true), function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })
            ->when(in_array($paymentStatus, ['paid', 'pending', 'confirmed'], true), function ($query) use ($paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
            ->when(in_array($transactionStatus, ['completed', 'cancelled'], true), function ($query) use ($transactionStatus) {
                $query->where('transaction_status', $transactionStatus);
            })
            ->when(! $allDates && $transactionDate !== '', function ($query) use ($transactionDate) {
                $query->whereDate('transaction_date', $transactionDate);
            })
            ->orderByDesc('transaction_date')
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', [
            'transactions' => $transactions,
            'search' => $search,
            'paymentMethod' => $paymentMethod,
            'paymentStatus' => $paymentStatus,
            'transactionStatus' => $transactionStatus,
            'transactionDate' => $transactionDate,
            'allDates' => $allDates,
        ]);
    }

    /**
     * Show the cashier screen.
     */
    public function create(): View
    {
        return view('transactions.create', $this->cashierViewData());
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $shift = $this->getCurrentShiftOrFail($request);
        $prepared = $this->prepareTransactionPayload($request);

        $transaction = DB::transaction(function () use ($request, $prepared, $shift) {
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'shift_id' => $shift->id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'transaction_date' => now(),
                'payment_method' => $prepared['payment_method'],
                'subtotal' => $prepared['subtotal'],
                'total_amount' => $prepared['total_amount'],
                'paid_amount' => $prepared['paid_amount'],
                'change_amount' => $prepared['change_amount'],
                'payment_status' => $prepared['payment_status'],
                'transaction_status' => 'completed',
                'notes' => $prepared['notes'],
            ]);

            $transaction->details()->createMany($prepared['details']);
            $this->deductProductStock($prepared['details']);

            return $transaction;
        });

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('status', 'Transaksi berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction): View|RedirectResponse
    {
        if ($transaction->isCancelled()) {
            return redirect()
                ->route('transactions.show', $transaction)
                ->withErrors([
                    'status' => 'Transaksi yang sudah dibatalkan tidak bisa diedit.',
                ]);
        }

        $transaction->load('details');

        return view('transactions.edit', array_merge(
            $this->cashierViewData(),
            ['transaction' => $transaction],
        ));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->isCancelled()) {
            return redirect()
                ->route('transactions.show', $transaction)
                ->withErrors([
                    'status' => 'Transaksi yang sudah dibatalkan tidak bisa diperbarui.',
                ]);
        }

        DB::transaction(function () use ($request, $transaction) {
            $transaction->loadMissing('details');
            $this->restoreProductStock($transaction->details->all());
            $prepared = $this->prepareTransactionPayload($request);

            $transaction->update([
                'payment_method' => $prepared['payment_method'],
                'subtotal' => $prepared['subtotal'],
                'total_amount' => $prepared['total_amount'],
                'paid_amount' => $prepared['paid_amount'],
                'change_amount' => $prepared['change_amount'],
                'payment_status' => $prepared['payment_status'],
                'notes' => $prepared['notes'],
                'cancelled_at' => null,
                'cancel_reason' => null,
            ]);

            $transaction->details()->delete();
            $transaction->details()->createMany($prepared['details']);
            $this->deductProductStock($prepared['details']);
        });

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('status', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction): View
    {
        $this->authorizeKasirTransactionAccess($transaction);
        $transaction->load(['user', 'details.product', 'shift']);

        return view('transactions.show', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Display a printable receipt version.
     */
    public function print(Transaction $transaction): View
    {
        $this->authorizeKasirTransactionAccess($transaction);
        $transaction->load(['user', 'details', 'shift']);

        return view('transactions.receipt-print', [
            'transaction' => $transaction,
            'booth' => config('booth'),
        ]);
    }

    /**
     * Cancel a completed transaction.
     */
    public function cancel(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->isCancelled()) {
            return redirect()
                ->route('transactions.show', $transaction)
                ->withErrors([
                    'status' => 'Transaksi ini sudah dibatalkan sebelumnya.',
                ]);
        }

        $validated = $request->validate([
            'cancel_reason' => ['required', 'string', 'min:5'],
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            $transaction->loadMissing('details');
            $transaction->update([
                'transaction_status' => 'cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $validated['cancel_reason'],
            ]);
            $this->restoreProductStock($transaction->details->all());
        });

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('status', 'Transaksi berhasil dibatalkan.');
    }

    /**
     * Confirm a pending QRIS transaction.
     */
    public function confirmQris(Transaction $transaction): RedirectResponse
    {
        $this->authorizeKasirTransactionAccess($transaction);

        if ($transaction->isCancelled()) {
            return redirect()
                ->route('transactions.show', $transaction)
                ->withErrors([
                    'status' => 'Transaksi yang dibatalkan tidak bisa dikonfirmasi.',
                ]);
        }

        if ($transaction->payment_method !== 'qris' || $transaction->payment_status !== 'pending') {
            return redirect()
                ->route('transactions.show', $transaction)
                ->withErrors([
                    'status' => 'Transaksi ini tidak bisa dikonfirmasi sebagai QRIS pending.',
                ]);
        }

        $transaction->update([
            'payment_status' => 'confirmed',
            'paid_amount' => $transaction->total_amount,
            'change_amount' => 0,
        ]);

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('status', 'Pembayaran QRIS berhasil dikonfirmasi.');
    }

    /**
     * Display a thermal receipt friendly version.
     */
    public function thermalPrint(Transaction $transaction): View
    {
        $this->authorizeKasirTransactionAccess($transaction);
        $transaction->load(['user', 'details', 'shift']);

        return view('transactions.thermal-print', [
            'transaction' => $transaction,
            'booth' => config('booth'),
        ]);
    }

    /**
     * Prepare transaction payload from request data.
     *
     * @return array<string, mixed>
     */
    protected function prepareTransactionPayload(Request $request): array
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:cash,qris'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $products = Product::query()
            ->whereIn('id', collect($validated['items'])->pluck('product_id')->all())
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $groupedItems = collect($validated['items'])
            ->groupBy('product_id')
            ->map(fn ($rows) => [
                'product_id' => (int) $rows[0]['product_id'],
                'qty' => (int) $rows->sum('qty'),
            ])
            ->values();

        $details = [];
        $subtotal = 0;

        foreach ($groupedItems as $item) {
            $product = $products->get($item['product_id']);

            if (! $product) {
                throw ValidationException::withMessages([
                    'items' => 'Ada produk yang tidak aktif atau tidak ditemukan.',
                ]);
            }

            $lineSubtotal = (float) $product->price * $item['qty'];
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
            throw ValidationException::withMessages([
                'items' => 'Pilih minimal satu produk untuk transaksi.',
            ]);
        }

        $paymentMethod = $validated['payment_method'];
        $paidAmount = $paymentMethod === 'cash'
            ? (float) ($validated['paid_amount'] ?? 0)
            : $subtotal;

        if ($paymentMethod === 'cash' && $paidAmount < $subtotal) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Nominal bayar tidak boleh kurang dari total transaksi.',
            ]);
        }

        return [
            'payment_method' => $paymentMethod,
            'subtotal' => $subtotal,
            'total_amount' => $subtotal,
            'paid_amount' => $paidAmount,
            'change_amount' => $paymentMethod === 'cash' ? max($paidAmount - $subtotal, 0) : 0,
            'payment_status' => $paymentMethod === 'cash' ? 'paid' : 'pending',
            'notes' => $validated['notes'] ?? null,
            'details' => $details,
        ];
    }

    /**
     * Get shared data for cashier views.
     *
     * @return array<string, mixed>
     */
    protected function cashierViewData(): array
    {
        $currentShift = auth()->user()?->currentShift()->first();

        return [
            'products' => Product::query()
                ->with('category')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'categories' => Category::query()->orderBy('name')->get(),
            'currentShift' => $currentShift,
            'currentShiftSummary' => $currentShift ? $this->buildShiftSummary($currentShift) : null,
            'quickCashAmounts' => [5000, 10000, 20000, 50000, 100000],
        ];
    }

    /**
     * Generate a unique invoice number for the current date.
     */
    protected function generateInvoiceNumber(): string
    {
        $datePart = now()->format('Ymd');
        $prefix = "TRX-{$datePart}-";
        $latestInvoice = Transaction::query()
            ->where('invoice_number', 'like', "{$prefix}%")
            ->latest('id')
            ->value('invoice_number');

        $lastNumber = 0;

        if (is_string($latestInvoice)) {
            $lastNumber = (int) substr($latestInvoice, -4);
        }

        do {
            $lastNumber++;
            $invoiceNumber = $prefix.str_pad((string) $lastNumber, 4, '0', STR_PAD_LEFT);
        } while (Transaction::query()->where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    /**
     * Restrict cashiers to their own transactions.
     */
    protected function authorizeKasirTransactionAccess(Transaction $transaction): void
    {
        $user = auth()->user();

        if ($user && $user->isKasir() && $transaction->user_id !== $user->id) {
            abort(403);
        }
    }

    /**
     * Get the current active shift or fail with a validation error.
     */
    protected function getCurrentShiftOrFail(Request $request): CashierShift
    {
        $shift = $request->user()?->currentShift()->first();

        if (! $shift) {
            throw ValidationException::withMessages([
                'items' => 'Buka shift kasir dulu sebelum membuat transaksi baru.',
            ]);
        }

        return $shift;
    }

    /**
     * Deduct product stock for tracked items.
     *
     * @param  array<int, array<string, mixed>>  $details
     */
    protected function deductProductStock(array $details): void
    {
        $products = Product::query()
            ->whereIn('id', collect($details)->pluck('product_id')->all())
            ->get()
            ->keyBy('id');

        foreach ($details as $detail) {
            $product = $products->get($detail['product_id']);

            if (! $product || ! $product->track_stock) {
                continue;
            }

            $newStock = (int) ($product->stock_quantity ?? 0) - (int) $detail['qty'];

            if ($newStock < 0) {
                throw ValidationException::withMessages([
                    'items' => 'Stok produk '.$product->name.' tidak mencukupi untuk transaksi ini.',
                ]);
            }

            $product->update([
                'stock_quantity' => $newStock,
                'is_active' => $newStock > 0,
            ]);
        }
    }

    /**
     * Restore product stock from transaction details.
     *
     * @param  iterable<int, mixed>  $details
     */
    protected function restoreProductStock(iterable $details): void
    {
        $details = collect($details);
        $products = Product::query()
            ->whereIn('id', $details->pluck('product_id')->all())
            ->get()
            ->keyBy('id');

        foreach ($details as $detail) {
            $product = $products->get($detail->product_id ?? $detail['product_id']);

            if (! $product || ! $product->track_stock) {
                continue;
            }

            $qty = (int) ($detail->qty ?? $detail['qty']);
            $newStock = (int) ($product->stock_quantity ?? 0) + $qty;

            $product->update([
                'stock_quantity' => $newStock,
                'is_active' => $newStock > 0,
            ]);
        }
    }

    /**
     * Build a current shift recap for the cashier screen.
     *
     * @return array<string, float|int>
     */
    protected function buildShiftSummary(CashierShift $shift): array
    {
        $shift->loadMissing(['transactions', 'cashFlows']);

        $completedTransactions = $shift->transactions->where('transaction_status', 'completed');
        $cashSales = (float) $completedTransactions->where('payment_method', 'cash')->sum('total_amount');
        $qrisSales = (float) $completedTransactions->where('payment_method', 'qris')->sum('total_amount');
        $cashIn = (float) $shift->cashFlows->where('type', 'in')->sum('amount');
        $cashOut = (float) $shift->cashFlows->where('type', 'out')->sum('amount');
        $totalSales = $cashSales + $qrisSales;

        return [
            'transactions_count' => $completedTransactions->count(),
            'cash_sales' => $cashSales,
            'qris_sales' => $qrisSales,
            'total_sales' => $totalSales,
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'expected_closing_cash' => (float) $shift->opening_cash + $cashSales + $cashIn - $cashOut,
        ];
    }

    /**
     * Normalize the selected transaction date, defaulting to today.
     */
    protected function normalizeTransactionDateFilter(string $date): string
    {
        if ($date === '') {
            return now()->toDateString();
        }

        $parsed = \DateTime::createFromFormat('Y-m-d', $date);

        if (! $parsed || $parsed->format('Y-m-d') !== $date) {
            return now()->toDateString();
        }

        return $date;
    }
}
