@php
    $title = 'Shift Kasir';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Shift')
@section('panel-title', 'Daftar shift kasir')
@section('panel-description', 'Pantau shift yang sedang berjalan, shift yang sudah ditutup, dan buka detail rekap setiap shift.')

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
        <form method="GET" action="{{ route('shifts.index') }}" class="grid gap-4 xl:grid-cols-[1fr_1fr_auto]">
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
                <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                <select id="status" name="status" class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                    <option value="">Semua status</option>
                    <option value="open" @selected($status === 'open')>Open</option>
                    <option value="closed" @selected($status === 'closed')>Closed</option>
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-orange-600">Filter</button>
                <a href="{{ route('shifts.index') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </section>

    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Riwayat Shift</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Shift kasir terbaru</h2>
            </div>
            <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">{{ $shifts->total() }} shift</div>
        </div>

        <div class="mt-5 grid gap-4">
            @forelse ($shifts as $shift)
                <article class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="font-display text-xl font-semibold text-slate-900">{{ $shift->user->name }}</h3>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $shift->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-900 text-white' }}">
                                    {{ $shift->status }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm text-slate-500">
                                Dibuka {{ $shift->opened_at->format('d M Y, H:i') }}
                                {{ $shift->closed_at ? ' - ditutup '.$shift->closed_at->format('d M Y, H:i') : ' - masih aktif' }}
                            </p>
                            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-[1.25rem] bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                    Modal awal<br>
                                    <span class="font-semibold text-slate-900">Rp{{ number_format($shift->opening_cash, 0, ',', '.') }}</span>
                                </div>
                                <div class="rounded-[1.25rem] bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                    Estimasi tutup<br>
                                    <span class="font-semibold text-slate-900">Rp{{ number_format($shift->closing_cash_expected ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="rounded-[1.25rem] bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                    Selisih<br>
                                    <span class="font-semibold {{ ($shift->cash_difference ?? 0) < 0 ? 'text-rose-600' : 'text-slate-900' }}">
                                        Rp{{ number_format($shift->cash_difference ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('shifts.show', $shift) }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Detail Shift
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-12 text-center text-sm text-slate-500">
                    Belum ada shift yang tercatat.
                </div>
            @endforelse
        </div>

        @if ($shifts->hasPages())
            <div class="mt-5">
                {{ $shifts->links() }}
            </div>
        @endif
    </section>
@endsection
