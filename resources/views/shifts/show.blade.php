@php
    $title = 'Detail Shift';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Detail Shift')
@section('panel-title', 'Shift ' . $shift->user->name)
@section('panel-description', 'Lihat rekap penjualan, arus kas, dan hasil penutupan shift secara rinci.')

@section('panel-actions')
    <div class="flex flex-wrap gap-3">
        <a
            href="{{ auth()->user()->isAdmin() ? route('shifts.index') : route('transactions.create') }}"
            class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
        >
            Kembali
        </a>
        @if (auth()->user()->isAdmin() && $shift->status === 'closed')
            <a
                href="#koreksi-shift"
                class="rounded-[1.5rem] bg-slate-900 px-5 py-4 text-sm font-semibold text-white transition hover:bg-orange-600"
            >
                Koreksi Shift
            </a>
        @endif
    </div>
@endsection

@section('panel-content')
    @if ($errors->has('shift'))
        <div class="animate-rise rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            {{ $errors->first('shift') }}
        </div>
    @endif

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="mesh-panel shadow-panel rounded-[1.75rem] border border-white/70 p-5">
            <p class="text-sm text-slate-500">Modal awal</p>
            <p class="font-display mt-3 text-3xl font-semibold text-slate-900">Rp{{ number_format($shift->opening_cash, 0, ',', '.') }}</p>
        </article>
        <article class="mesh-panel shadow-panel rounded-[1.75rem] border border-white/70 p-5">
            <p class="text-sm text-slate-500">Total penjualan</p>
            <p class="font-display mt-3 text-3xl font-semibold text-slate-900">Rp{{ number_format($summary['total_sales'], 0, ',', '.') }}</p>
            <p class="mt-2 text-xs leading-5 text-slate-500">Gabungan semua transaksi selesai: cash + QRIS.</p>
        </article>
        <article class="mesh-panel shadow-panel rounded-[1.75rem] border border-white/70 p-5">
            <p class="text-sm text-slate-500">Cash sales</p>
            <p class="font-display mt-3 text-3xl font-semibold text-emerald-700">Rp{{ number_format($summary['cash_sales'], 0, ',', '.') }}</p>
        </article>
        <article class="mesh-panel shadow-panel rounded-[1.75rem] border border-white/70 p-5">
            <p class="text-sm text-slate-500">QRIS sales</p>
            <p class="font-display mt-3 text-3xl font-semibold text-sky-700">Rp{{ number_format($summary['qris_sales'], 0, ',', '.') }}</p>
        </article>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <article class="mesh-panel shadow-panel rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
            <div class="flex flex-col gap-2">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Informasi Shift</p>
                <h2 class="font-display text-2xl font-semibold text-slate-900">{{ $shift->opened_at->format('d M Y, H:i') }}</h2>
                <p class="text-sm text-slate-500">
                    Status {{ $shift->status }}{{ $shift->closed_at ? ', ditutup '.$shift->closed_at->format('d M Y, H:i') : ', masih berjalan' }}
                </p>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                    <p class="text-sm text-slate-500">Kas masuk</p>
                    <p class="font-display mt-2 text-xl font-semibold text-emerald-700">Rp{{ number_format($summary['cash_in'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                    <p class="text-sm text-slate-500">Kas keluar</p>
                    <p class="font-display mt-2 text-xl font-semibold text-rose-600">Rp{{ number_format($summary['cash_out'], 0, ',', '.') }}</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4 sm:col-span-2">
                    <p class="text-sm text-slate-500">Estimasi kas sistem saat tutup</p>
                    <p class="font-display mt-2 text-xl font-semibold text-slate-900">Rp{{ number_format($summary['expected_closing_cash'], 0, ',', '.') }}</p>
                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Dihitung dari modal awal + penjualan cash + kas masuk - kas keluar.
                    </p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                    <p class="text-sm text-slate-500">Kas aktual saat tutup</p>
                    <p class="font-display mt-2 text-xl font-semibold text-slate-900">Rp{{ number_format($shift->closing_cash_actual ?? 0, 0, ',', '.') }}</p>
                    <p class="mt-2 text-xs leading-5 text-slate-500">Jumlah uang tunai fisik yang benar-benar ada di laci saat dihitung manual.</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                    <p class="text-sm text-slate-500">Selisih</p>
                    <p class="font-display mt-2 text-xl font-semibold {{ ($shift->cash_difference ?? 0) < 0 ? 'text-rose-600' : 'text-slate-900' }}">
                        Rp{{ number_format($shift->cash_difference ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="mt-2 text-xs leading-5 text-slate-500">Hasil kas aktual dikurangi estimasi kas sistem.</p>
                </div>
            </div>

            @if ($shift->opening_notes || $shift->closing_notes)
                <div class="mt-5 grid gap-4">
                    @if ($shift->opening_notes)
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4 text-sm text-slate-700">
                            <p class="font-semibold text-slate-900">Catatan buka shift</p>
                            <p class="mt-2 leading-6">{{ $shift->opening_notes }}</p>
                        </div>
                    @endif
                    @if ($shift->closing_notes)
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4 text-sm text-slate-700">
                            <p class="font-semibold text-slate-900">Catatan tutup shift</p>
                            <p class="mt-2 leading-6">{{ $shift->closing_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </article>

        <aside class="space-y-6">
            @if (auth()->user()->isAdmin() && $shift->status === 'closed')
                <article id="koreksi-shift" class="mesh-panel shadow-panel rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
                    <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Koreksi Shift</p>
                    <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Perbaiki hasil penutupan</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Dipakai jika setelah tutup shift ada salah input pada kas aktual atau catatan. Selisih kas akan dihitung ulang otomatis.
                    </p>

                    <form method="POST" action="{{ route('shifts.update', $shift) }}" class="mt-5 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="closing_cash_actual" class="mb-2 block text-sm font-medium text-slate-700">Kas aktual saat tutup</label>
                            <input
                                id="closing_cash_actual"
                                type="number"
                                min="0"
                                step="1"
                                name="closing_cash_actual"
                                value="{{ old('closing_cash_actual', (int) round((float) ($shift->closing_cash_actual ?? 0))) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            >
                            @error('closing_cash_actual')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="opening_notes" class="mb-2 block text-sm font-medium text-slate-700">Catatan buka shift</label>
                            <textarea
                                id="opening_notes"
                                name="opening_notes"
                                rows="3"
                                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            >{{ old('opening_notes', $shift->opening_notes) }}</textarea>
                            @error('opening_notes')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="closing_notes" class="mb-2 block text-sm font-medium text-slate-700">Catatan tutup shift</label>
                            <textarea
                                id="closing_notes"
                                name="closing_notes"
                                rows="3"
                                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            >{{ old('closing_notes', $shift->closing_notes) }}</textarea>
                            @error('closing_notes')
                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-2xl border border-orange-200 bg-orange-50 px-5 py-4 text-sm font-semibold text-orange-700 transition hover:bg-orange-100"
                        >
                            Simpan Koreksi Shift
                        </button>
                    </form>
                </article>
            @endif

            <article class="mesh-panel shadow-panel rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Transaksi Shift</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Riwayat transaksi</h2>

                <div class="mt-5 space-y-3">
                    @forelse ($shift->transactions as $transaction)
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-display text-base font-semibold text-slate-900">{{ $transaction->invoice_number }}</p>
                                    <p class="text-sm text-slate-500">{{ $transaction->transaction_date->format('d M Y, H:i') }}</p>
                                </div>
                                <p class="font-semibold text-slate-900">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada transaksi pada shift ini.
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="mesh-panel shadow-panel rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Arus Kas Shift</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Kas masuk dan keluar</h2>

                <div class="mt-5 space-y-3">
                    @forelse ($shift->cashFlows as $cashFlow)
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-display text-base font-semibold text-slate-900">{{ $cashFlow->source ?: 'Operasional booth' }}</p>
                                    <p class="text-sm text-slate-500">{{ $cashFlow->flow_date->format('d M Y, H:i') }}</p>
                                </div>
                                <p class="font-semibold {{ $cashFlow->type === 'in' ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $cashFlow->type === 'in' ? '+' : '-' }}Rp{{ number_format($cashFlow->amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-10 text-center text-sm text-slate-500">
                            Belum ada arus kas pada shift ini.
                        </div>
                    @endforelse
                </div>
            </article>
        </aside>
    </section>
@endsection
