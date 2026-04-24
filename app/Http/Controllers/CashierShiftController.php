<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierShiftController extends Controller
{
    /**
     * Display cashier shift history.
     */
    public function index(Request $request): View
    {
        $status = (string) $request->string('status');
        $userId = (int) $request->integer('user_id');

        $shifts = CashierShift::query()
            ->with('user')
            ->when(in_array($status, ['open', 'closed'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($userId > 0, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderByDesc('opened_at')
            ->paginate(10)
            ->withQueryString();

        return view('shifts.index', [
            'shifts' => $shifts,
            'status' => $status,
            'userId' => $userId,
            'users' => \App\Models\User::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly opened cashier shift.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->currentShift()->exists()) {
            return back()->withErrors([
                'shift' => 'Masih ada shift yang terbuka. Tutup shift aktif dulu sebelum buka shift baru.',
            ]);
        }

        $validated = $request->validate([
            'opening_cash' => ['required', 'numeric', 'min:0'],
            'opening_notes' => ['nullable', 'string'],
        ]);

        CashierShift::create([
            'user_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => $validated['opening_cash'],
            'opening_notes' => $validated['opening_notes'] ?? null,
            'status' => 'open',
        ]);

        return back()->with('status', 'Shift kasir berhasil dibuka.');
    }

    /**
     * Close the currently active cashier shift.
     */
    public function close(Request $request): RedirectResponse
    {
        $shift = $request->user()?->currentShift()->first();

        if (! $shift) {
            return back()->withErrors([
                'shift' => 'Belum ada shift aktif yang bisa ditutup.',
            ]);
        }

        $summary = $this->buildShiftSummary($shift);

        if (($summary['pending_qris_count'] ?? 0) > 0) {
            return back()->withErrors([
                'shift' => 'Shift belum bisa ditutup karena masih ada transaksi QRIS yang statusnya pending. Konfirmasi atau selesaikan dulu pembayaran QRIS tersebut.',
            ]);
        }

        $validated = $request->validate([
            'closing_cash_actual' => ['required', 'numeric', 'min:0'],
            'closing_notes' => ['nullable', 'string'],
        ]);

        $closingCashActual = (float) $validated['closing_cash_actual'];
        $closingCashExpected = $summary['expected_closing_cash'];

        $shift->update([
            'closed_at' => now(),
            'closing_cash_expected' => $closingCashExpected,
            'closing_cash_actual' => $closingCashActual,
            'cash_difference' => $closingCashActual - $closingCashExpected,
            'closing_notes' => $validated['closing_notes'] ?? null,
            'status' => 'closed',
        ]);

        return redirect()
            ->route('shifts.show', $shift)
            ->with('status', 'Shift kasir berhasil ditutup.');
    }

    /**
     * Display the specified shift.
     */
    public function show(Request $request, CashierShift $shift): View
    {
        $this->authorizeShiftAccess($request, $shift);

        $shift->load([
            'user',
            'transactions.user',
            'transactions.details',
            'cashFlows.user',
        ]);

        return view('shifts.show', [
            'shift' => $shift,
            'summary' => $this->buildShiftSummary($shift),
        ]);
    }

    /**
     * Update closing correction fields for a closed shift.
     */
    public function update(Request $request, CashierShift $shift): RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            abort(403);
        }

        if ($shift->status !== 'closed') {
            return redirect()
                ->route('shifts.show', $shift)
                ->withErrors([
                    'shift' => 'Koreksi shift hanya bisa dilakukan setelah shift ditutup.',
                ]);
        }

        $validated = $request->validate([
            'closing_cash_actual' => ['required', 'numeric', 'min:0'],
            'opening_notes' => ['nullable', 'string'],
            'closing_notes' => ['nullable', 'string'],
        ]);

        $summary = $this->buildShiftSummary($shift);
        $closingCashActual = (float) $validated['closing_cash_actual'];
        $closingCashExpected = $summary['expected_closing_cash'];

        $shift->update([
            'closing_cash_expected' => $closingCashExpected,
            'closing_cash_actual' => $closingCashActual,
            'cash_difference' => $closingCashActual - $closingCashExpected,
            'opening_notes' => $validated['opening_notes'] ?? null,
            'closing_notes' => $validated['closing_notes'] ?? null,
        ]);

        return redirect()
            ->route('shifts.show', $shift)
            ->with('status', 'Koreksi shift berhasil disimpan.');
    }

    /**
     * Build an operational recap for a shift.
     *
     * @return array<string, float|int>
     */
    protected function buildShiftSummary(CashierShift $shift): array
    {
        $shift->loadMissing(['transactions', 'cashFlows']);

        $completedTransactions = $shift->transactions->where('transaction_status', 'completed');
        $cashSales = (float) $completedTransactions->where('payment_method', 'cash')->sum('total_amount');
        $qrisSales = (float) $completedTransactions->where('payment_method', 'qris')->sum('total_amount');
        $pendingQris = (float) $completedTransactions
            ->where('payment_method', 'qris')
            ->where('payment_status', 'pending')
            ->sum('total_amount');
        $pendingQrisCount = $completedTransactions
            ->where('payment_method', 'qris')
            ->where('payment_status', 'pending')
            ->count();
        $cashIn = (float) $shift->cashFlows->where('type', 'in')->sum('amount');
        $cashOut = (float) $shift->cashFlows->where('type', 'out')->sum('amount');
        $totalSales = $cashSales + $qrisSales;
        $expectedClosingCash = (float) $shift->opening_cash + $cashSales + $cashIn - $cashOut;

        return [
            'transactions_count' => $completedTransactions->count(),
            'cancelled_count' => $shift->transactions->where('transaction_status', 'cancelled')->count(),
            'cash_sales' => $cashSales,
            'qris_sales' => $qrisSales,
            'total_sales' => $totalSales,
            'pending_qris_count' => $pendingQrisCount,
            'pending_qris' => $pendingQris,
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'expected_closing_cash' => $expectedClosingCash,
        ];
    }

    /**
     * Restrict cashiers to their own shifts.
     */
    protected function authorizeShiftAccess(Request $request, CashierShift $shift): void
    {
        $user = $request->user();

        if ($user && $user->isKasir() && $shift->user_id !== $user->id) {
            abort(403);
        }
    }
}
