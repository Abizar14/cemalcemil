@php
    $title = 'Pengaturan Booth';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Pengaturan Booth')
@section('panel-title', 'Identitas booth dan struk')
@section('panel-description', 'Atur nama booth, alamat, telepon, footer struk, ukuran kertas, dan logo agar laporan serta struk selalu konsisten.')

@section('panel-actions')
    <a
        href="{{ route('dashboard') }}"
        class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
    >
        Kembali
    </a>
@endsection

@section('panel-content')
    <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
        <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6">
            <form method="POST" action="{{ route('booth-settings.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama booth</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $setting->name) }}"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="mb-2 block text-sm font-medium text-slate-700">Alamat singkat</label>
                        <input
                            id="address"
                            type="text"
                            name="address"
                            value="{{ old('address', $setting->address) }}"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                        >
                        @error('address')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="mb-2 block text-sm font-medium text-slate-700">Kota / area</label>
                        <input
                            id="city"
                            type="text"
                            name="city"
                            value="{{ old('city', $setting->city) }}"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                        >
                        @error('city')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">No. telepon</label>
                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            value="{{ old('phone', $setting->phone) }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                        >
                        @error('phone')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="receipt_footer" class="mb-2 block text-sm font-medium text-slate-700">Footer struk</label>
                        <textarea
                            id="receipt_footer"
                            name="receipt_footer"
                            rows="3"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                        >{{ old('receipt_footer', $setting->receipt_footer) }}</textarea>
                        @error('receipt_footer')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="receipt_paper" class="mb-2 block text-sm font-medium text-slate-700">Ukuran paper thermal</label>
                        <select
                            id="receipt_paper"
                            name="receipt_paper"
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                        >
                            <option value="80" @selected(old('receipt_paper', $setting->receipt_paper) === '80')>80 mm</option>
                            <option value="58" @selected(old('receipt_paper', $setting->receipt_paper) === '58')>58 mm</option>
                        </select>
                        @error('receipt_paper')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="logo" class="mb-2 block text-sm font-medium text-slate-700">Logo booth</label>
                        <input
                            id="logo"
                            type="file"
                            name="logo"
                            accept="image/*"
                            class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition file:mr-4 file:rounded-xl file:border-0 file:bg-orange-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-orange-700 hover:file:bg-orange-200 focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                        >
                        @error('logo')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/70 px-4 py-3 text-sm text-slate-600">
                    <input
                        type="checkbox"
                        name="remove_logo"
                        value="1"
                        @checked((bool) old('remove_logo'))
                        class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-300"
                    >
                    Hapus logo custom dan pakai logo default
                </label>

                <button
                    type="submit"
                    class="font-display rounded-2xl bg-slate-900 px-5 py-4 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
                >
                    Simpan Pengaturan Booth
                </button>
            </form>
        </section>

        <aside class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6" style="animation-delay: 0.05s;">
            <p class="text-sm font-medium tracking-[0.2em] text-slate-500 uppercase">Preview Identitas</p>
            <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">Tampilan yang dipakai sistem</h2>

            <div class="mt-5 rounded-[1.75rem] border border-slate-200/80 bg-white/85 p-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-[1.5rem] border border-slate-200 bg-slate-50">
                        <img src="{{ asset($booth['logo']) }}" alt="Logo {{ $booth['name'] }}" class="h-16 w-16 object-contain">
                    </div>
                    <div>
                        <p class="font-display text-2xl font-semibold text-slate-900">{{ $booth['name'] }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $booth['address'] }}, {{ $booth['city'] }}</p>
                        <p class="text-sm text-slate-500">Telp: {{ $booth['phone'] }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-[1.5rem] border border-orange-100 bg-orange-50 p-4 text-sm text-slate-700">
                    <p class="font-semibold text-orange-800">Footer struk</p>
                    <p class="mt-2 leading-6">{{ $booth['receipt_footer'] }}</p>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Ukuran thermal</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $booth['receipt_paper'] }} mm</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200/80 bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Dipakai di</p>
                        <p class="mt-2 font-semibold text-slate-900">Panel, laporan, struk, thermal</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
