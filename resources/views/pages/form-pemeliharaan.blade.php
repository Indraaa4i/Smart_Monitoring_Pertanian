{{--
    Form + log catatan pemeliharaan.
    Pakai di resources/views/pages/pemeliharaan.blade.php dengan:
    @include('pages.partials.form-pemeliharaan')
--}}
@php
    $jenisOpsi = [
        'pemupukan' => 'Pemupukan',
        'penyiangan' => 'Penyiangan',
        'pengairan' => 'Pengairan',
        'pestisida' => 'Pestisida / pengendalian hama',
        'lainnya' => 'Lainnya',
    ];
@endphp

<section class="card" id="form-pemeliharaan" aria-labelledby="form-pemeliharaan-judul">
    <h2 id="form-pemeliharaan-judul">Catat pemeliharaan</h2>
    <p class="muted">Siklus MH-2026-A · Blok A</p>

    @if ($errors->any())
        <div role="alert" class="muted" style="color:#b42318;margin:.75rem 0">
            <ul style="margin:0;padding-left:1.1rem">
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('fase-tanam.pemeliharaan.store') }}"
          style="display:grid;gap:.75rem;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
        @csrf

        <label style="display:grid;gap:.25rem">
            <span>Tanggal</span>
            <input type="date" name="tanggal" required
                   max="{{ now('Asia/Jakarta')->toDateString() }}"
                   value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}">
        </label>

        <label style="display:grid;gap:.25rem">
            <span>Jenis pemeliharaan</span>
            <select name="jenis" required>
                @foreach ($jenisOpsi as $nilai => $label)
                    <option value="{{ $nilai }}" @selected(old('jenis') === $nilai)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label style="display:grid;gap:.25rem">
            <span>Bahan (opsional)</span>
            <input type="text" name="bahan" maxlength="100" value="{{ old('bahan') }}" placeholder="Contoh: Urea">
        </label>

        <label style="display:grid;gap:.25rem">
            <span>Dosis (opsional)</span>
            <input type="text" name="dosis" maxlength="100" value="{{ old('dosis') }}" placeholder="Contoh: 100 kg/ha">
        </label>

        <label style="display:grid;gap:.25rem">
            <span>Tinggi air, cm (opsional)</span>
            <input type="number" name="tinggi_air" min="0" max="100" step="0.1" value="{{ old('tinggi_air') }}">
        </label>

        <label style="display:grid;gap:.25rem;grid-column:1/-1">
            <span>Catatan (opsional)</span>
            <textarea name="catatan" rows="3" maxlength="500">{{ old('catatan') }}</textarea>
        </label>

        <div style="grid-column:1/-1">
            <button type="submit" class="btn-fase btn-fase--utama">Simpan catatan</button>
        </div>
    </form>
</section>

<section class="card" id="log-pemeliharaan" aria-labelledby="log-pemeliharaan-judul">
    <h2 id="log-pemeliharaan-judul">Catatan pemeliharaan</h2>

    @if (empty($logPemeliharaan))
        <p class="muted">Belum ada catatan pemeliharaan.</p>
    @else
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="text-align:left">
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Bahan</th>
                        <th>Dosis</th>
                        <th>Tinggi air</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logPemeliharaan as $baris)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($baris['tanggal'])->locale('id')->translatedFormat('j M Y') }}</td>
                            <td>{{ $jenisOpsi[$baris['jenis']] ?? ucfirst($baris['jenis']) }}</td>
                            <td>{{ $baris['bahan'] ?? '-' }}</td>
                            <td>{{ $baris['dosis'] ?? '-' }}</td>
                            <td>{{ isset($baris['tinggi_air']) && $baris['tinggi_air'] !== '' ? $baris['tinggi_air'].' cm' : '-' }}</td>
                            <td>{{ $baris['catatan'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
