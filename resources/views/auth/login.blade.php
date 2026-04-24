@php
    $title = 'Login Kasir Booth';
@endphp

@extends('layouts.app')

@section('content')
    @php($booth = config('booth'))
    <div class="relative isolate overflow-hidden">
        <div class="absolute inset-0 soft-grid opacity-40"></div>
        <div class="absolute -left-24 top-16 h-72 w-72 rounded-full bg-orange-300/35 blur-3xl animate-float"></div>
        <div class="absolute right-0 top-0 h-96 w-96 rounded-full bg-amber-200/45 blur-3xl animate-pulse-soft"></div>

        <div class="relative mx-auto flex min-h-screen max-w-7xl items-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid w-full gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="mesh-panel shadow-panel animate-rise relative overflow-hidden rounded-[2rem] border border-white/70 p-8 sm:p-10 lg:p-12">
                    <div class="absolute right-6 top-6 rounded-full border border-orange-200/70 bg-white/70 px-4 py-2 text-xs font-medium tracking-[0.24em] text-slate-500 uppercase">
                        Booth POS
                    </div>

                    <div class="max-w-2xl">
                        <div class="mb-6 flex items-center gap-4">
                            <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-[1.75rem] border border-white/80 bg-white/85 shadow-lg shadow-orange-100/60">
                                <img src="{{ asset($booth['logo']) }}" alt="Logo {{ $booth['name'] }}" class="h-16 w-16 object-contain">
                            </div>
                            <div>
                                <p class="text-xs font-semibold tracking-[0.24em] text-slate-500 uppercase">Brand Booth</p>
                                <h2 class="font-display mt-2 text-2xl font-semibold text-slate-900">{{ $booth['name'] }}</h2>
                            </div>
                        </div>
                        <p class="mb-4 inline-flex rounded-full bg-orange-500/10 px-4 py-2 text-sm font-medium text-orange-700">
                            Dashboard kasir yang cepat, ringan, dan siap dipakai
                        </p>

                        <h1 class="font-display max-w-xl text-4xl leading-tight font-semibold text-slate-900 sm:text-5xl">
                            Kelola booth harian dengan tampilan yang lebih modern dan fokus.
                        </h1>

                        <p class="mt-6 max-w-xl text-base leading-7 text-slate-600 sm:text-lg">
                            Masuk ke panel kasir untuk memantau penjualan, transaksi QRIS, arus kas, dan produk aktif
                            dalam satu layar yang ringkas.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-4 md:grid-cols-3">
                        <div class="rounded-[1.5rem] border border-white/80 bg-white/75 p-5 shadow-lg shadow-orange-100/50">
                            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-500 text-white">
                                <span class="font-display text-lg font-semibold">24</span>
                            </div>
                            <h2 class="font-display text-lg font-semibold text-slate-900">Transaksi cepat</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Alur kasir dibuat singkat agar cocok untuk booth kecil dan jam ramai.
                            </p>
                        </div>

                        <div class="rounded-[1.5rem] border border-white/80 bg-slate-900 p-5 text-slate-50 shadow-lg shadow-slate-900/20">
                            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-400/20 text-emerald-300">
                                <span class="font-display text-lg font-semibold">QR</span>
                            </div>
                            <h2 class="font-display text-lg font-semibold">QRIS manual</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-300">
                                Tetap aman dengan status pembayaran yang bisa dikonfirmasi kasir.
                            </p>
                        </div>

                        <div class="rounded-[1.5rem] border border-white/80 bg-white/75 p-5 shadow-lg shadow-orange-100/50">
                            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-500 text-white">
                                <span class="font-display text-lg font-semibold">Rp</span>
                            </div>
                            <h2 class="font-display text-lg font-semibold text-slate-900">Kas lebih rapi</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Catat kas masuk dan keluar supaya laporan harian lebih mudah ditutup.
                            </p>
                        </div>
                    </div>

                    <div class="mt-10 rounded-[1.75rem] border border-orange-100/80 bg-white/80 p-6">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-sm font-medium tracking-[0.22em] text-slate-500 uppercase">Akun Demo</p>
                                <h3 class="font-display mt-2 text-2xl font-semibold text-slate-900">
                                    Langsung pakai data seed yang sudah tersedia
                                </h3>
                            </div>
                            <div class="rounded-full bg-emerald-500/10 px-4 py-2 text-sm font-medium text-emerald-700">
                                Password semua akun: <span class="font-semibold">password</span>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            <div class="rounded-[1.25rem] border border-slate-200/80 bg-slate-50 p-4">
                                <p class="text-xs font-semibold tracking-[0.2em] text-slate-500 uppercase">Admin</p>
                                <p class="mt-2 font-display text-lg font-semibold text-slate-900">admin@booth.test</p>
                                <p class="mt-1 text-sm text-slate-500">Untuk monitoring dan operasional booth.</p>
                            </div>
                            <div class="rounded-[1.25rem] border border-slate-200/80 bg-slate-50 p-4">
                                <p class="text-xs font-semibold tracking-[0.2em] text-slate-500 uppercase">Kasir</p>
                                <p class="mt-2 font-display text-lg font-semibold text-slate-900">kasir@booth.test</p>
                                <p class="mt-1 text-sm text-slate-500">Untuk transaksi penjualan dan input kas harian.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="animate-rise flex items-center lg:justify-end" style="animation-delay: 0.08s;">
                    <div class="mesh-panel shadow-panel w-full max-w-xl rounded-[2rem] border border-white/70 p-6 sm:p-8">
                        <div class="mb-8">
                            <div class="mb-5 flex items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-[1.35rem] bg-slate-900 p-2">
                                    <img src="{{ asset($booth['logo']) }}" alt="Logo {{ $booth['name'] }}" class="h-full w-full object-contain">
                                </div>
                                <div>
                                    <p class="text-sm font-medium tracking-[0.24em] text-slate-500 uppercase">Masuk ke aplikasi</p>
                                    <h2 class="font-display mt-2 text-3xl font-semibold text-slate-900">Login dashboard</h2>
                                </div>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-slate-600">
                                Gunakan akun kasir atau admin untuk mulai memantau performa booth hari ini.
                            </p>
                        </div>

                        @if (session('status'))
                            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                            @csrf

                            <div>
                                <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', 'kasir@booth.test') }}"
                                    required
                                    autofocus
                                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                                    placeholder="nama@booth.test"
                                >
                            </div>

                            <div>
                                <div class="mb-2 flex items-center justify-between">
                                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                                    <span class="text-xs text-slate-500">Default seed: password</span>
                                </div>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    class="w-full rounded-2xl border border-slate-200 bg-white/90 px-4 py-3.5 text-slate-900 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                                    placeholder="Masukkan password"
                                >
                            </div>

                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/70 px-4 py-3 text-sm text-slate-600">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    value="1"
                                    class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-300"
                                >
                                Ingat saya di perangkat ini
                            </label>

                            <button
                                type="submit"
                                class="font-display w-full rounded-2xl bg-slate-900 px-5 py-4 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
                            >
                                Masuk ke Dashboard
                            </button>
                        </form>

                        <div class="mt-6 rounded-[1.5rem] border border-slate-200/80 bg-slate-50/90 p-4 text-sm leading-6 text-slate-600">
                            Setelah login, kamu akan masuk ke dashboard ringkas berisi penjualan hari ini, transaksi
                            terbaru, arus kas, dan produk terlaris.
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
