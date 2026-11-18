@props(['tahapan'])
<section class="card fase-seksi" aria-labelledby="fase-tahapan-judul">
    <h2 id="fase-tahapan-judul" class="fase-seksi__judul">Progres tahapan tanam</h2>
    <p class="muted muted--xs">{{ $tahapan['keterangan'] }}</p>

    <ol class="fase-tahapan">
        @foreach ($tahapan['daftar'] as $tahap)
            <li @class(['fase-tahapan__item', 'is-berlangsung' => $tahap['status'] === 'berlangsung'])>
                <h3 class="fase-tahapan__nama">{{ $tahap['nama'] }}</h3>
                <p class="muted muted--xs">{{ $tahap['meta'] }}</p>

                @if (! empty($tahap['url']))
                    <a href="{{ $tahap['url'] }}" class="btn-fase btn-fase--kecil">Lihat</a>
                @else
                    <button type="button" class="btn-fase btn-fase--kecil" aria-disabled="true"
                            title="Detail tahap segera hadir">Lihat</button>
                @endif
            </li>
        @endforeach
    </ol>
</section>