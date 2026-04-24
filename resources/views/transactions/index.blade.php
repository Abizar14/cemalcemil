@php
    $title = 'Transaksi Booth';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Transaksi')
@section('panel-title', 'Daftar transaksi kasir')
@section('panel-description', 'Pantau transaksi terbaru, status pembayaran, status transaksi, dan buka kasir baru untuk proses penjualan berikutnya.')

@section('panel-actions')
    <a
        href="{{ route('transactions.create') }}"
        class="font-display rounded-[1.5rem] bg-orange-500 px-5 py-4 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
    >
        Buka Kasir
    </a>
@endsection

@section('panel-content')
    @if ($errors->has('status'))
        <div class="animate-rise rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            {{ $errors->first('status') }}
        </div>
    @endif

    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
        <form method="GET" action="{{ route('transactions.index') }}" class="grid gap-4 xl:grid-cols-[1.05fr_0.78fr_0.78fr_0.9fr_0.9fr_auto]">
            <div>
                <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Cari invoice</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Contoh: TRX-20260422-0001"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
            </div>

            <div>
                <label for="payment_method" class="mb-2 block text-sm font-medium text-slate-700">Metode</label>
                <select
                    id="payment_method"
                    name="payment_method"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua metode</option>
                    <option value="cash" @selected($paymentMethod === 'cash')>Cash</option>
                    <option value="qris" @selected($paymentMethod === 'qris')>QRIS</option>
                </select>
            </div>

            <div>
                <label for="payment_status" class="mb-2 block text-sm font-medium text-slate-700">Status bayar</label>
                <select
                    id="payment_status"
                    name="payment_status"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua status</option>
                    <option value="paid" @selected($paymentStatus === 'paid')>Paid</option>
                    <option value="pending" @selected($paymentStatus === 'pending')>Pending</option>
                    <option value="confirmed" @selected($paymentStatus === 'confirmed')>Confirmed</option>
                </select>
            </div>

            <div>
                <label for="transaction_status" class="mb-2 block text-sm font-medium text-slate-700">Status transaksi</label>
                <select
                    id="transaction_status"
                    name="transaction_status"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua transaksi</option>
                    <option value="completed" @selected($transactionStatus === 'completed')>Completed</option>
                    <option value="cancelled" @selected($transactionStatus === 'cancelled')>Cancelled</option>
                </select>
            </div>

            <div class="space-y-3">
                <div>
                    <label for="transaction_date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal transaksi</label>
                    <input
                        id="transaction_date"
                        type="date"
                        name="transaction_date"
                        value="{{ $allDates ? '' : $transactionDate }}"
                        class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                        {{ $allDates ? 'disabled' : '' }}
                    >
                </div>

                <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-sm font-medium text-slate-700">
                    <input
                        type="checkbox"
                        name="all_dates"
                        value="1"
                        @checked($allDates)
                        class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-400"
                    >
                    Semua tanggal
                </label>
            </div>

            <div class="flex items-end gap-3">
                <button
                    type="submit"
                    class="rounded-2xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-orange-600"
                >
                    Filter
                </button>
                <a
                    href="{{ route('transactions.index') }}"
                    class="rounded-2xl border border-slate-200 bg-white px-5 py-3.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.06s;">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Riwayat Transaksi</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Penjualan terbaru booth</h2>
                <p class="mt-2 text-sm text-slate-500">
                    {{ $allDates ? 'Menampilkan semua tanggal transaksi.' : 'Menampilkan transaksi pada ' . \Carbon\Carbon::parse($transactionDate)->translatedFormat('d M Y') . '.' }}
                </p>
            </div>
            <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">
                {{ $transactions->total() }} transaksi
            </div>
        </div>

        <div class="mt-5 grid gap-4">
            @forelse ($transactions as $transaction)
                <article class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="font-display text-xl font-semibold text-slate-900">{{ $transaction->invoice_number }}</h3>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-600">
                                    {{ $transaction->payment_method }}
                                </span>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $transaction->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($transaction->payment_status === 'confirmed' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $transaction->payment_status }}
                                </span>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $transaction->transaction_status === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-slate-900 text-white' }}">
                                    {{ $transaction->transaction_status }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm text-slate-500">
                                {{ $transaction->transaction_date->format('d M Y, H:i') }} - {{ $transaction->user->name }} - {{ $transaction->details_count }} item
                            </p>
                            @if ($transaction->shift)
                                <p class="mt-2 text-sm text-slate-500">
                                    Shift: {{ $transaction->shift->opened_at->format('d M Y, H:i') }}
                                </p>
                            @endif
                            <p class="font-display mt-3 text-2xl font-semibold text-orange-600">
                                Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </p>
                            @if ($transaction->isCancelled())
                                <p class="mt-2 text-sm text-rose-600">
                                    Dibatalkan{{ $transaction->cancelled_at ? ' pada ' . $transaction->cancelled_at->format('d M Y, H:i') : '' }}.
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a
                                href="{{ route('transactions.show', $transaction) }}"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                            >
                                Lihat Struk
                            </a>
                            <a
                                href="{{ route('transactions.thermal-print', $transaction) }}"
                                class="rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-semibold text-orange-700 transition hover:bg-orange-100"
                            >
                                Thermal
                            </a>
                            @if (! $transaction->isCancelled())
                                <a
                                    href="{{ route('transactions.edit', $transaction) }}"
                                    class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                >
                                    Edit
                                </a>
                            @endif

                            @if (! $transaction->isCancelled() && $transaction->payment_method === 'qris' && $transaction->payment_status === 'pending')
                                <form method="POST" action="{{ route('transactions.confirm-qris', $transaction) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100"
                                    >
                                        Konfirmasi QRIS
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-12 text-center text-sm text-slate-500">
                    Belum ada transaksi yang tercatat.
                </div>
            @endforelse
        </div>

        @if ($transactions->hasPages())
            <div class="mt-5">
                {{ $transactions->links() }}
            </div>
        @endif
    </section>
@endsection
