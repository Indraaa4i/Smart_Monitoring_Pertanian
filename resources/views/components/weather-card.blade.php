@props(['weather' => null])
<article class="card weather">
    <header class="metric__head">
        <h2 class="metric__title">Cuaca hari ini</h2>
        <span class="muted muted--xs">BMKG</span>
    </header>

    @if ($weather)
        <div class="weather__main">
            <span class="weather__icon"><x-icon :name="$weather['icon']" :size="26" /></span>
            <div>
                <p class="weather__temp">{{ $weather['temp'] }}</p>
                <p class="muted">{{ $weather['condition'] }}</p>
            </div>
        </div>
        <p class="weather__rain"><x-icon name="cloud-rain" :size="15" /> {{ $weather['rain'] }}</p>
        <p class="muted">{{ $weather['note'] }}</p>
    @else
        <p class="muted">Data cuaca belum tersedia. Pastikan kolom kode_wilayah pada lahan sudah diisi dan koneksi internet server aktif.</p>
    @endif

    <p class="weather__foot">Sumber: BMKG · Cuaca hanya informasi pendukung.</p>
</article>
