@csrf

<div class="grid gap-5">
    @if (! empty($currentShift))
        <div class="rounded-[1.5rem] border border-sky-200 bg-sky-50 px-4 py-4 text-sm text-sky-700">
            Arus kas ini akan tercatat ke shift aktif yang dibuka pada {{ $currentShift->opened_at->format('d M Y, H:i') }}.
        </div>
    @endif

    <div>
        <label for="flow_date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal dan waktu</label>
        <input
            id="flow_date"
            type="datetime-local"
            name="flow_date"
            value="{{ old('flow_date', isset($cashFlow) ? $cashFlow->flow_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
            required
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
        >
        @error('flow_date')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="type" class="mb-2 block text-sm font-medium text-slate-700">Tipe arus kas</label>
        <select
            id="type"
            name="type"
            required
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
        >
            <option value="">Pilih tipe</option>
            <option value="in" @selected(old('type', $cashFlow->type ?? '') === 'in')>Kas masuk</option>
            <option value="out" @selected(old('type', $cashFlow->type ?? '') === 'out')>Kas keluar</option>
        </select>
        @error('type')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="amount" class="mb-2 block text-sm font-medium text-slate-700">Nominal</label>
        <input
            id="amount"
            type="number"
            min="0"
            step="1"
            name="amount"
            value="{{ old('amount', isset($cashFlow) ? (int) round((float) $cashFlow->amount) : '') }}"
            required
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
            placeholder="0"
        >
        @error('amount')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="source" class="mb-2 block text-sm font-medium text-slate-700">Sumber atau tujuan</label>
        <input
            id="source"
            type="text"
            name="source"
            value="{{ old('source', $cashFlow->source ?? '') }}"
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
            placeholder="Contoh: Modal awal, belanja bahan"
        >
        @error('source')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Keterangan</label>
        <textarea
            id="description"
            name="description"
            rows="5"
            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
            placeholder="Catatan tambahan"
        >{{ old('description', $cashFlow->description ?? '') }}</textarea>
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
            href="{{ route('cash-flows.index') }}"
            class="rounded-2xl border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
        >
            Kembali
        </a>
    </div>
</div>
