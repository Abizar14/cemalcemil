@csrf

<div class="grid gap-5">
    <div>
        <label for="category_id" class="mb-2 block text-sm font-medium text-slate-700">Kategori</label>
        <select
            id="category_id"
            name="category_id"
            required
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
        >
            <option value="">Pilih kategori</option>
            @foreach ($categories as $categoryOption)
                <option value="{{ $categoryOption->id }}" @selected((int) old('category_id', $product->category_id ?? 0) === $categoryOption->id)>
                    {{ $categoryOption->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama produk</label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $product->name ?? '') }}"
            required
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
            placeholder="Contoh: Es Teh"
        >
        @error('name')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="price" class="mb-2 block text-sm font-medium text-slate-700">Harga</label>
        <input
            id="price"
            type="number"
            min="0"
            step="0.01"
            name="price"
            value="{{ old('price', isset($product) ? number_format((float) $product->price, 2, '.', '') : '') }}"
            required
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
            placeholder="0"
        >
        @error('price')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 lg:grid-cols-[0.95fr_1.05fr]">
        <div>
            <label for="image" class="mb-2 block text-sm font-medium text-slate-700">Gambar produk</label>
            <input
                id="image"
                type="file"
                name="image"
                accept="image/*"
                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition file:mr-4 file:rounded-xl file:border-0 file:bg-orange-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-orange-700 hover:file:bg-orange-200 focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
            >
            <p class="mt-2 text-sm text-slate-500">Upload gambar menu agar kartu produk di kasir tampil lebih menarik.</p>
            @error('image')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror

            @if (isset($product) && $product->image_path)
                <label class="mt-4 flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/70 px-4 py-3 text-sm text-slate-600">
                    <input
                        type="checkbox"
                        name="remove_image"
                        value="1"
                        @checked((bool) old('remove_image'))
                        class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-300"
                    >
                    Hapus gambar saat ini
                </label>
            @endif
        </div>

        <div class="rounded-[1.75rem] border border-slate-200/80 bg-white/80 p-4">
            <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Preview</p>
            <div class="mt-3 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-slate-100">
                <img
                    src="{{ old('remove_image') ? asset('images/products/placeholder.svg') : ($product->image_url ?? asset('images/products/placeholder.svg')) }}"
                    alt="{{ old('name', $product->name ?? 'Preview produk') }}"
                    class="aspect-[4/3] w-full object-cover"
                >
            </div>
            <div class="mt-4">
                <p class="font-display text-xl font-semibold text-slate-900">{{ old('name', $product->name ?? 'Nama menu') }}</p>
                <p class="mt-2 text-sm text-slate-500">
                    {{ old('price') !== null || isset($product) ? 'Rp'.number_format((float) old('price', $product->price ?? 0), 0, ',', '.') : 'Harga akan tampil di sini' }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/70 px-4 py-3 text-sm text-slate-600 lg:col-span-3">
            <input
                type="checkbox"
                name="track_stock"
                value="1"
                @checked((bool) old('track_stock', $product->track_stock ?? false))
                class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-300"
            >
            Aktifkan stok sederhana untuk produk ini
        </label>

        <div>
            <label for="stock_quantity" class="mb-2 block text-sm font-medium text-slate-700">Jumlah stok</label>
            <input
                id="stock_quantity"
                type="number"
                min="0"
                step="1"
                name="stock_quantity"
                value="{{ old('stock_quantity', $product->stock_quantity ?? '') }}"
                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                placeholder="Kosongkan jika tidak pakai stok"
            >
            @error('stock_quantity')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="stock_alert_threshold" class="mb-2 block text-sm font-medium text-slate-700">Batas stok menipis</label>
            <input
                id="stock_alert_threshold"
                type="number"
                min="0"
                step="1"
                name="stock_alert_threshold"
                value="{{ old('stock_alert_threshold', $product->stock_alert_threshold ?? 3) }}"
                class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                placeholder="3"
            >
            @error('stock_alert_threshold')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-[1.5rem] border border-slate-200/80 bg-white/80 p-4 text-sm text-slate-600">
            Jika stok diaktifkan dan jumlahnya `0`, produk akan otomatis nonaktif dari layar kasir.
        </div>
    </div>

    <label class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/70 px-4 py-3 text-sm text-slate-600">
        <input
            type="checkbox"
            name="is_active"
            value="1"
            @checked((bool) old('is_active', $product->is_active ?? true))
            class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-300"
        >
        Produk aktif dan bisa dipakai di kasir
    </label>
    @error('is_active')
        <p class="text-sm text-rose-600">{{ $message }}</p>
    @enderror

    <div class="flex flex-col gap-3 sm:flex-row">
        <button
            type="submit"
            class="font-display rounded-2xl bg-slate-900 px-5 py-4 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
        >
            {{ $submitLabel }}
        </button>
        <a
            href="{{ route('products.index') }}"
            class="rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
        >
            Kembali
        </a>
    </div>
</div>
