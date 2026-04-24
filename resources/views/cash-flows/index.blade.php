@php
    $title = 'Arus Kas';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Arus Kas')
@section('panel-title', 'Kas masuk dan keluar')
@section('panel-description', 'Catat pergerakan kas operasional booth agar laporan harian selalu rapi dan mudah dicek.')

@section('panel-actions')
    <a
        href="{{ route('cash-flows.create') }}"
        class="font-display rounded-[1.5rem] bg-orange-500 px-5 py-4 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
    >
        Tambah Arus Kas
    </a>
@endsection

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
        <form method="GET" action="{{ route('cash-flows.index') }}" class="grid gap-4 lg:grid-cols-[1fr_auto]">
            <div>
                <label for="type" class="mb-2 block text-sm font-medium text-slate-700">Tipe arus kas</label>
                <select
                    id="type"
                    name="type"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua tipe</option>
                    <option value="in" @selected($type === 'in')>Kas masuk</option>
                    <option value="out" @selected($type === 'out')>Kas keluar</option>
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button
                    type="submit"
                    class="rounded-2xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-orange-600"
                >
                    Filter
                </button>
                <a
                    href="{{ route('cash-flows.index') }}"
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
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Riwayat Arus Kas</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Aktivitas operasional booth</h2>
            </div>
            <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">
                {{ $cashFlows->total() }} catatan
            </div>
        </div>

        <div class="mt-5 grid gap-4">
            @forelse ($cashFlows as $cashFlow)
                <article class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $cashFlow->type === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $cashFlow->type === 'in' ? 'Kas Masuk' : 'Kas Keluar' }}
                                </span>
                                <span class="text-sm text-slate-500">{{ $cashFlow->flow_date->format('d M Y, H:i') }}</span>
                            </div>
                            <h3 class="font-display mt-3 text-xl font-semibold text-slate-900">
                                {{ $cashFlow->source ?: 'Operasional Booth' }}
                            </h3>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                                {{ $cashFlow->description ?: 'Tidak ada keterangan tambahan.' }}
                            </p>
                            <p class="mt-2 text-sm text-slate-500">Dicatat oleh {{ $cashFlow->user->name }}</p>
                        </div>

                        <div class="flex flex-col items-start gap-3 lg:items-end">
                            <p class="font-display text-2xl font-semibold {{ $cashFlow->type === 'in' ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $cashFlow->type === 'in' ? '+' : '-' }}Rp{{ number_format($cashFlow->amount, 0, ',', '.') }}
                            </p>
                            <div class="flex gap-3">
                                <a
                                    href="{{ route('cash-flows.edit', $cashFlow) }}"
                                    class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                >
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('cash-flows.destroy', $cashFlow) }}" onsubmit="return confirm('Hapus arus kas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-12 text-center text-sm text-slate-500">
                    Belum ada catatan arus kas.
                </div>
            @endforelse
        </div>

        @if ($cashFlows->hasPages())
            <div class="mt-5">
                {{ $cashFlows->links() }}
            </div>
        @endif
    </section>
@endsection
