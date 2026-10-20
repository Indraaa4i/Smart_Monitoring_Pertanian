@props(['items'])
<section class="card actions">
    <header class="card__head">
        <h2 class="card__title">Apa yang perlu Pak Tani lakukan?</h2>
        <a href="{{ route('rekomendasi') }}" class="link">Lihat saran <x-icon name="arrow-up-right" :size="12" /></a>
    </header>
    <ul class="actions__list">
        @foreach ($items as $item)
            <li class="actions__item">
                <span class="actions__icon"><x-icon :name="$item['icon']" :size="16" /></span>
                <div>
                    <p class="actions__title">{{ $item['title'] }}</p>
                    <p class="muted">{{ $item['text'] }}</p>
                </div>
            </li>
        @endforeach
    </ul>
    <p class="weather__foot">Saran berdasarkan batas contoh · Sesuaikan dengan kondisi sawah.</p>
</section>
