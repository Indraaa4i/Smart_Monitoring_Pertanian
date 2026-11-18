@props(['siklus'])
@php
    $detail = $siklus['detail'];
@endphp
<dialog id="modal-detail-siklus" class="modal-siklus" aria-labelledby="modal-detail-siklus-judul">
    <div class="modal-siklus__isi">
        <header>
            <h2 id="modal-detail-siklus-judul" class="modal-siklus__judul">{{ $detail['judul'] }}</h2>
            <p class="modal-siklus__sub">{{ $detail['subjudul'] }}</p>
        </header>

        <dl class="modal-siklus__kolom">
            @foreach ($detail['kolom'] as $kolom)
                <div class="kolom-detail">
                    <dt>{{ $kolom['label'] }}</dt>
                    <dd class="kolom-detail__nilai">{{ $kolom['nilai'] }}</dd>
                </div>
            @endforeach
        </dl>

        @if ($detail['catatan'])
            <p class="modal-siklus__catatan">{{ $detail['catatan'] }}</p>
        @endif

        <div class="modal-siklus__tahap">
            @foreach ($detail['tahap'] as $tahap)
                @if ($tahap['url'])
                    <a href="{{ $tahap['url'] }}" class="btn-fase btn-fase--penuh">{{ $tahap['nama'] }}</a>
                @else
                    <button type="button" class="btn-fase btn-fase--penuh" aria-disabled="true"
                            title="Detail {{ strtolower($tahap['nama']) }} belum tersedia">{{ $tahap['nama'] }}</button>
                @endif
            @endforeach
        </div>

        {{-- method="dialog": menutup modal tanpa JavaScript --}}
        <form method="dialog">
            <button type="submit" class="btn-fase btn-fase--penuh">Tutup</button>
        </form>
    </div>
</dialog>