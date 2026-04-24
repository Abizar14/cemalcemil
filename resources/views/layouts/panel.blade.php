@extends('layouts.app')

@section('content')
    @php
        $booth = config('booth');
        $currentUser = auth()->user();
        $isKasirOnly = $currentUser?->isKasir() ?? false;
        $menu = $isKasirOnly
            ? [
                ['label' => 'Kasir', 'route' => 'transactions.create'],
            ]
            : [
                ['label' => 'Dashboard', 'route' => 'dashboard'],
                ['label' => 'Shift', 'route' => 'shifts.index'],
                ['label' => 'Transaksi', 'route' => 'transactions.index'],
                ['label' => 'Laporan', 'route' => 'reports.daily'],
                ['label' => 'Kategori', 'route' => 'categories.index'],
                ['label' => 'Produk', 'route' => 'products.index'],
                ['label' => 'Arus Kas', 'route' => 'cash-flows.index'],
            ];
    @endphp

    <div class="relative isolate min-h-screen overflow-hidden">
        <div class="absolute inset-0 soft-grid opacity-35"></div>
        <div class="absolute left-10 top-8 h-72 w-72 rounded-full bg-orange-300/30 blur-3xl"></div>
        <div class="absolute right-0 top-24 h-80 w-80 rounded-full bg-amber-200/35 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="{{ $isKasirOnly ? 'space-y-6' : 'grid gap-6 xl:grid-cols-[280px_minmax(0,1fr)]' }}">
                @unless ($isKasirOnly)
                <aside class="animate-rise xl:sticky xl:top-6 xl:self-start">
                    <div class="mesh-panel shadow-panel overflow-hidden rounded-[2rem] border border-white/70 p-5">
                        <div class="rounded-[1.5rem] bg-slate-900 p-5 text-white">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-[1.35rem] bg-white/10 ring-1 ring-white/10">
                                    <img src="{{ asset($booth['logo']) }}" alt="Logo {{ $booth['name'] }}" class="h-14 w-14 object-contain">
                                </div>
                                <div>
                                    <p class="text-xs font-semibold tracking-[0.22em] text-slate-300 uppercase">{{ $booth['name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-400">Panel operasional booth</p>
                                </div>
                            </div>
                            <h1 class="font-display mt-3 text-2xl font-semibold">{{ $currentUser->name }}</h1>
                            <p class="mt-2 text-sm text-slate-300">{{ $currentUser->email }}</p>
                            <p class="mt-2 inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase text-slate-200">
                                {{ $currentUser->role }}
                            </p>
                        </div>

                        <nav class="mt-5 space-y-2">
                            @foreach ($menu as $item)
                                @php
                                    $isActive = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route']));
                                @endphp
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="flex items-center justify-between rounded-[1.25rem] px-4 py-3 text-sm font-medium transition {{ $isActive ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'bg-white/70 text-slate-700 hover:bg-white' }}"
                                >
                                    <span>{{ $item['label'] }}</span>
                                    <span class="text-xs {{ $isActive ? 'text-slate-300' : 'text-slate-400' }}">Open</span>
                                </a>
                            @endforeach
                        </nav>

                        <div class="mt-5 rounded-[1.5rem] border border-orange-100 bg-orange-50 p-4">
                            <p class="text-xs font-semibold tracking-[0.18em] text-orange-700 uppercase">Akses Cepat</p>
                            <div class="mt-3 space-y-2 text-sm">
                                <a href="{{ route('transactions.create') }}" class="block rounded-xl bg-white px-4 py-3 text-slate-700 transition hover:bg-orange-100">
                                    Buka kasir
                                </a>
                                @if ($currentUser->isAdmin())
                                    <a href="{{ route('reports.daily') }}" class="block rounded-xl bg-white px-4 py-3 text-slate-700 transition hover:bg-orange-100">
                                        Lihat laporan harian
                                    </a>
                                    <a href="{{ route('categories.create') }}" class="block rounded-xl bg-white px-4 py-3 text-slate-700 transition hover:bg-orange-100">
                                        Tambah kategori baru
                                    </a>
                                    <a href="{{ route('products.create') }}" class="block rounded-xl bg-white px-4 py-3 text-slate-700 transition hover:bg-orange-100">
                                        Tambah produk baru
                                    </a>
                                    <a href="{{ route('cash-flows.create') }}" class="block rounded-xl bg-white px-4 py-3 text-slate-700 transition hover:bg-orange-100">
                                        Input arus kas
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </aside>
                @endunless

                <main class="space-y-6">
                    @if ($isKasirOnly)
                        <div class="mesh-panel shadow-panel animate-rise rounded-[2rem] border border-white/70 p-4 sm:p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-[1.35rem] bg-white/80 ring-1 ring-slate-200/70">
                                        <img src="{{ asset($booth['logo']) }}" alt="Logo {{ $booth['name'] }}" class="h-14 w-14 object-contain">
                                    </div>
                                    <div>
                                    <p class="text-xs font-semibold tracking-[0.22em] text-slate-500 uppercase">Kasir Mode</p>
                                    <p class="font-display mt-2 text-xl font-semibold text-slate-900">{{ $currentUser->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $currentUser->email }}</p>
                                    </div>
                                </div>
                                <div class="inline-flex rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white">
                                    Akses kasir
                                </div>
                            </div>
                        </div>
                    @endif

                    <header class="mesh-panel shadow-panel animate-rise rounded-[2rem] border border-white/70 p-5 sm:p-6 lg:p-8">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-3xl">
                                <p class="text-sm font-medium tracking-[0.24em] text-slate-500 uppercase">
                                    @yield('panel-eyebrow', 'Panel Operasional')
                                </p>
                                <h1 class="font-display mt-3 text-3xl font-semibold text-slate-900 sm:text-4xl">
                                    @yield('panel-title')
                                </h1>
                                <p class="mt-4 text-sm leading-7 text-slate-600 sm:text-base">
                                    @yield('panel-description')
                                </p>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                @yield('panel-actions')
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="font-display rounded-[1.5rem] bg-slate-900 px-5 py-4 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-600"
                                    >
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </header>

                    @if (session('status'))
                        <div class="animate-rise rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->has('delete'))
                        <div class="animate-rise rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                            {{ $errors->first('delete') }}
                        </div>
                    @endif

                    @yield('panel-content')
                </main>
            </div>
        </div>
    </div>
@endsection
