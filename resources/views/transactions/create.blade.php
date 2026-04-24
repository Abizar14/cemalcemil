@php
    $title = 'Kasir Transaksi';
    $initialItems = collect(old('items', []))
        ->filter(fn ($item) => isset($item['product_id'], $item['qty']))
        ->map(fn ($item) => [
            'product_id' => (int) $item['product_id'],
            'qty' => (int) $item['qty'],
        ])
        ->values()
        ->all();
    $productData = $products->map(fn ($product) => [
        'id' => $product->id,
        'name' => $product->name,
        'price' => (float) $product->price,
        'category_id' => $product->category_id,
        'menu_group' => $product->menu_group,
        'selling_unit' => $product->selling_unit,
        'image_url' => $product->image_url,
        'track_stock' => (bool) $product->track_stock,
        'stock_quantity' => $product->stock_quantity,
    ])->values()->all();
    $menuGroups = $products->pluck('menu_group')->filter()->unique()->sort()->values()->all();
    $sellingUnits = $products->pluck('selling_unit')->filter()->unique()->sort()->values()->all();
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Kasir Transaksi')
@section('panel-title', $currentShift ? 'Transaksi super cepat booth' : 'Buka shift sebelum mulai jualan')
@section('panel-description', $currentShift
    ? 'Shift aktif sudah siap. Pilih produk, pakai tombol nominal cepat, dan selesaikan transaksi tanpa pindah-pindah halaman.'
    : 'Untuk menjaga rekap per shift tetap rapi, buka shift aktif lebih dulu sebelum kasir mulai menerima transaksi.')

@section('panel-actions')
    @if (auth()->user()->isAdmin())
        <a
            href="{{ route('shifts.index') }}"
            class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
        >
            Lihat Shift
        </a>
        <a
            href="{{ route('transactions.index') }}"
            class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
        >
            Riwayat
        </a>
    @else
        <div class="rounded-[1.5rem] border border-orange-100 bg-white/80 px-5 py-4 text-sm font-semibold text-slate-700">
            Mode kasir aktif
        </div>
    @endif
@endsection

@section('panel-content')
    @if ($errors->has('items') || $errors->has('paid_amount') || $errors->has('shift'))
        <div class="animate-rise rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            {{ $errors->first('items') ?: ($errors->first('paid_amount') ?: $errors->first('shift')) }}
        </div>
    @endif

    @if (! $currentShift)
        <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6 lg:p-8">
            <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
                <div>
                    <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Shift Kasir</p>
                    <h2 class="font-display mt-2 text-3xl font-semibold text-slate-900">Mulai shift baru</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600">
                        Modal awal kas akan dipakai untuk menghitung estimasi uang penutup shift. Setelah shift dibuka,
                        transaksi dan arus kas akan otomatis masuk ke rekap shift ini.
                    </p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/80 p-4">
                            <p class="text-sm text-slate-500">Kasir</p>
                            <p class="font-display mt-2 text-xl font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/80 p-4">
                            <p class="text-sm text-slate-500">Waktu mulai</p>
                            <p class="font-display mt-2 text-xl font-semibold text-slate-900">{{ now()->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('shifts.store') }}" class="rounded-[1.75rem] border border-slate-200/80 bg-white/85 p-5 sm:p-6">
                    @csrf
                    <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Buka Shift</p>
                    <h3 class="font-display mt-2 text-2xl font-semibold text-slate-900">Input modal awal</h3>

                    <div class="mt-5 space-y-5">
                        <div>
                            <label for="opening_cash" class="mb-2 block text-sm font-medium text-slate-700">Modal awal kas</label>
                            <input
                                id="opening_cash"
                                type="number"
                                name="opening_cash"
                                min="0"
                                step="0.01"
                                value="{{ old('opening_cash', '0') }}"
                                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                                placeholder="0"
                            >
                        </div>

                        <div>
                            <label for="opening_notes" class="mb-2 block text-sm font-medium text-slate-700">Catatan pembuka</label>
                            <textarea
                                id="opening_notes"
                                name="opening_notes"
                                rows="4"
                                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                                placeholder="Contoh: booth ramai sejak pagi, modal receh lengkap"
                            >{{ old('opening_notes') }}</textarea>
                        </div>

                        <button
                            type="submit"
                            class="font-display w-full rounded-2xl bg-slate-900 px-5 py-4 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
                        >
                            Buka Shift Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </section>
    @else
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-[1.25fr_0.95fr]">
            <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Shift Aktif</p>
                        <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">
                            Shift {{ $currentShift->opened_at->format('d M Y, H:i') }}
                        </h2>
                        <p class="mt-3 text-sm text-slate-500">
                            Kasir: {{ $currentShift->user->name }} · Modal awal Rp{{ number_format($currentShift->opening_cash, 0, ',', '.') }}
                        </p>
                    </div>
                    <a
                        href="{{ route('shifts.show', $currentShift) }}"
                        class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Detail Shift
                    </a>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5">
                        <p class="text-sm text-slate-500">Transaksi</p>
                        <p class="font-display mt-3 text-3xl font-semibold text-slate-900">{{ $currentShiftSummary['transactions_count'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5">
                        <p class="text-sm text-slate-500">Total Penjualan</p>
                        <p class="font-display mt-3 text-3xl font-semibold text-slate-900">Rp{{ number_format($currentShiftSummary['total_sales'], 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5">
                        <p class="text-sm text-slate-500">Penjualan Cash</p>
                        <p class="font-display mt-3 text-3xl font-semibold text-emerald-700">Rp{{ number_format($currentShiftSummary['cash_sales'], 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5">
                        <p class="text-sm text-slate-500">Penjualan QRIS</p>
                        <p class="font-display mt-3 text-3xl font-semibold text-sky-700">Rp{{ number_format($currentShiftSummary['qris_sales'], 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-5 sm:col-span-2 2xl:col-span-1">
                        <p class="text-sm text-slate-500">Estimasi Tutup</p>
                        <p class="font-display mt-3 text-3xl font-semibold text-slate-900">Rp{{ number_format($currentShiftSummary['expected_closing_cash'], 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs leading-5 text-slate-500">Modal awal + cash sales + kas masuk - kas keluar.</p>
                    </div>
                </div>
            </article>

            <article class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
                <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Tutup Shift</p>
                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Rekap dan selisih kas</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    Saat tutup shift, masukkan uang tunai aktual yang ada di laci. Sistem akan hitung selisih otomatis.
                </p>

                <form method="POST" action="{{ route('shifts.close') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="closing_cash_actual" class="mb-2 block text-sm font-medium text-slate-700">Kas aktual saat tutup</label>
                        <input
                            id="closing_cash_actual"
                            type="number"
                            min="0"
                            step="0.01"
                            name="closing_cash_actual"
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            placeholder="Masukkan uang tunai aktual"
                        >
                    </div>

                    <div>
                        <label for="closing_notes" class="mb-2 block text-sm font-medium text-slate-700">Catatan tutup shift</label>
                        <textarea
                            id="closing_notes"
                            name="closing_notes"
                            rows="3"
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            placeholder="Contoh: selisih karena receh, ada titipan owner, dsb"
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
                    >
                        Tutup Shift Ini
                    </button>
                </form>
            </article>
        </section>

        <form method="POST" action="{{ route('transactions.store') }}" id="cashier-form" class="grid gap-6 pb-28 xl:grid-cols-[minmax(0,1.55fr)_minmax(340px,0.9fr)] xl:items-start xl:pb-0">
            @csrf

            <section class="space-y-6 xl:min-h-0">
                <div class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6 xl:flex xl:max-h-[calc(140vh-14rem)] xl:min-h-0 xl:flex-col xl:overflow-hidden">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Katalog Produk</p>
                            <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Pilih menu booth</h2>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <input
                                id="product-search"
                                type="text"
                                placeholder="Cari produk..."
                                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            >
                            <select
                                id="category-filter"
                                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            >
                                <option value="">Semua kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <select
                                id="group-filter"
                                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            >
                                <option value="">All produk</option>
                                @foreach ($menuGroups as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                            <select
                                id="unit-filter"
                                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            >
                                <option value="">Semua satuan</option>
                                @foreach ($sellingUnits as $unit)
                                    <option value="{{ $unit }}">{{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <button type="button" data-group-chip="" class="group-chip rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white">
                            All Produk
                        </button>
                        @foreach ($menuGroups as $group)
                            <button type="button" data-group-chip="{{ $group }}" class="group-chip rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                                {{ $group }}
                            </button>
                        @endforeach
                    </div>

                    <div id="product-grid" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:min-h-0 xl:flex-1 xl:auto-rows-max xl:grid-cols-4 xl:content-start xl:overflow-y-auto xl:pr-2">
                        @forelse ($products as $product)
                            <button
                                type="button"
                                data-product-card
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-product-price="{{ number_format((float) $product->price, 2, '.', '') }}"
                                data-category-id="{{ $product->category_id }}"
                                data-menu-group="{{ $product->menu_group }}"
                                data-selling-unit="{{ $product->selling_unit }}"
                                class="group overflow-hidden rounded-[1.7rem] border border-slate-200/80 bg-white/90 text-left transition hover:-translate-y-1 hover:border-orange-200 hover:bg-orange-50"
                            >
                                <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                                    <img
                                        src="{{ $product->image_url }}"
                                        alt="{{ $product->name }}"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                    >
                                    <div class="absolute inset-x-0 top-0 flex items-start justify-between p-4">
                                        <p class="inline-flex rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-700 shadow-sm">
                                            {{ $product->category->name }}
                                        </p>
                                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-2xl font-semibold leading-none text-white shadow-lg transition group-hover:bg-orange-500">+</span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-display text-lg font-semibold text-slate-900">{{ $product->name }}</p>
                                            <div class="mt-2 flex flex-wrap gap-2">
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
                                                @if ($product->track_stock)
                                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->isLowStock() ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                                        Stok {{ $product->stock_quantity }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-500">Tap kartu untuk menambahkan ke keranjang.</p>
                                    <p class="font-display mt-4 text-2xl font-semibold text-orange-600">
                                        Rp{{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </button>
                        @empty
                            <div class="col-span-full rounded-[1.75rem] border border-dashed border-slate-300 bg-white/80 px-5 py-12 text-center">
                                <p class="font-display text-xl font-semibold text-slate-900">Produk aktif belum ada</p>
                                <p class="mt-3 text-sm leading-6 text-slate-500">
                                    Biasanya ini terjadi karena data produk belum di-seed, belum dibuat di menu admin,
                                    atau semua produk sedang nonaktif.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <aside class="space-y-6 xl:sticky xl:top-6 xl:max-h-[calc(140vh-3rem)] xl:self-start xl:overflow-y-auto xl:pr-1" id="payment-section">
                <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.05s;">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Keranjang</p>
                            <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Ringkasan belanja</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                id="reset-cart-button"
                                class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
                            >
                                Reset keranjang
                            </button>
                            <div id="cart-count" class="rounded-full bg-white/80 px-4 py-2 text-sm text-slate-600">0 item</div>
                        </div>
                    </div>

                    <div id="cart-empty" class="mt-5 rounded-[1.5rem] border border-dashed border-slate-300 bg-white/70 px-5 py-10 text-center text-sm text-slate-500">
                        Belum ada produk di keranjang.
                    </div>

                    <div id="cart-items" class="mt-5 space-y-3"></div>

                    <div class="mt-5 rounded-[1.5rem] border border-slate-200/80 bg-slate-900 p-5 text-white">
                        <div class="flex items-center justify-between text-sm text-slate-300">
                            <span>Total transaksi</span>
                            <span id="summary-qty">0 item</span>
                        </div>
                        <p id="summary-total" class="font-display mt-3 text-3xl font-semibold">Rp0</p>
                        <p class="mt-2 text-sm text-slate-300">Perhitungan total dilakukan otomatis dari item yang dipilih.</p>
                    </div>

                    <div id="hidden-items"></div>
                </section>

                <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.1s;">
                    <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Pembayaran</p>
                    <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Metode dan catatan</h2>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <label class="cursor-pointer rounded-[1.5rem] border border-slate-200 bg-white/80 p-4 transition has-[:checked]:border-orange-300 has-[:checked]:bg-orange-50">
                            <input type="radio" name="payment_method" value="cash" class="sr-only" {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}>
                            <span class="font-display text-lg font-semibold text-slate-900">Cash</span>
                            <span class="mt-2 block text-sm text-slate-600">Hitung bayar dan kembalian otomatis.</span>
                        </label>

                        <label class="cursor-pointer rounded-[1.5rem] border border-slate-200 bg-white/80 p-4 transition has-[:checked]:border-orange-300 has-[:checked]:bg-orange-50">
                            <input type="radio" name="payment_method" value="qris" class="sr-only" {{ old('payment_method') === 'qris' ? 'checked' : '' }}>
                            <span class="font-display text-lg font-semibold text-slate-900">QRIS</span>
                            <span class="mt-2 block text-sm text-slate-600">Status awal pending, lalu kasir konfirmasi.</span>
                        </label>
                    </div>

                    <div id="cash-payment-box" class="mt-5">
                        <label for="paid_amount" class="mb-2 block text-sm font-medium text-slate-700">Nominal dibayar</label>
                        <input
                            id="paid_amount"
                            type="number"
                            min="0"
                            step="0.01"
                            name="paid_amount"
                            value="{{ old('paid_amount') }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            placeholder="Masukkan nominal bayar"
                        >

                        <div class="mt-4 grid grid-cols-3 gap-2">
                            <button type="button" data-quick-cash="exact" class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                Uang Pas
                            </button>
                            @foreach ($quickCashAmounts as $amount)
                                <button type="button" data-quick-cash="{{ $amount }}" class="rounded-2xl border border-slate-200 bg-white px-3 py-3 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                    {{ number_format($amount / 1000, 0) }}K
                                </button>
                            @endforeach
                        </div>

                        <p id="change-preview" class="mt-3 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            Kembalian: Rp0
                        </p>
                    </div>

                    <div id="qris-payment-box" class="mt-5 hidden rounded-[1.5rem] border border-sky-200 bg-sky-50 p-4 text-sm text-sky-700">
                        Transaksi QRIS akan disimpan dengan status <span class="font-semibold">pending</span> sampai kasir melakukan konfirmasi.
                    </div>

                    <div class="mt-5">
                        <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">Catatan</label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                            placeholder="Catatan tambahan untuk transaksi ini"
                        >{{ old('notes') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        id="submit-transaction-button"
                        class="font-display mt-5 w-full rounded-2xl bg-slate-900 px-5 py-4 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
                    >
                        Simpan Transaksi
                    </button>
                    <p id="submit-helper-text" class="mt-3 text-sm text-slate-500">
                        Pilih produk dulu, lalu simpan transaksi saat keranjang sudah sesuai.
                    </p>
                </section>
            </aside>
        </form>
    @endif

    @if ($currentShift)
        <div class="fixed inset-x-3 bottom-3 z-20 xl:hidden">
            <div class="mesh-panel shadow-panel rounded-[1.5rem] border border-white/80 p-3">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p id="mobile-cart-count" class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">0 item</p>
                        <p id="mobile-summary-total" class="font-display mt-1 text-xl font-semibold text-slate-900">Rp0</p>
                    </div>
                    <button
                        type="button"
                        onclick="document.getElementById('payment-section').scrollIntoView({ behavior: 'smooth', block: 'start' })"
                        class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-orange-600"
                    >
                        Lanjut Bayar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($currentShift)
        <script>
            (() => {
                const productData = @json($productData);
                const initialItems = @json($initialItems);
                const cart = new Map();
                let currentTotalAmount = 0;

                const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(Math.max(Number(value) || 0, 0));
                const productGrid = document.getElementById('product-grid');
                const productSearch = document.getElementById('product-search');
                const categoryFilter = document.getElementById('category-filter');
                const groupFilter = document.getElementById('group-filter');
                const unitFilter = document.getElementById('unit-filter');
                const groupChips = document.querySelectorAll('[data-group-chip]');
                const cartItems = document.getElementById('cart-items');
                const cartEmpty = document.getElementById('cart-empty');
                const cartCount = document.getElementById('cart-count');
                const mobileCartCount = document.getElementById('mobile-cart-count');
                const summaryQty = document.getElementById('summary-qty');
                const summaryTotal = document.getElementById('summary-total');
                const mobileSummaryTotal = document.getElementById('mobile-summary-total');
                const hiddenItems = document.getElementById('hidden-items');
                const resetCartButton = document.getElementById('reset-cart-button');
                const paidAmountInput = document.getElementById('paid_amount');
                const changePreview = document.getElementById('change-preview');
                const cashPaymentBox = document.getElementById('cash-payment-box');
                const qrisPaymentBox = document.getElementById('qris-payment-box');
                const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
                const quickCashButtons = document.querySelectorAll('[data-quick-cash]');
                const submitTransactionButton = document.getElementById('submit-transaction-button');
                const submitHelperText = document.getElementById('submit-helper-text');

                const findProduct = (productId) => productData.find((product) => product.id === productId);

                const syncGroupChipState = () => {
                    groupChips.forEach((chip) => {
                        const isActive = chip.dataset.groupChip === groupFilter.value;
                        chip.classList.toggle('bg-slate-900', isActive);
                        chip.classList.toggle('text-white', isActive);
                        chip.classList.toggle('border', !isActive);
                        chip.classList.toggle('border-slate-200', !isActive);
                        chip.classList.toggle('bg-white', !isActive);
                        chip.classList.toggle('text-slate-600', !isActive);
                    });
                };

                const renderCart = () => {
                    cartItems.innerHTML = '';
                    hiddenItems.innerHTML = '';

                    let totalQty = 0;
                    currentTotalAmount = 0;

                    if (cart.size === 0) {
                        cartEmpty.classList.remove('hidden');
                    } else {
                        cartEmpty.classList.add('hidden');
                    }

                    Array.from(cart.values()).forEach((item, index) => {
                        totalQty += item.qty;
                        currentTotalAmount += item.qty * item.price;

                        const row = document.createElement('div');
                        row.className = 'rounded-[1.5rem] border border-slate-200/80 bg-white/85 p-4';
                        row.innerHTML = `
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <img src="${item.image_url}" alt="${item.name}" class="h-16 w-16 rounded-2xl border border-slate-200 bg-slate-100 object-cover">
                                    <div>
                                        <p class="font-display text-base font-semibold text-slate-900">${item.name}</p>
                                        <p class="mt-1 text-sm text-slate-500">Rp${formatRupiah(item.price)} per item</p>
                                    </div>
                                </div>
                                <button type="button" data-remove="${item.product_id}" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                    Hapus
                                </button>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <button type="button" data-dec="${item.product_id}" class="h-10 w-10 rounded-2xl border border-slate-200 bg-white text-lg font-semibold text-slate-700">-</button>
                                    <span class="min-w-10 text-center text-sm font-semibold text-slate-700">${item.qty}</span>
                                    <button type="button" data-inc="${item.product_id}" class="h-10 w-10 rounded-2xl border border-slate-200 bg-white text-lg font-semibold text-slate-700">+</button>
                                </div>
                                <p class="font-display text-lg font-semibold text-orange-600">Rp${formatRupiah(item.qty * item.price)}</p>
                            </div>
                        `;
                        cartItems.appendChild(row);

                        const productInput = document.createElement('input');
                        productInput.type = 'hidden';
                        productInput.name = `items[${index}][product_id]`;
                        productInput.value = item.product_id;
                        hiddenItems.appendChild(productInput);

                        const qtyInput = document.createElement('input');
                        qtyInput.type = 'hidden';
                        qtyInput.name = `items[${index}][qty]`;
                        qtyInput.value = item.qty;
                        hiddenItems.appendChild(qtyInput);
                    });

                    cartCount.textContent = `${totalQty} item`;
                    mobileCartCount.textContent = `${totalQty} item`;
                    summaryQty.textContent = `${totalQty} item`;
                    summaryTotal.textContent = `Rp${formatRupiah(currentTotalAmount)}`;
                    mobileSummaryTotal.textContent = `Rp${formatRupiah(currentTotalAmount)}`;
                    resetCartButton.disabled = cart.size === 0;
                    resetCartButton.classList.toggle('cursor-not-allowed', cart.size === 0);
                    resetCartButton.classList.toggle('opacity-50', cart.size === 0);
                    submitTransactionButton.disabled = cart.size === 0;
                    submitTransactionButton.classList.toggle('cursor-not-allowed', cart.size === 0);
                    submitTransactionButton.classList.toggle('opacity-60', cart.size === 0);
                    submitHelperText.textContent = cart.size === 0
                        ? 'Pilih produk dulu, lalu simpan transaksi saat keranjang sudah sesuai.'
                        : 'Kalau ada salah pilih, kamu bisa tambah, kurangi, hapus item, atau reset keranjang.';
                    updateChangePreview();
                };

                const addToCart = (productId) => {
                    const product = findProduct(productId);

                    if (!product) {
                        return;
                    }

                    const current = cart.get(productId) || {
                        product_id: product.id,
                        name: product.name,
                        price: product.price,
                        image_url: product.image_url,
                        qty: 0,
                    };

                    current.qty += 1;
                    cart.set(productId, current);
                    renderCart();
                };

                const adjustQty = (productId, delta) => {
                    const item = cart.get(productId);

                    if (!item) {
                        return;
                    }

                    item.qty += delta;

                    if (item.qty <= 0) {
                        cart.delete(productId);
                    } else {
                        cart.set(productId, item);
                    }

                    renderCart();
                };

                const removeFromCart = (productId) => {
                    cart.delete(productId);
                    renderCart();
                };

                const resetCart = () => {
                    if (cart.size === 0) {
                        return;
                    }

                    const confirmed = window.confirm('Kosongkan semua item di keranjang ini?');

                    if (!confirmed) {
                        return;
                    }

                    cart.clear();
                    paidAmountInput.value = '';
                    renderCart();
                };

                const updateChangePreview = () => {
                    const selectedMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
                    const paidAmount = Number(paidAmountInput.value || 0);

                    if (selectedMethod === 'cash') {
                        const change = Math.max(paidAmount - currentTotalAmount, 0);
                        changePreview.textContent = `Kembalian: Rp${formatRupiah(change)}`;
                    } else {
                        changePreview.textContent = 'Kembalian: Rp0';
                    }
                };

                const syncPaymentMode = () => {
                    const selectedMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';

                    if (selectedMethod === 'cash') {
                        cashPaymentBox.classList.remove('hidden');
                        qrisPaymentBox.classList.add('hidden');
                    } else {
                        cashPaymentBox.classList.add('hidden');
                        qrisPaymentBox.classList.remove('hidden');
                        paidAmountInput.value = '';
                    }

                    updateChangePreview();
                };

                const filterProducts = () => {
                    const searchTerm = productSearch.value.trim().toLowerCase();
                    const selectedCategory = categoryFilter.value;
                    const selectedGroup = groupFilter.value;
                    const selectedUnit = unitFilter.value;

                    productGrid.querySelectorAll('[data-product-card]').forEach((card) => {
                        const name = card.dataset.productName.toLowerCase();
                        const categoryId = card.dataset.categoryId;
                        const menuGroup = card.dataset.menuGroup;
                        const sellingUnit = card.dataset.sellingUnit;
                        const matchesSearch = name.includes(searchTerm);
                        const matchesCategory = selectedCategory === '' || categoryId === selectedCategory;
                        const matchesGroup = selectedGroup === '' || menuGroup === selectedGroup;
                        const matchesUnit = selectedUnit === '' || sellingUnit === selectedUnit;

                        card.classList.toggle('hidden', !matchesSearch || !matchesCategory || !matchesGroup || !matchesUnit);
                    });

                    syncGroupChipState();
                };

                productGrid.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-product-card]');

                    if (button) {
                        addToCart(Number(button.dataset.productId));
                    }
                });

                cartItems.addEventListener('click', (event) => {
                    const removeButton = event.target.closest('[data-remove]');
                    const incButton = event.target.closest('[data-inc]');
                    const decButton = event.target.closest('[data-dec]');

                    if (removeButton) {
                        removeFromCart(Number(removeButton.dataset.remove));
                    }

                    if (incButton) {
                        adjustQty(Number(incButton.dataset.inc), 1);
                    }

                    if (decButton) {
                        adjustQty(Number(decButton.dataset.dec), -1);
                    }
                });

                quickCashButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const value = button.dataset.quickCash;
                        paidAmountInput.value = value === 'exact' ? currentTotalAmount : value;
                        updateChangePreview();
                    });
                });

                resetCartButton.addEventListener('click', resetCart);
                productSearch.addEventListener('input', filterProducts);
                categoryFilter.addEventListener('change', filterProducts);
                groupFilter.addEventListener('change', filterProducts);
                unitFilter.addEventListener('change', filterProducts);
                paidAmountInput.addEventListener('input', updateChangePreview);
                paymentRadios.forEach((radio) => radio.addEventListener('change', syncPaymentMode));
                groupChips.forEach((chip) => {
                    chip.addEventListener('click', () => {
                        groupFilter.value = chip.dataset.groupChip;
                        filterProducts();
                    });
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === '/' && document.activeElement !== productSearch) {
                        event.preventDefault();
                        productSearch.focus();
                    }
                });

                initialItems.forEach((item) => {
                    const product = findProduct(item.product_id);

                    if (!product) {
                        return;
                    }

                    cart.set(item.product_id, {
                        product_id: product.id,
                        name: product.name,
                        price: product.price,
                        image_url: product.image_url,
                        qty: item.qty,
                    });
                });

                renderCart();
                syncPaymentMode();
                filterProducts();
                productSearch.focus();
            })();
        </script>
    @endif
@endsection
