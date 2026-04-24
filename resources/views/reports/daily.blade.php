@php
    $title = 'Laporan Harian';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Laporan Operasional')
@section('panel-title', 'Laporan harian, shift, dan backup')
@section('panel-description', 'Pantau penjualan berdasarkan preset periode, filter kasir/shift, ekspor PDF, dan unduh backup database untuk keamanan operasional booth.')

@section('panel-actions')
    <div class="flex flex-wrap gap-3">
        <a
            href="{{ route('reports.daily.pdf', request()->query()) }}"
            class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
        >
            Export PDF
        </a>
        <a
            href="{{ route('backups.database') }}"
            class="rounded-[1.5rem] bg-slate-900 px-5 py-4 text-sm font-semibold text-white transition hover:bg-orange-600"
        >
            Backup DB
        </a>
    </div>
@endsection

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-[1.75rem] border border-white/80 bg-white/80 shadow-lg shadow-orange-100/50">
                    <img src="{{ asset($booth['logo']) }}" alt="Logo {{ $booth['name'] }}" class="h-16 w-16 object-contain">
                </div>
                <div>
                    <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Identitas Booth</p>
                    <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">{{ $booth['name'] }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $booth['address'] }}, {{ $booth['city'] }} | {{ $booth['phone'] }}</p>
                </div>
            </div>
            <div class="rounded-[1.5rem] border border-orange-100 bg-orange-50 px-4 py-3 text-sm text-orange-800">
                Periode {{ $dateFrom->format('d M Y') }} - {{ $dateTo->format('d M Y') }}
            </div>
        </div>
    </section>

    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
        <form method="GET" action="{{ route('reports.daily') }}" class="grid gap-4 xl:grid-cols-[1fr_1fr_0.9fr_0.9fr_auto]">
            <div class="xl:col-span-5">
                <p class="mb-3 text-sm font-medium text-slate-700">Preset cepat</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ([
                        'today' => 'Hari ini',
                        'yesterday' => 'Kemarin',
                        'this_week' => 'Minggu ini',
                        'this_month' => 'Bulan ini',
                        'last_7_days' => '7 hari',
                        'custom' => 'Custom',
                    ] as $presetValue => $presetLabel)
                        <label class="cursor-pointer">
                            <input type="radio" name="preset" value="{{ $presetValue }}" class="sr-only" {{ $preset === $presetValue ? 'checked' : '' }}>
                            <span class="inline-flex rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                                {{ $presetLabel }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="date_from" class="mb-2 block text-sm font-medium text-slate-700">Dari tanggal</label>
                <input id="date_from" type="date" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}" class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
            </div>
            <div>
                <label for="date_to" class="mb-2 block text-sm font-medium text-slate-700">Sampai tanggal</label>
                <input id="date_to" type="date" name="date_to" value="{{ $dateTo->format('Y-m-d') }}" class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
            </div>
            <div>
                <label for="user_id" class="mb-2 block text-sm font-medium text-slate-700">Kasir</label>
                <select id="user_id" name="user_id" class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                    <option value="">Semua kasir</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected($userId === $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="shift_id" class="mb-2 block text-sm font-medium text-slate-700">Shift</label>
                <select id="shift_id" name="shift_id" class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                    <option value="">Semua shift</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}" @selected($shiftId === $shift->id)>
                            {{ $shift->user->name }} - {{ $shift->opened_at->format('d M H:i') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-orange-600">Tampilkan</button>
                <a href="{{ route('reports.daily') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5">
            <p class="text-sm text-slate-500">Penjualan Tercatat</p>
            <p class="font-display mt-3 text-3xl font-semibold text-slate-900">Rp{{ number_format($summary['gross_sales'], 0, ',', '.') }}</p>
            <p class="mt-4 text-sm text-slate-600">{{ $summary['completed_count'] }} transaksi selesai dalam periode ini.</p>
        </article>
        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5">
            <p class="text-sm text-slate-500">QRIS Pending</p>
            <p class="font-display mt-3 text-3xl font-semibold text-amber-600">Rp{{ number_format($summary['pending_qris_total'], 0, ',', '.') }}</p>
            <p class="mt-4 text-sm text-slate-600">Nilai transaksi QRIS yang masih menunggu konfirmasi.</p>
        </article>
        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5">
            <p class="text-sm text-slate-500">Net Operasional</p>
            <p class="font-display mt-3 text-3xl font-semibold {{ $summary['net_amount'] < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                Rp{{ number_format($summary['net_amount'], 0, ',', '.') }}
            </p>
            <p class="mt-4 text-sm text-slate-600">Realisasi penjualan + kas masuk - kas keluar.</p>
        </article>
        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5">
            <p class="text-sm text-slate-500">Nilai Minusan</p>
            <p class="font-display mt-3 text-3xl font-semibold {{ $summary['minus_amount'] > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                Rp{{ number_format($summary['minus_amount'], 0, ',', '.') }}
            </p>
            <p class="mt-4 text-sm text-slate-600">Jika negatif, operasional sedang minus pada hari tertentu.</p>
        </article>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Rekap Harian</p>
                    <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Ringkasan per tanggal</h2>
                </div>
                <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">{{ $rows->count() }} hari</div>
            </div>

            <div class="mt-5 overflow-hidden rounded-[1.5rem] border border-slate-200/80 bg-white/85">
                <div class="hidden grid-cols-[0.85fr_0.85fr_0.75fr_0.75fr_0.75fr_0.75fr_0.75fr] gap-4 border-b border-slate-200 px-5 py-4 text-xs font-semibold tracking-[0.16em] text-slate-500 uppercase lg:grid">
                    <span>Tanggal</span>
                    <span>Penjualan</span>
                    <span>Realisasi</span>
                    <span>Kas Masuk</span>
                    <span>Kas Keluar</span>
                    <span>Net</span>
                    <span>Minus</span>
                </div>

                <div class="divide-y divide-slate-200/80">
                    @foreach ($rows as $row)
                        <div class="grid gap-3 px-5 py-4 lg:grid-cols-[0.85fr_0.85fr_0.75fr_0.75fr_0.75fr_0.75fr_0.75fr] lg:items-center">
                            <div>
                                <p class="font-display text-base font-semibold text-slate-900">{{ $row['date']->format('d M Y') }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $row['completed_count'] }} selesai, {{ $row['cancelled_count'] }} batal</p>
                            </div>
                            <div class="text-sm text-slate-700">Rp{{ number_format($row['gross_sales'], 0, ',', '.') }}</div>
                            <div class="text-sm text-slate-700">Rp{{ number_format($row['realized_sales'], 0, ',', '.') }}</div>
                            <div class="text-sm text-emerald-700">Rp{{ number_format($row['cash_in'], 0, ',', '.') }}</div>
                            <div class="text-sm text-rose-600">Rp{{ number_format($row['cash_out'], 0, ',', '.') }}</div>
                            <div class="text-sm font-semibold {{ $row['net_amount'] < 0 ? 'text-rose-600' : 'text-slate-900' }}">Rp{{ number_format($row['net_amount'], 0, ',', '.') }}</div>
                            <div class="text-sm font-semibold {{ $row['minus_amount'] > 0 ? 'text-rose-600' : 'text-emerald-700' }}">Rp{{ number_format($row['minus_amount'], 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <aside class="space-y-6">
            <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Rekap Tambahan</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Ikhtisar periode</h2>

                <div class="mt-5 space-y-3">
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                        <p class="text-sm text-slate-500">Kas masuk</p>
                        <p class="font-display mt-2 text-xl font-semibold text-emerald-700">Rp{{ number_format($summary['cash_in'], 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                        <p class="text-sm text-slate-500">Kas keluar</p>
                        <p class="font-display mt-2 text-xl font-semibold text-rose-600">Rp{{ number_format($summary['cash_out'], 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                        <p class="text-sm text-slate-500">Transaksi dibatalkan</p>
                        <p class="font-display mt-2 text-xl font-semibold text-slate-900">Rp{{ number_format($summary['cancelled_total'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </article>

            <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Produk Terlaris</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Top product periode ini</h2>

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
                                <p class="text-sm font-semibold text-orange-600">Rp{{ number_format($product->total_sales, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada data produk terjual pada periode ini.
                        </div>
                    @endforelse
                </div>
            </article>
        </aside>
    </section>

    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Rekap per Shift</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Penjualan per shift kasir</h2>
            </div>
            <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">{{ $shiftRows->count() }} shift</div>
        </div>

        <div class="mt-5 grid gap-4">
            @forelse ($shiftRows as $row)
                <article class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="font-display text-xl font-semibold text-slate-900">{{ $row['shift']->user->name }}</h3>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $row['shift']->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-900 text-white' }}">
                                    {{ $row['shift']->status }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500">
                                {{ $row['shift']->opened_at->format('d M Y, H:i') }}
                                {{ $row['shift']->closed_at ? ' - '.$row['shift']->closed_at->format('d M Y, H:i') : ' - masih berjalan' }}
                            </p>
                        </div>
                        <a href="{{ route('shifts.show', $row['shift']) }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Detail Shift
                        </a>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Transaksi</p>
                            <p class="font-display mt-2 text-xl font-semibold text-slate-900">{{ $row['transactions_count'] }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Cash</p>
                            <p class="font-display mt-2 text-xl font-semibold text-emerald-700">Rp{{ number_format($row['cash_sales'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">QRIS</p>
                            <p class="font-display mt-2 text-xl font-semibold text-sky-700">Rp{{ number_format($row['qris_sales'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Kas in/out</p>
                            <p class="font-display mt-2 text-xl font-semibold text-slate-900">
                                Rp{{ number_format($row['cash_in'], 0, ',', '.') }} / Rp{{ number_format($row['cash_out'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Estimasi tutup</p>
                            <p class="font-display mt-2 text-xl font-semibold text-slate-900">Rp{{ number_format($row['expected_closing_cash'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-12 text-center text-sm text-slate-500">
                    Belum ada shift dalam periode yang dipilih.
                </div>
            @endforelse
        </div>
    </section>
@endsection
