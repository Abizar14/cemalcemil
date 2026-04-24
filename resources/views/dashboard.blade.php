@php
    $title = 'Dashboard Kasir Booth';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Operasional ' . $today->format('d M Y'))
@section('panel-title', 'Selamat datang, ' . auth()->user()->name . '.')
@section('panel-description', 'Ringkasan hari ini memperlihatkan performa penjualan, status QRIS, arus kas, dan aktivitas terakhir di booth tanpa perlu pindah-pindah halaman.')

@section('panel-actions')
    <div class="rounded-[1.5rem] border border-orange-100 bg-white/80 px-5 py-4 text-right">
        <p class="text-xs font-semibold tracking-[0.2em] text-slate-500 uppercase">Penjualan Hari Ini</p>
        <p class="font-display mt-2 text-2xl font-semibold text-slate-900">
            Rp{{ number_format($stats['sales_today'], 0, ',', '.') }}
        </p>
    </div>
@endsection

@section('panel-content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5" style="animation-delay: 0.05s;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Transaksi Hari Ini</p>
                    <p class="font-display mt-3 text-3xl font-semibold text-slate-900">{{ $stats['transactions_today'] }}</p>
                </div>
                <div class="rounded-2xl bg-orange-500 px-3 py-2 text-sm font-semibold text-white">Live</div>
            </div>
            <p class="mt-4 text-sm leading-6 text-slate-600">
                Total item terjual {{ $stats['items_sold_today'] }} dan rata-rata belanja
                Rp{{ number_format($stats['average_ticket'], 0, ',', '.') }}.
            </p>
        </article>

        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5" style="animation-delay: 0.1s;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Status QRIS</p>
                    <p class="font-display mt-3 text-3xl font-semibold text-slate-900">{{ $stats['pending_qris'] }}</p>
                </div>
                <div class="rounded-2xl bg-emerald-500/15 px-3 py-2 text-sm font-semibold text-emerald-700">Pending</div>
            </div>
            <p class="mt-4 text-sm leading-6 text-slate-600">
                Jumlah transaksi QRIS yang masih menunggu konfirmasi kasir hari ini.
            </p>
        </article>

        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5" style="animation-delay: 0.15s;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Kas Hari Ini</p>
                    <p class="font-display mt-3 text-3xl font-semibold text-slate-900">
                        Rp{{ number_format($stats['net_cash_flow'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="rounded-2xl bg-amber-500/15 px-3 py-2 text-sm font-semibold text-amber-700">Net Flow</div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-2xl bg-white/75 px-4 py-3">
                    <p class="text-slate-500">Masuk</p>
                    <p class="mt-1 font-semibold text-emerald-700">Rp{{ number_format($stats['cash_in_today'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl bg-white/75 px-4 py-3">
                    <p class="text-slate-500">Keluar</p>
                    <p class="mt-1 font-semibold text-rose-600">Rp{{ number_format($stats['cash_out_today'], 0, ',', '.') }}</p>
                </div>
            </div>
        </article>

        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5" style="animation-delay: 0.2s;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Produk & Kategori</p>
                    <p class="font-display mt-3 text-3xl font-semibold text-slate-900">{{ $stats['active_products'] }}</p>
                </div>
                <div class="rounded-2xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Aktif</div>
            </div>
            <p class="mt-4 text-sm leading-6 text-slate-600">
                Produk aktif saat ini tersebar dalam {{ $stats['categories_count'] }} kategori penjualan.
            </p>
        </article>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.22s;">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Shift Aktif</p>
                    <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Kasir yang sedang jaga</h2>
                </div>
                <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">
                    {{ $activeShifts->count() }} shift
                </div>
            </div>

            <div class="mt-5 grid gap-4">
                @forelse ($activeShifts as $shift)
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-display text-lg font-semibold text-slate-900">{{ $shift->user->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">Buka shift {{ $shift->opened_at->format('d M Y, H:i') }}</p>
                            </div>
                            <a href="{{ route('shifts.show', $shift) }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-10 text-center text-sm text-slate-500">
                        Belum ada shift aktif saat ini.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.24s;">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Stok Menipis</p>
                    <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Produk yang perlu dicek</h2>
                </div>
                <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">
                    {{ $lowStockProducts->count() }} produk
                </div>
            </div>

            <div class="mt-5 grid gap-4">
                @forelse ($lowStockProducts as $product)
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-display text-lg font-semibold text-slate-900">{{ $product->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $product->category->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->isOutOfStock() ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $product->isOutOfStock() ? 'Stok Habis' : 'Stok Ada' }}
                                </p>
                                <p class="mt-2 text-xs text-slate-500">Perlu dicek ulang dari stok internal.</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-10 text-center text-sm text-slate-500">
                        Belum ada produk yang masuk kategori stok menipis.
                    </div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
        <div class="space-y-6">
            <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.26s;">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Transaksi Terbaru</p>
                        <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Aktivitas penjualan booth</h2>
                    </div>
                    <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">
                        {{ $recentTransactions->count() }} transaksi terbaru
                    </div>
                </div>

                <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200/70 bg-white/85">
                    <div class="hidden grid-cols-[1.2fr_0.7fr_0.7fr_0.6fr_0.8fr] gap-4 border-b border-slate-200 px-5 py-4 text-xs font-semibold tracking-[0.16em] text-slate-500 uppercase md:grid">
                        <span>Invoice</span>
                        <span>Metode</span>
                        <span>Status</span>
                        <span>Item</span>
                        <span>Total</span>
                    </div>

                    <div class="divide-y divide-slate-200/80">
                        @forelse ($recentTransactions as $transaction)
                            <div class="grid gap-3 px-5 py-4 md:grid-cols-[1.2fr_0.7fr_0.7fr_0.6fr_0.8fr] md:items-center">
                                <div>
                                    <p class="font-display text-base font-semibold text-slate-900">
                                        {{ $transaction->invoice_number }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $transaction->transaction_date->format('d M Y, H:i') }} - {{ $transaction->user->name }}
                                    </p>
                                </div>
                                <div class="text-sm font-medium text-slate-700 uppercase">
                                    {{ $transaction->payment_method }}
                                </div>
                                <div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $transaction->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($transaction->payment_status === 'confirmed' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ $transaction->payment_status }}
                                    </span>
                                </div>
                                <div class="text-sm text-slate-600">
                                    {{ $transaction->details_count }} item
                                </div>
                                <div class="font-semibold text-slate-900">
                                    Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-10 text-center text-sm text-slate-500">
                                Belum ada transaksi yang tercatat.
                            </div>
                        @endforelse
                    </div>
                </div>
            </article>

            <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.32s;">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Arus Kas Terbaru</p>
                        <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Kas masuk dan keluar</h2>
                    </div>
                    <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">
                        Update operasional terbaru
                    </div>
                </div>

                <div class="mt-5 grid gap-4">
                    @forelse ($recentCashFlows as $cashFlow)
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $cashFlow->type === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $cashFlow->type === 'in' ? 'Kas Masuk' : 'Kas Keluar' }}
                                        </span>
                                        <span class="text-sm text-slate-500">{{ $cashFlow->flow_date->format('d M Y, H:i') }}</span>
                                    </div>
                                    <p class="font-display mt-3 text-lg font-semibold text-slate-900">{{ $cashFlow->source ?? 'Operasional Booth' }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $cashFlow->description ?: 'Tidak ada keterangan tambahan.' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-slate-500">{{ $cashFlow->user->name }}</p>
                                    <p class="font-display mt-2 text-xl font-semibold {{ $cashFlow->type === 'in' ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $cashFlow->type === 'in' ? '+' : '-' }}Rp{{ number_format($cashFlow->amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada arus kas yang tercatat.
                        </div>
                    @endforelse
                </div>
            </article>
        </div>

        <aside class="space-y-6">
            <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.36s;">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Produk Terlaris Hari Ini</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Top seller booth</h2>

                <div class="mt-5 space-y-3">
                    @forelse ($topProducts as $index => $product)
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-sm font-semibold text-white">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <p class="font-display text-base font-semibold text-slate-900">{{ $product->product_name }}</p>
                                        <p class="text-sm text-slate-500">Terjual {{ $product->total_qty }} item</p>
                                    </div>
                                </div>
                                <div class="h-2 w-20 overflow-hidden rounded-full bg-orange-100">
                                    <div class="h-full rounded-full bg-orange-500" style="width: {{ min((int) $product->total_qty * 20, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada data penjualan hari ini.
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.42s;">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Status Booth</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Ringkasan singkat</h2>

                <div class="mt-5 space-y-3">
                    <div class="rounded-[1.5rem] bg-slate-900 p-4 text-slate-50">
                        <p class="text-xs font-semibold tracking-[0.18em] text-slate-300 uppercase">Saran Fokus</p>
                        <p class="mt-3 text-sm leading-6 text-slate-200">
                            Periksa transaksi QRIS yang masih pending agar laporan harian tidak tertahan saat tutup booth.
                        </p>
                    </div>

                    <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-sm font-semibold text-emerald-800">Produk aktif: {{ $stats['active_products'] }}</p>
                        <p class="mt-2 text-sm leading-6 text-emerald-700">
                            Cukup ideal untuk booth kecil dengan transaksi cepat dan pengelolaan menu sederhana.
                        </p>
                    </div>

                    <div class="rounded-[1.5rem] border border-orange-200 bg-orange-50 p-4">
                        <p class="text-sm font-semibold text-orange-800">Rata-rata tiket belanja</p>
                        <p class="mt-2 text-sm leading-6 text-orange-700">
                            Rp{{ number_format($stats['average_ticket'], 0, ',', '.') }} per transaksi hari ini.
                        </p>
                    </div>
                </div>
            </article>
        </aside>
    </section>
@endsection
