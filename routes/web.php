<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\CashierShiftController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->isAdmin()
        ? redirect()->route('dashboard')
        : redirect()->route('transactions.create');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('products', ProductController::class)->except('show');
        Route::resource('cash-flows', CashFlowController::class)->except('show');
        Route::get('reports/daily', DailyReportController::class)->name('reports.daily');
        Route::get('reports/daily/pdf', [DailyReportController::class, 'pdf'])->name('reports.daily.pdf');
        Route::get('backups/database', [BackupController::class, 'download'])->name('backups.database');
        Route::get('shifts', [CashierShiftController::class, 'index'])->name('shifts.index');
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
        Route::patch('transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    });

    Route::middleware('role:admin,kasir')->group(function () {
        Route::post('shifts', [CashierShiftController::class, 'store'])->name('shifts.store');
        Route::patch('shifts/current/close', [CashierShiftController::class, 'close'])->name('shifts.close');
        Route::get('shifts/{shift}', [CashierShiftController::class, 'show'])->name('shifts.show');
        Route::get('transactions/cashier/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('transactions/{transaction}/print', [TransactionController::class, 'print'])->name('transactions.print');
        Route::patch('transactions/{transaction}/confirm-qris', [TransactionController::class, 'confirmQris'])->name('transactions.confirm-qris');
        Route::get('transactions/{transaction}/thermal-print', [TransactionController::class, 'thermalPrint'])->name('transactions.thermal-print');
    });
});
