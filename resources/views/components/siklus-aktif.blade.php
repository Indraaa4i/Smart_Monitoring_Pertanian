@props(['siklus'])
<section class="card siklus-aktif" aria-labelledby="siklus-aktif-judul">
    <h2 id="siklus-aktif-judul" class="siklus-aktif__judul">{{ $siklus['judul'] }}</h2>
    <p class="muted">{{ $siklus['ringkas'] }}</p>
    <p><span class="lencana-hijau">{{ $siklus['lencana'] }}</span></p>

    <p class="siklus-aktif__progres">{{ $siklus['progres']['teks'] }}</p>
    <div class="bar" role="progressbar" aria-label="Progres tahapan tanam"
         aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $siklus['progres']['persen'] }}">
        <span class="bar__isi" style="width: {{ $siklus['progres']['persen'] }}%"></span>
    </div>

    <div class="siklus-aktif__aksi">
        <button type="button" class="btn-fase"
                data-modal-buka="modal-detail-siklus"
                aria-haspopup="dialog" aria-controls="modal-detail-siklus">Detail siklus</button>

        @if ($siklus['aksi']['catat_pemeliharaan'])
            <a href="{{ $siklus['aksi']['catat_pemeliharaan'] }}" class="btn-fase btn-fase--utama">Catat pemeliharaan</a>
        @else
            <button type="button" class="btn-fase btn-fase--utama" aria-disabled="true"
                    title="Pencatatan pemeliharaan belum tersedia">Catat pemeliharaan</button>
        @endif
    </div>
</section>

<x-modal-detail-siklus :siklus="$siklus" />