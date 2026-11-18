@props(['kartu'])
<section class="fase-kartu" aria-label="Ringkasan siklus">
    @foreach ($kartu as $item)
        <article class="card fase-kartu__item">
            <h2 class="fase-kartu__judul">{{ $item['judul'] }}</h2>
            <p class="fase-kartu__nilai">{{ $item['nilai'] }}</p>
            <p class="muted muted--xs">{{ $item['ket'] }}</p>
        </article>
    @endforeach
</section>