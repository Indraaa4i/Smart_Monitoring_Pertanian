@props(['daftarLahan' => collect(), 'lahan' => null, 'phase' => null, 'appName' => 'Smart Sawah'])
<header class="topbar card">
    <div class="topbar__brand">
        <span class="topbar__logo"><x-icon name="leaf" :size="20" /></span>
        <div>
            <p class="topbar__name">{{ $appName }}</p>
            <p class="topbar__sub">Dashboard pantauan sawah untuk petani</p>
        </div>
    </div>
    <div class="topbar__right">
        @if ($daftarLahan->isNotEmpty())
            <form method="get" action="{{ route('beranda') }}">
                <label class="select-btn">
                    <x-icon name="leaf" :size="15" />
                    <select name="lahan" aria-label="Pilih lahan" onchange="this.form.submit()">
                        @foreach ($daftarLahan as $item)
                            <option value="{{ $item->id }}" @selected($lahan && $lahan->id === $item->id)>{{ $item->nama_lahan }}</option>
                        @endforeach
                    </select>
                    <svg width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M1 3h10L6 10z" fill="currentColor"/></svg>
                </label>
                <noscript><button type="submit">Tampilkan</button></noscript>
            </form>
        @endif
        @if ($phase)
            <span class="pill">Hari ke-{{ $phase['day'] }} dari {{ $phase['total'] }} · {{ $phase['label'] }}</span>
        @endif
    </div>
</header>
