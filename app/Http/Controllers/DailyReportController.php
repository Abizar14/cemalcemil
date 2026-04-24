<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Models\CashierShift;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DailyReportController extends Controller
{
    /**
     * Display daily reports with date filters and recap.
     */
    public function __invoke(Request $request): View
    {
        return view('reports.daily', $this->buildReportPayload($request));
    }

    /**
     * Export the current report filters into a PDF file.
     */
    public function pdf(Request $request): Response|BinaryFileResponse
    {
        $payload = $this->buildReportPayload($request);
        $filename = 'laporan-booth-'.$payload['dateFrom']->format('Ymd').'-'.$payload['dateTo']->format('Ymd').'.pdf';

        return Pdf::loadView('reports.daily-pdf', array_merge($payload, [
            'booth' => config('booth'),
        ]))
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    /**
     * Build the report payload for HTML and PDF output.
     *
     * @return array<string, mixed>
     */
    protected function buildReportPayload(Request $request): array
    {
        [$dateFrom, $dateTo, $preset] = $this->resolveDateRange($request);
        $shiftId = (int) $request->integer('shift_id');
        $userId = (int) $request->integer('user_id');

        $transactionRows = Transaction::query()
            ->selectRaw('DATE(transaction_date) as report_date')
            ->selectRaw("SUM(CASE WHEN transaction_status = 'completed' THEN total_amount ELSE 0 END) as gross_sales")
            ->selectRaw("SUM(CASE WHEN transaction_status = 'completed' AND (payment_method = 'cash' OR payment_status IN ('paid', 'confirmed')) THEN total_amount ELSE 0 END) as realized_sales")
            ->selectRaw("SUM(CASE WHEN transaction_status = 'completed' AND payment_method = 'qris' AND payment_status = 'pending' THEN total_amount ELSE 0 END) as pending_qris_total")
            ->selectRaw("SUM(CASE WHEN transaction_status = 'cancelled' THEN total_amount ELSE 0 END) as cancelled_total")
            ->selectRaw("SUM(CASE WHEN transaction_status = 'completed' THEN 1 ELSE 0 END) as completed_count")
            ->selectRaw("SUM(CASE WHEN transaction_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count")
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->when($shiftId > 0, fn ($query) => $query->where('shift_id', $shiftId))
            ->when($userId > 0, fn ($query) => $query->where('user_id', $userId))
            ->groupBy('report_date')
            ->get()
            ->keyBy('report_date');

        $cashFlowRows = CashFlow::query()
            ->selectRaw('DATE(flow_date) as report_date')
            ->selectRaw("SUM(CASE WHEN type = 'in' THEN amount ELSE 0 END) as cash_in")
            ->selectRaw("SUM(CASE WHEN type = 'out' THEN amount ELSE 0 END) as cash_out")
            ->whereBetween('flow_date', [$dateFrom, $dateTo])
            ->when($shiftId > 0, fn ($query) => $query->where('shift_id', $shiftId))
            ->when($userId > 0, fn ($query) => $query->where('user_id', $userId))
            ->groupBy('report_date')
            ->get()
            ->keyBy('report_date');

        $rows = collect();

        foreach (CarbonPeriod::create($dateFrom->copy()->startOfDay(), $dateTo->copy()->startOfDay()) as $date) {
            $dateKey = $date->format('Y-m-d');
            $transactionRow = $transactionRows->get($dateKey);
            $cashFlowRow = $cashFlowRows->get($dateKey);

            $grossSales = (float) ($transactionRow->gross_sales ?? 0);
            $realizedSales = (float) ($transactionRow->realized_sales ?? 0);
            $pendingQrisTotal = (float) ($transactionRow->pending_qris_total ?? 0);
            $cancelledTotal = (float) ($transactionRow->cancelled_total ?? 0);
            $cashIn = (float) ($cashFlowRow->cash_in ?? 0);
            $cashOut = (float) ($cashFlowRow->cash_out ?? 0);
            $netAmount = $realizedSales + $cashIn - $cashOut;

            $rows->push([
                'date' => $date->copy(),
                'gross_sales' => $grossSales,
                'realized_sales' => $realizedSales,
                'pending_qris_total' => $pendingQrisTotal,
                'cancelled_total' => $cancelledTotal,
                'completed_count' => (int) ($transactionRow->completed_count ?? 0),
                'cancelled_count' => (int) ($transactionRow->cancelled_count ?? 0),
                'cash_in' => $cashIn,
                'cash_out' => $cashOut,
                'net_amount' => $netAmount,
                'minus_amount' => $netAmount < 0 ? abs($netAmount) : 0,
            ]);
        }

        $summary = [
            'gross_sales' => $rows->sum('gross_sales'),
            'realized_sales' => $rows->sum('realized_sales'),
            'pending_qris_total' => $rows->sum('pending_qris_total'),
            'cancelled_total' => $rows->sum('cancelled_total'),
            'completed_count' => $rows->sum('completed_count'),
            'cancelled_count' => $rows->sum('cancelled_count'),
            'cash_in' => $rows->sum('cash_in'),
            'cash_out' => $rows->sum('cash_out'),
            'net_amount' => $rows->sum('net_amount'),
            'minus_amount' => $rows->sum('minus_amount'),
        ];

        $topProducts = TransactionDetail::query()
            ->select('product_name')
            ->selectRaw('SUM(transaction_details.qty) as total_qty')
            ->selectRaw('SUM(transaction_details.subtotal) as total_sales')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereBetween('transactions.transaction_date', [$dateFrom, $dateTo])
            ->where('transactions.transaction_status', 'completed')
            ->when($shiftId > 0, fn ($query) => $query->where('transactions.shift_id', $shiftId))
            ->when($userId > 0, fn ($query) => $query->where('transactions.user_id', $userId))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $shiftRows = $this->buildShiftRows($dateFrom, $dateTo, $shiftId, $userId);

        return [
            'booth' => config('booth'),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'preset' => $preset,
            'rows' => $rows,
            'summary' => $summary,
            'topProducts' => $topProducts,
            'shiftRows' => $shiftRows,
            'shiftId' => $shiftId,
            'userId' => $userId,
            'users' => User::query()->orderBy('name')->get(),
            'shifts' => CashierShift::query()->with('user')->orderByDesc('opened_at')->limit(50)->get(),
        ];
    }

    /**
     * Resolve report dates using preset or direct input.
     *
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    protected function resolveDateRange(Request $request): array
    {
        $preset = (string) $request->string('preset', 'today');

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $preset = 'custom';
        }

        return match ($preset) {
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay(), $preset],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek(), $preset],
            'this_month' => [now()->startOfMonth(), now()->endOfMonth(), $preset],
            'last_7_days' => [now()->subDays(6)->startOfDay(), now()->endOfDay(), $preset],
            'custom' => $this->resolveCustomDateRange($request),
            default => [now()->startOfDay(), now()->endOfDay(), 'today'],
        };
    }

    /**
     * Resolve a custom date range.
     *
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    protected function resolveCustomDateRange(Request $request): array
    {
        $dateFrom = $request->date('date_from')
            ? Carbon::parse((string) $request->input('date_from'))->startOfDay()
            : now()->startOfDay();
        $dateTo = $request->date('date_to')
            ? Carbon::parse((string) $request->input('date_to'))->endOfDay()
            : now()->endOfDay();

        if ($dateFrom->gt($dateTo)) {
            [$dateFrom, $dateTo] = [$dateTo->copy()->startOfDay(), $dateFrom->copy()->endOfDay()];
        }

        return [$dateFrom, $dateTo, 'custom'];
    }

    /**
     * Build shift recap rows for the report period.
     */
    protected function buildShiftRows(Carbon $dateFrom, Carbon $dateTo, int $shiftId, int $userId)
    {
        return CashierShift::query()
            ->with([
                'user',
                'transactions' => fn ($query) => $query->whereBetween('transaction_date', [$dateFrom, $dateTo]),
                'cashFlows' => fn ($query) => $query->whereBetween('flow_date', [$dateFrom, $dateTo]),
            ])
            ->when($shiftId > 0, fn ($query) => $query->where('id', $shiftId))
            ->when($userId > 0, fn ($query) => $query->where('user_id', $userId))
            ->where(function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('opened_at', [$dateFrom, $dateTo])
                    ->orWhereBetween('closed_at', [$dateFrom, $dateTo])
                    ->orWhere(function ($nested) use ($dateFrom, $dateTo) {
                        $nested->where('opened_at', '<=', $dateTo)
                            ->where(function ($open) use ($dateFrom) {
                                $open->whereNull('closed_at')
                                    ->orWhere('closed_at', '>=', $dateFrom);
                            });
                    });
            })
            ->orderByDesc('opened_at')
            ->get()
            ->map(function (CashierShift $shift) {
                $completedTransactions = $shift->transactions->where('transaction_status', 'completed');
                $cashSales = (float) $completedTransactions->where('payment_method', 'cash')->sum('total_amount');
                $qrisSales = (float) $completedTransactions->where('payment_method', 'qris')->sum('total_amount');
                $cashIn = (float) $shift->cashFlows->where('type', 'in')->sum('amount');
                $cashOut = (float) $shift->cashFlows->where('type', 'out')->sum('amount');

                return [
                    'shift' => $shift,
                    'cash_sales' => $cashSales,
                    'qris_sales' => $qrisSales,
                    'cash_in' => $cashIn,
                    'cash_out' => $cashOut,
                    'expected_closing_cash' => (float) $shift->opening_cash + $cashSales + $cashIn - $cashOut,
                    'transactions_count' => $completedTransactions->count(),
                ];
            });
    }
}
