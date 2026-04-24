@csrf

<div class="grid gap-5">
    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama kategori</label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $category->name ?? '') }}"
            required
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
            placeholder="Contoh: Minuman"
        >
        @error('name')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="5"
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
            placeholder="Deskripsi singkat kategori"
        >{{ old('description', $category->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <button
            type="submit"
            class="font-display rounded-2xl bg-slate-900 px-5 py-4 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
        >
            {{ $submitLabel }}
        </button>
        <a
            href="{{ route('categories.index') }}"
            class="rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
        >
            Kembali
        </a>
    </div>
</div>
