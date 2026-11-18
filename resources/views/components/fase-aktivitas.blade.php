@props(['aktivitas'])
<section class="card fase-seksi" aria-labelledby="fase-aktivitas-judul">
    <h2 id="fase-aktivitas-judul" class="fase-seksi__judul">Aktivitas terdekat</h2>
    <p class="muted muted--xs">{{ $aktivitas['keterangan'] }}</p>

    <div class="fase-tabel-bungkus">
        <table class="fase-tabel">
            <thead>
                <tr>
                    <th scope="col">Kegiatan</th>
                    <th scope="col">Lahan &amp; waktu</th>
                    <th scope="col">Prioritas</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($aktivitas['daftar'] as $baris)
                    <tr>
                        <th scope="row" class="fase-tabel__kegiatan">{{ $baris['kegiatan'] }}</th>
                        <td>{{ $baris['lahan'] }}</td>
                        <td><span class="lencana-fase lencana-fase--{{ $baris['prioritas']['warna'] }}">{{ $baris['prioritas']['teks'] }}</span></td>
                        <td><span class="lencana-fase lencana-fase--{{ $baris['status']['warna'] }}">{{ $baris['status']['teks'] }}</span></td>
                        <td>
                            @if (! empty($baris['url']))
                                <a href="{{ $baris['url'] }}" class="btn-fase btn-fase--kecil">{{ $baris['aksi'] }}</a>
                            @else
                                <button type="button" class="btn-fase btn-fase--kecil" aria-disabled="true"
                                        title="Segera hadir">{{ $baris['aksi'] }}</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>