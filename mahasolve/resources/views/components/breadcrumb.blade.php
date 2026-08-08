@php
    $routeName = request()->route()?->getName() ?? '';
    $pathSegments = array_filter(explode('/', request()->path()));

    $breadcrumbs = [
        ['name' => 'Beranda', 'url' => auth()->check() && auth()->user()->role === 'provider' ? route('provider.dashboard') : route('catalog.index')]
    ];

    if (request()->routeIs('catalog.*') || request()->routeIs('home')) {
        $breadcrumbs[] = ['name' => 'Katalog Layanan', 'url' => route('catalog.index')];
        if (request()->routeIs('catalog.show')) {
            $breadcrumbs[] = ['name' => 'Detail Layanan', 'url' => '#'];
        }
    } elseif (request()->routeIs('pesanan.*')) {
        $breadcrumbs[] = ['name' => 'Pesanan Saya', 'url' => route('pesanan.index')];
        if (request()->routeIs('pesanan.show')) {
            $breadcrumbs[] = ['name' => 'Detail & Progress Pekerjaan', 'url' => '#'];
        } elseif (request()->routeIs('pesanan.struk')) {
            $breadcrumbs[] = ['name' => 'Struk Pembayaran', 'url' => '#'];
        }
    } elseif (request()->routeIs('negosiasi.*')) {
        $breadcrumbs[] = ['name' => 'Pesanan Saya', 'url' => route('pesanan.index')];
        $breadcrumbs[] = ['name' => 'Ruang Negosiasi Chat', 'url' => '#'];
    } elseif (request()->routeIs('mahasiswa.request.*')) {
        $breadcrumbs[] = ['name' => 'Request Custom', 'url' => route('mahasiswa.request.create')];
    } elseif (request()->routeIs('provider.dashboard')) {
        $breadcrumbs[] = ['name' => 'Portal Provider', 'url' => route('provider.dashboard')];
        $breadcrumbs[] = ['name' => 'Dashboard Overview', 'url' => route('provider.dashboard')];
    } elseif (request()->routeIs('order') || request()->routeIs('provider.order')) {
        $breadcrumbs[] = ['name' => 'Portal Provider', 'url' => route('provider.dashboard')];
        $breadcrumbs[] = ['name' => 'Order Masuk & Negosiasi', 'url' => route('order')];
    } elseif (request()->routeIs('my-service') || request()->routeIs('provider.my-service')) {
        $breadcrumbs[] = ['name' => 'Portal Provider', 'url' => route('provider.dashboard')];
        $breadcrumbs[] = ['name' => 'Kelola Layanan Saya', 'url' => route('my-service')];
    } elseif (request()->routeIs('provider.review') || request()->routeIs('review.*')) {
        $breadcrumbs[] = ['name' => 'Portal Provider', 'url' => route('provider.dashboard')];
        $breadcrumbs[] = ['name' => 'Riwayat & Ulasan', 'url' => '#'];
    } else {
        foreach ($pathSegments as $segment) {
            $breadcrumbs[] = [
                'name' => ucfirst(str_replace('-', ' ', $segment)),
                'url' => '#'
            ];
        }
    }
@endphp

<nav aria-label="Breadcrumb" class="mb-6 flex items-center gap-2 text-xs font-semibold text-slate-500 bg-white/80 backdrop-blur-sm border border-slate-200/80 px-4 py-2.5 rounded-2xl shadow-sm overflow-x-auto">
    <a href="{{ $breadcrumbs[0]['url'] }}" class="flex items-center gap-1.5 hover:text-indigo-600 transition shrink-0">
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>{{ $breadcrumbs[0]['name'] }}</span>
    </a>

    @foreach (array_slice($breadcrumbs, 1) as $index => $item)
        <svg class="w-3 h-3 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>

        @if ($loop->last)
            <span class="text-indigo-600 font-bold bg-indigo-50 px-2.5 py-0.5 rounded-xl border border-indigo-100/80 shrink-0">
                {{ $item['name'] }}
            </span>
        @else
            <a href="{{ $item['url'] }}" class="hover:text-indigo-600 transition shrink-0">
                {{ $item['name'] }}
            </a>
        @endif
    @endforeach
</nav>
