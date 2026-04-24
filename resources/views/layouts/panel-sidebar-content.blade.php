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
            <a href="{{ route('booth-settings.edit') }}" class="block rounded-xl bg-white px-4 py-3 text-slate-700 transition hover:bg-orange-100">
                Pengaturan booth
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
