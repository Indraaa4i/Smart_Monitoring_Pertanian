{{-- Kartu satu parameter tanah. Saat ini hanya keadaan offline (belum ada pembacaan). --}}
@props(['parameter'])

<article class="soil-card soil-card--offline">
    <header class="soil-card__head">
        <h2 class="soil-card__label">
            <x-icon :name="$parameter['ikon']" :size="18" />
            {{ $parameter['label'] }}
        </h2>
        <x-status-pill status="offline" label="Offline" />
    </header>

    <p class="soil-card__value" aria-label="Belum ada nilai">—</p>
    <h3 class="soil-card__title">Belum ada pembacaan</h3>
    <p class="soil-card__hint">Nilai akan muncul saat sensor terhubung.</p>

    <div class="soil-meter"
         style="--zona-awal: {{ $parameter['zona_awal'] }}%; --zona-lebar: {{ $parameter['zona_lebar'] }}%;"
         role="img"
         aria-label="{{ $parameter['label'] }}: belum ada pembacaan. {{ $parameter['batas'] }}">
        <span class="soil-meter__zone"></span>
    </div>
    <div class="soil-card__scale">
        <span>{{ $parameter['skala'][0] }}</span>
        <strong>{{ $parameter['batas'] }}</strong>
        <span>{{ $parameter['skala'][1] }}</span>
    </div>

    <p class="soil-card__time">Terakhir diperbarui: belum ada data</p>
</article>