@extends('layouts.app')

@section('title', 'Penanaman')
@section('tanpa-catatan', '1')

@section('content')
<style>
    .pn-card{background:#fff;border:1px solid #e6ece8;border-radius:16px;padding:20px;margin-top:16px}
    .pn-card h2{font-size:18px;color:#14352a;margin:0 0 4px}
    .pn-muted{font-size:12px;color:#6b7c74;margin:0 0 14px}
    .pn-banner{background:#e4f5e8;border-radius:16px;padding:22px;margin-top:16px}
    .pn-banner h2{font-size:22px;color:#14352a;margin:0 0 8px}
    .pn-banner p{font-size:14px;color:#4b5d54;margin:0 0 14px}
    .pn-bar{display:flex;align-items:center;gap:12px;margin-top:16px}
    .pn-select{padding:10px 16px;border:1px solid #e6ece8;border-radius:12px;background:#fff;font-weight:600;font-size:13px}
    .pn-badge{background:#d9f5e0;color:#1b7a4a;font-size:12px;font-weight:600;padding:4px 10px;border-radius:999px}
    .pn-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 20px}
    .pn-grid .full{grid-column:1 / -1}
    .pn-grid label{display:block;font-size:13px;font-weight:600;color:#14352a;margin-bottom:6px}
    .pn-grid input,.pn-grid select{width:100%;padding:12px 14px;border:1px solid #e6ece8;border-radius:10px;font-size:14px;box-sizing:border-box;background:#fff}
    .pn-btn{display:inline-block;padding:10px 18px;border-radius:10px;border:1px solid #e6ece8;background:#fff;font-weight:600;font-size:13px;color:#14352a;text-decoration:none;cursor:pointer}
    .pn-btn.primary{background:#1b7a4a;border-color:#1b7a4a;color:#fff}
    .pn-actions{margin-top:16px;display:flex;gap:10px}
    .pn-table{width:100%;border-collapse:collapse;font-size:13px}
    .pn-table th{background:#f3f6f4;text-align:left;padding:10px 12px;color:#6b7c74;font-weight:600}
    .pn-table td{padding:14px 12px;border-bottom:1px solid #e6ece8;color:#14352a}
    @media(max-width:768px){.pn-grid{grid-template-columns:1fr}}
</style>

<header class="halaman-kepala">
    <div>
        <p class="remah">Fase Tanam / Penanaman</p>
        <h1 class="halaman-kepala__judul">Penanaman</h1>
        <p class="muted">Catat pelaksanaan pindah tanam di setiap blok lahan.</p>
        <p class="muted muted--xs">{{ $waktu }}</p>
    </div>
    @if ($contoh)
        <span class="lencana-hijau">Data contoh</span>
    @endif
</header>

<x-fase-tab :tab="$tab" />

<div class="pn-bar">
    <span class="pn-select">{{ $penanaman['siklus'] }} · {{ $penanaman['blok'] }}</span>
    <span class="pn-badge">Penanaman selesai</span>
</div>

<div class="pn-banner">
    <h2>{{ $penanaman['blok'] }} sudah ditanam pada {{ $penanaman['tanggal']->translatedFormat('j M Y') }}</h2>
    <p>{{ $penanaman['varietas'] }} · {{ $penanaman['luas'] }} ha · {{ $penanaman['metode'] }}
       · Umur tanaman hari ini: {{ $penanaman['hst'] }} HST</p>
    <a href="#" class="pn-btn primary">Lanjut ke pemeliharaan</a>
</div>

<div class="pn-card">
    <h2>Pencatatan penanaman</h2>
    <p class="pn-muted">Data contoh pelaksanaan pindah tanam pada {{ $penanaman['blok'] }}.</p>

    <form onsubmit="return false">
        <div class="pn-grid">
            <div>
                <label>Lahan / siklus *</label>
                <select><option>{{ $penanaman['blok'] }} · {{ $penanaman['siklus'] }}</option></select>
            </div>
            <div>
                <label>Tanggal penanaman *</label>
                <input type="date" value="{{ $penanaman['tanggal']->format('Y-m-d') }}">
            </div>
            <div>
                <label>Luas ditanam (ha) *</label>
                <input type="text" value="{{ $penanaman['luas'] }}">
            </div>
            <div>
                <label>Varietas padi *</label>
                <select>
                    @foreach ($varietas as $v)
                        <option @selected($v === $penanaman['varietas'])>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Metode penanaman *</label>
                <select>
                    @foreach ($metode as $m)
                        <option @selected($m === $penanaman['metode'])>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Jumlah bibit (batang) *</label>
                <input type="text" value="{{ number_format($penanaman['jumlah_bibit'], 0, ',', '.') }}">
            </div>
            <div>
                <label>Jarak tanam</label>
                <input type="text" value="{{ $penanaman['jarak_tanam'] }}">
            </div>
            <div>
                <label>Umur bibit saat ditanam</label>
                <input type="text" value="{{ $penanaman['umur_bibit'] }} hari">
            </div>
            <div class="full">
                <label>Catatan lapangan</label>
                <input type="text" value="{{ $penanaman['catatan'] }}">
            </div>
        </div>

        <div class="pn-actions">
            <button type="submit" class="pn-btn primary">Simpan penanaman</button>
            <a href="{{ route('fase-tanam.penanaman') }}" class="pn-btn">Batal</a>
        </div>
    </form>
</div>

<div class="pn-card">
    <h2>Catatan penanaman</h2>
    <p class="pn-muted">1 catatan pelaksanaan untuk musim berjalan.</p>
    <table class="pn-table">
        <thead>
            <tr><th>Tanggal</th><th>Lahan</th><th>Varietas</th><th>Luas / bibit</th><th>Metode</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $penanaman['tanggal']->translatedFormat('j M Y') }}</strong></td>
                <td>{{ $penanaman['blok'] }}</td>
                <td>{{ $penanaman['varietas'] }}</td>
                <td>{{ $penanaman['luas'] }} ha / {{ number_format($penanaman['jumlah_bibit'], 0, ',', '.') }}</td>
                <td>{{ $penanaman['metode'] }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="pn-card">
    <h2>{{ $blokB['nama'] }} belum ditanam</h2>
    <p class="pn-muted">{{ $blokB['status'] }}</p>
    <a href="#" class="pn-btn">Periksa kesiapan bibit</a>
</div>

@if ($contoh)
    <p class="catatan">Data demonstrasi · Pembaruan sensor {{ $waktu }}. Semua waktu dalam WIB.</p>
@endif
@endsection