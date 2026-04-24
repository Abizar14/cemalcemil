@php
    $title = 'Struk Transaksi';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Detail Transaksi')
@section('panel-title', 'Struk ' . $transaction->invoice_number)
@section('panel-description', 'Lihat detail transaksi, cetak struk biasa atau thermal, konfirmasi QRIS, edit transaksi, dan batalkan transaksi bila diperlukan.')

@section('panel-actions')
    <div class="flex flex-wrap gap-3">
        <a
            href="{{ auth()->user()->isAdmin() ? route('transactions.index') : route('transactions.create') }}"
            class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
        >
            {{ auth()->user()->isAdmin() ? 'Kembali ke Riwayat' : 'Kembali ke Kasir' }}
        </a>
        <a
            href="{{ route('transactions.print', $transaction) }}"
            class="font-display rounded-[1.5rem] bg-orange-500 px-5 py-4 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
        >
            Cetak Struk
        </a>
        <a
            href="{{ route('transactions.thermal-print', $transaction) }}"
            class="font-display rounded-[1.5rem] bg-slate-900 px-5 py-4 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-slate-800"
        >
            Thermal Print
        </a>
        @if (auth()->user()->isAdmin() && ! $transaction->isCancelled())
            <a
                href="{{ route('transactions.edit', $transaction) }}"
                class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
            >
                Edit Transaksi
            </a>
        @endif
    </div>
@endsection

@section('panel-content')
    @if ($errors->has('status'))
        <div class="animate-rise rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            {{ $errors->first('status') }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Informasi Invoice</p>
                    <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">{{ $transaction->invoice_number }}</h2>
                    <p class="mt-3 text-sm text-slate-500">
                        {{ $transaction->transaction_date->format('d M Y, H:i') }} - Kasir: {{ $transaction->user->name }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold uppercase text-slate-700">
                        {{ $transaction->payment_method }}
                    </span>
                    <span class="rounded-full px-4 py-2 text-sm font-semibold uppercase {{ $transaction->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($transaction->payment_status === 'confirmed' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ $transaction->payment_status }}
                    </span>
                    <span class="rounded-full px-4 py-2 text-sm font-semibold uppercase {{ $transaction->transaction_status === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-slate-900 text-white' }}">
                        {{ $transaction->transaction_status }}
                    </span>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-slate-200/80 bg-white/85">
                <div class="hidden grid-cols-[1.5fr_0.6fr_0.8fr_0.8fr] gap-4 border-b border-slate-200 px-5 py-4 text-xs font-semibold tracking-[0.16em] text-slate-500 uppercase md:grid">
                    <span>Produk</span>
                    <span>Qty</span>
                    <span>Harga</span>
                    <span>Subtotal</span>
                </div>

                <div class="divide-y divide-slate-200/80">
                    @foreach ($transaction->details as $detail)
                        <div class="grid gap-3 px-5 py-4 md:grid-cols-[1.5fr_0.6fr_0.8fr_0.8fr] md:items-center">
                            <div>
                                <p class="font-display text-base font-semibold text-slate-900">{{ $detail->product_name }}</p>
                            </div>
                            <div class="text-sm text-slate-600">{{ $detail->qty }}</div>
                            <div class="text-sm text-slate-600">Rp{{ number_format($detail->price, 0, ',', '.') }}</div>
                            <div class="font-semibold text-slate-900">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($transaction->notes)
                <div class="mt-5 rounded-[1.5rem] border border-orange-100 bg-orange-50 p-4 text-sm text-slate-700">
                    <p class="font-semibold text-orange-800">Catatan</p>
                    <p class="mt-2 leading-6">{{ $transaction->notes }}</p>
                </div>
            @endif
        </section>

        <aside class="space-y-6">
            <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.05s;">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Ringkasan Pembayaran</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Total dan status bayar</h2>

                <div class="mt-5 space-y-3">
                    <div class="rounded-[1.5rem] bg-slate-900 p-5 text-white">
                        <p class="text-sm text-slate-300">Total</p>
                        <p class="font-display mt-2 text-3xl font-semibold">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                            <p class="text-sm text-slate-500">Dibayar</p>
                            <p class="font-display mt-2 text-xl font-semibold text-slate-900">Rp{{ number_format($transaction->paid_amount ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4">
                            <p class="text-sm text-slate-500">Kembalian</p>
                            <p class="font-display mt-2 text-xl font-semibold text-slate-900">Rp{{ number_format($transaction->change_amount ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>

                @if (! $transaction->isCancelled() && $transaction->payment_method === 'qris' && $transaction->payment_status === 'pending')
                    <form method="POST" action="{{ route('transactions.confirm-qris', $transaction) }}">
                        @csrf
                        @method('PATCH')
                            <button
                                type="submit"
                                class="font-display w-full rounded-2xl bg-emerald-600 px-5 py-4 text-sm font-semibold text-white transition hover:bg-emerald-700"
                            >
                                Konfirmasi Pembayaran QRIS
                            </button>
                        </form>
                    @endif
                </div>
            </section>

            <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.1s;">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Status & Tindakan</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Kelola transaksi ini</h2>
                @if ($transaction->isCancelled())
                    <div class="mt-4 rounded-[1.5rem] border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                        <p class="font-semibold">Transaksi dibatalkan</p>
                        <p class="mt-2 leading-6">{{ $transaction->cancel_reason ?: 'Tidak ada alasan pembatalan.' }}</p>
                    </div>
                @elseif (auth()->user()->isAdmin())
                    <p class="mt-4 text-sm leading-6 text-slate-600">
                        Jika ada kesalahan input, kamu bisa edit transaksi atau batalkan transaksi dengan alasan yang jelas.
                    </p>
                    <form method="POST" action="{{ route('transactions.cancel', $transaction) }}" class="mt-5 space-y-3">
                        @csrf
                        @method('PATCH')
                        <textarea
                            name="cancel_reason"
                            rows="4"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            placeholder="Tulis alasan pembatalan transaksi"
                        >{{ old('cancel_reason') }}</textarea>
                        <button
                            type="submit"
                            class="w-full rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
                        >
                            Batalkan Transaksi
                        </button>
                    </form>
                @else
                    <div class="mt-4 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                        Halaman ini hanya menampilkan detail struk dan konfirmasi QRIS untuk kasir.
                    </div>
                @endif
            </section>
        </aside>
    </div>

    <style>
        @media print {
            body {
                background: #ffffff !important;
            }

            aside,
            header form,
            nav,
            .soft-grid,
            .animate-rise,
            .shadow-panel {
                display: none !important;
            }

            main,
            section,
            article {
                box-shadow: none !important;
                background: #ffffff !important;
            }
        }
    </style>
@endsection
