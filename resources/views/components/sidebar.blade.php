@php
    $items = [
        ['route' => 'beranda', 'label' => 'Beranda', 'icon' => 'home'],
        ['route' => 'kondisi-tanah', 'label' => 'Kondisi Tanah', 'icon' => 'droplet'],
        ['route' => 'rekomendasi', 'label' => 'Rekomendasi', 'icon' => 'bulb'],
        ['route' => 'riwayat', 'label' => 'Riwayat', 'icon' => 'clock'],
    ];
@endphp
<aside class="sidebar card" data-sidebar>
    <button type="button" class="sidebar__toggle" data-sidebar-toggle aria-expanded="false" aria-controls="nav-list">
        <span class="sidebar__menu-icon"><x-icon name="menu" /></span>
        <span>Navigasi</span>
    </button>
    <nav id="nav-list" class="sidebar__list" aria-label="Navigasi utama">
        @foreach ($items as $item)
            <a href="{{ route($item['route']) }}"
               @class(['nav-item', 'is-active' => request()->routeIs($item['route'])])
               @if (request()->routeIs($item['route'])) aria-current="page" @endif>
                <x-icon :name="$item['icon']" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
