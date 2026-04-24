@php
    $title = 'Produk Booth';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Produk')
@section('panel-title', 'Produk yang dijual')
@section('panel-description', 'Kelola daftar menu aktif, kategori, dan harga agar kasir selalu memakai data terbaru.')

@section('panel-actions')
    <a
        href="{{ route('products.create') }}"
        aria-label="Tambah produk"
        title="Tambah produk"
        class="font-display inline-flex h-14 w-14 items-center justify-center rounded-[1.5rem] bg-orange-500 text-3xl font-semibold leading-none text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
    >
        +
    </a>
@endsection

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
        <form method="GET" action="{{ route('products.index') }}" class="grid gap-4 xl:grid-cols-[1.1fr_0.8fr_0.8fr_0.8fr_0.8fr_auto]">
            <div>
                <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Cari produk</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama produk"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
            </div>

            <div>
                <label for="category_id" class="mb-2 block text-sm font-medium text-slate-700">Kategori</label>
                <select
                    id="category_id"
                    name="category_id"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua status</option>
                    <option value="active" @selected($status === 'active')>Aktif</option>
                    <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
                </select>
            </div>

            <div>
                <label for="menu_group" class="mb-2 block text-sm font-medium text-slate-700">Kelompok menu</label>
                <select
                    id="menu_group"
                    name="menu_group"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua kelompok</option>
                    @foreach ($menuGroups as $groupOption)
                        <option value="{{ $groupOption }}" @selected($menuGroup === $groupOption)>{{ $groupOption }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="selling_unit" class="mb-2 block text-sm font-medium text-slate-700">Satuan jual</label>
                <select
                    id="selling_unit"
                    name="selling_unit"
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua satuan</option>
                    @foreach ($sellingUnits as $unitOption)
                        <option value="{{ $unitOption }}" @selected($sellingUnit === $unitOption)>{{ $unitOption }}</option>
                    @endforeach
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
                    href="{{ route('products.index') }}"
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
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Daftar Produk</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Menu booth aktif dan nonaktif</h2>
            </div>
            <div class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">
                {{ $products->total() }} produk
            </div>
        </div>

        <div class="mt-5 grid gap-4">
            @forelse ($products as $product)
                <article class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex gap-4">
                            <div class="h-24 w-24 shrink-0 overflow-hidden rounded-[1.35rem] border border-slate-200 bg-slate-100">
                                <img
                                    src="{{ $product->image_url }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover"
                                >
                            </div>

                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="font-display text-xl font-semibold text-slate-900">{{ $product->name }}</h3>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ $product->category->name }}
                                    </span>
                                    @if ($product->menu_group)
                                        <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">
                                            {{ $product->menu_group }}
                                        </span>
                                    @endif
                                    @if ($product->selling_unit)
                                        <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                                            {{ $product->selling_unit }}
                                        </span>
                                    @endif
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                                <p class="font-display mt-3 text-2xl font-semibold text-orange-600">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </p>
                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $product->image_path ? 'Produk ini sudah memakai gambar.' : 'Belum ada gambar, placeholder akan ditampilkan.' }}
                                </p>
                                @if ($product->track_stock)
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->isOutOfStock() ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                            {{ $product->isOutOfStock() ? 'Stok Habis' : 'Stok Ada' }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <a
                                href="{{ route('products.edit', $product) }}"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                            >
                                Edit
                            </a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?');">
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
                    Belum ada produk yang tersimpan.
                </div>
            @endforelse
        </div>

        @if ($products->hasPages())
            <div class="mt-5">
                {{ $products->links() }}
            </div>
        @endif
    </section>
@endsection
