@props(['label', 'day', 'total', 'tip'])
<section class="card phase">
    <div class="phase__top">
        <div>
            <p class="phase__label">{{ $label }}</p>
            <h2 class="phase__title">Hari ke-{{ $day }} dari {{ $total }}</h2>
        </div>
        <a href="{{ route('rekomendasi') }}" class="link">Lihat saran <x-icon name="arrow-up-right" :size="12" /></a>
    </div>
    <p class="phase__tip">{{ $tip }}</p>
    <div class="steps" role="img" aria-label="Hari ke-{{ $day }} dari {{ $total }}">
        @for ($i = 1; $i <= $total; $i++)
            <span @class(['steps__dot', 'is-done' => $i < $day, 'is-current' => $i === $day])></span>
        @endfor
    </div>
</section>
