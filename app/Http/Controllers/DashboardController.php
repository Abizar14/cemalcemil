<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function __invoke(): View
    {
        $today = now();
        $todayDate = $today->toDateString();

        $salesToday = Transaction::query()
            ->whereDate('transaction_date', $todayDate)
            ->where('transaction_status', 'completed')
            ->sum('total_amount');

        $transactionsToday = Transaction::query()
            ->whereDate('transaction_date', $todayDate)
            ->where('transaction_status', 'completed')
            ->count();

        $itemsSoldToday = TransactionDetail::query()
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereDate('transactions.transaction_date', $todayDate)
            ->where('transactions.transaction_status', 'completed')
            ->sum('transaction_details.qty');

        $pendingQris = Transaction::query()
            ->whereDate('transaction_date', $todayDate)
            ->where('transaction_status', 'completed')
            ->where('payment_method', 'qris')
            ->where('payment_status', 'pending')
            ->count();

        $cashInToday = CashFlow::query()
            ->whereDate('flow_date', $todayDate)
            ->where('type', 'in')
            ->sum('amount');

        $cashOutToday = CashFlow::query()
            ->whereDate('flow_date', $todayDate)
            ->where('type', 'out')
            ->sum('amount');

        $recentTransactions = Transaction::query()
            ->with(['user', 'shift'])
            ->withCount('details')
            ->orderByDesc('transaction_date')
            ->limit(5)
            ->get();

        $recentCashFlows = CashFlow::query()
            ->with('user')
            ->orderByDesc('flow_date')
            ->limit(5)
            ->get();

        $topProducts = TransactionDetail::query()
            ->select('product_name')
            ->selectRaw('SUM(qty) as total_qty')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereDate('transactions.transaction_date', $todayDate)
            ->where('transactions.transaction_status', 'completed')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(4)
            ->get();

        $activeShifts = CashierShift::query()
            ->with('user')
            ->where('status', 'open')
            ->orderByDesc('opened_at')
            ->get();

        $lowStockProducts = Product::query()
            ->with('category')
            ->where('track_stock', true)
            ->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
            ->orderBy('stock_quantity')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'today' => $today,
            'stats' => [
                'sales_today' => $salesToday,
                'transactions_today' => $transactionsToday,
                'items_sold_today' => $itemsSoldToday,
                'average_ticket' => $transactionsToday > 0 ? $salesToday / $transactionsToday : 0,
                'pending_qris' => $pendingQris,
                'cash_in_today' => $cashInToday,
                'cash_out_today' => $cashOutToday,
                'net_cash_flow' => $cashInToday - $cashOutToday,
                'active_products' => Product::query()->where('is_active', true)->count(),
                'categories_count' => Category::query()->count(),
            ],
            'recentTransactions' => $recentTransactions,
            'recentCashFlows' => $recentCashFlows,
            'topProducts' => $topProducts,
            'activeShifts' => $activeShifts,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
