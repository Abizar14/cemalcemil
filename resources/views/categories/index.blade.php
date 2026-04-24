@php
    $title = 'Kategori Produk';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Kategori')
@section('panel-title', 'Kategori produk booth')
@section('panel-description', 'Kelola kelompok menu agar produk lebih rapi dan mudah dipilih saat transaksi.')

@section('panel-actions')
    <a
        href="{{ route('categories.create') }}"
        class="font-display rounded-[1.5rem] bg-orange-500 px-5 py-4 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
    >
        Tambah Kategori
    </a>
@endsection

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
        <form method="GET" action="{{ route('categories.index') }}" class="grid gap-4 lg:grid-cols-[1fr_auto]">
            <div>
                <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Cari kategori</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Misal: minuman, snack"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
            </div>

            <div class="flex items-end gap-3">
                <button
                    type="submit"
                    class="rounded-2xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-orange-600"
                >
                    Filter
                </button>
                <a
                    href="{{ route('categories.index') }}"
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
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Daftar Kategori</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Struktur menu booth</h2>
            </div>
            <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">
                {{ $categories->total() }} kategori
            </div>
        </div>

        <div class="mt-5 grid gap-4">
            @forelse ($categories as $category)
                <article class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="font-display text-xl font-semibold text-slate-900">{{ $category->name }}</h3>
                                <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">
                                    {{ $category->products_count }} produk
                                </span>
                            </div>
                            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                                {{ $category->description ?: 'Belum ada deskripsi untuk kategori ini.' }}
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <a
                                href="{{ route('categories.edit', $category) }}"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                            >
                                Edit
                            </a>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?');">
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
                </article>
            @empty
                <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 px-5 py-12 text-center text-sm text-slate-500">
                    Belum ada kategori yang tersimpan.
                </div>
            @endforelse
        </div>

        @if ($categories->hasPages())
            <div class="mt-5">
                {{ $categories->links() }}
            </div>
        @endif
    </section>
@endsection
