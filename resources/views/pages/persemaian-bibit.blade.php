@extends('layouts.app')

@section('title', 'Persemaian Bibit')
@section('tanpa-catatan', '1')

@section('content')
<style>
    .sb-breadcrumb {
        font-size: 12px;
        color: #9aa5a0;
        margin-bottom: 10px;
    }

    .sb-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 18px;
    }

    .sb-head__title {
        font-size: 26px;
        font-weight: 700;
        color: #16221c;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .sb-head__sub {
        font-size: 13px;
        color: #6d7a73;
        margin: 4px 0 6px;
    }

    .sb-head__time {
        font-size: 11px;
        color: #98a39e;
        margin: 0;
    }

    .sb-badge {
        display: inline-block;
        background: #dff5e7;
        color: #166b41;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .sb-filter-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 16px 0;
        flex-wrap: wrap;
    }

    .sb-select-pill {
        background: #ffffff;
        border: 1px solid #e2e8e4;
        border-radius: 10px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #1f2d26;
    }

    /* 3 Kartu Ringkasan */
    .sb-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .sb-summary-card {
        background: #ffffff;
        border: 1px solid #e6ece8;
        border-radius: 14px;
        padding: 18px 20px;
    }

    .sb-summary-card__label {
        font-size: 12px;
        font-weight: 600;
        color: #37463e;
        margin-bottom: 10px;
    }

    .sb-summary-card__value {
        font-size: 24px;
        font-weight: 700;
        color: #156b41;
        margin: 0 0 6px;
    }

    .sb-summary-card__desc {
        font-size: 11px;
        color: #8c9992;
        margin: 0;
    }

    /* Card Utama Form & Tabel */
    .sb-card {
        background: #ffffff;
        border: 1px solid #e6ece8;
        border-radius: 16px;
        padding: 22px 24px;
        margin-bottom: 18px;
    }

    .sb-card__title {
        font-size: 16px;
        font-weight: 700;
        color: #16221c;
        margin: 0 0 4px;
    }

    .sb-card__sub {
        font-size: 12px;
        color: #8c9992;
        margin: 0 0 18px;
    }

    .sb-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 18px;
    }

    .sb-field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #2b3a32;
        margin-bottom: 6px;
    }

    .sb-input,
    .sb-select {
        width: 100%;
        box-sizing: border-box;
        background: #ffffff;
        border: 1px solid #dfe6e2;
        border-radius: 10px;
        padding: 9px 12px;
        font-size: 12px;
        color: #1e2b24;
    }

    .sb-input:focus,
    .sb-select:focus {
        outline: none;
        border-color: #156b41;
    }

    .sb-age-box {
        background: #e1f5e9;
        border-radius: 10px;
        padding: 13px 16px;
        margin: 16px 0 14px;
    }

    .sb-age-box__title {
        font-size: 13px;
        font-weight: 700;
        color: #14643c;
        margin: 0 0 3px;
    }

    .sb-age-box__desc {
        font-size: 11px;
        color: #2d7a53;
        margin: 0;
    }

    .sb-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        flex-wrap: wrap;
    }

    .sb-btn-primary {
        background: #156b41;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 9px 18px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    .sb-btn-primary:hover {
        background: #105533;
    }

    .sb-btn-secondary {
        display: inline-block;
        background: #ffffff;
        color: #2b3a32;
        border: 1px solid #dfe6e2;
        border-radius: 10px;
        padding: 9px 16px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }

    .sb-btn-secondary:hover {
        background: #f7faf8;
    }

    .sb-alert-success {
        background: #e1f5e9;
        color: #14643c;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 14px;
    }

    /* Tabel Riwayat */
    .sb-table-wrap {
        overflow-x: auto;
    }

    .sb-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        min-width: 560px;
    }

    .sb-table th {
        background: #f6f8f7;
        color: #7c8a83;
        font-weight: 600;
        text-align: left;
        padding: 11px 14px;
    }

    .sb-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #eef2f0;
        color: #26352d;
    }

    .sb-table tr:last-child td {
        border-bottom: none;
    }

    .sb-footer-note {
        font-size: 11px;
        color: #98a39e;
        margin-top: 8px;
    }

    @media (max-width: 900px) {
        .sb-summary-grid,
        .sb-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

{{-- BREADCRUMB & HEADER --}}
<div class="sb-breadcrumb">Fase Tanam / Persemaian bibit</div>

<header class="sb-head">
    <div>
        <h1 class="sb-head__title">Persemaian Bibit</h1>
        <p class="sb-head__sub">Pantau benih sejak disemai hingga siap dipindah tanam.</p>
        <p class="sb-head__time">{{ $waktu }}</p>
    </div>

    @if ($contoh)
        <span class="sb-badge">Data contoh</span>
    @endif
</header>

{{-- 8 TAB FASE TANAM --}}
<x-fase-tab :tab="$tab" />

{{-- FILTER BLOK & STATUS --}}
<div class="sb-filter-row">
    <span class="sb-select-pill">{{ $data['siklus'] }} · {{ $data['blok'] }} ▾</span>
    <span class="sb-badge">Persemaian · {{ $umurBibit }} hari</span>
</div>

{{-- 3 KARTU RINGKASAN --}}
<div class="sb-summary-grid">
    <div class="sb-summary-card">
        <div class="sb-summary-card__label">Umur bibit</div>
        <p class="sb-summary-card__value" id="psCardUmur">{{ $umurBibit }} hari</p>
        <p class="sb-summary-card__desc">
            Disemai {{ \Carbon\Carbon::parse($data['tanggal_semai'])->locale('id')->translatedFormat('j M Y') }}
        </p>
    </div>

    <div class="sb-summary-card">
        <div class="sb-summary-card__label">Benih disemai</div>
        <p class="sb-summary-card__value">
            {{ number_format($data['jumlah_benih'], 0, ',', '.') }} kg
        </p>
        <p class="sb-summary-card__desc">{{ $data['varietas'] }} · {{ $data['blok'] }}</p>
    </div>

    <div class="sb-summary-card">
        <div class="sb-summary-card__label">Pindah tanam</div>
        <p class="sb-summary-card__value">
            {{ \Carbon\Carbon::parse($data['target_pindah'])->locale('id')->translatedFormat('j M') }}
        </p>
        <p class="sb-summary-card__desc">Target umur bibit 20 hari</p>
    </div>
</div>

{{-- FORM PENCATATAN PERSEMAIAN BIBIT --}}
<div class="sb-card">
    <h2 class="sb-card__title">Pencatatan persemaian bibit</h2>
    <p class="sb-card__sub">Perbarui kondisi pertumbuhan sesuai pemeriksaan lapangan.</p>

    @if (session('success'))
        <div class="sb-alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('fase-tanam.persemaian.store') }}" method="POST">
        @csrf
        <div class="sb-form-grid">
            <div class="sb-field">
                <label>Lahan / siklus *</label>
                <select name="lahan_siklus" class="sb-select">
                    <option value="{{ $data['blok'] }} · {{ $data['siklus'] }}" selected>
                        {{ $data['blok'] }} · {{ $data['siklus'] }}
                    </option>
                </select>
            </div>

            <div class="sb-field">
                <label>Varietas padi *</label>
                <select name="varietas" class="sb-select">
                    <option value="{{ $data['varietas'] }}" selected>{{ $data['varietas'] }}</option>
                    <option value="Ciherang">Ciherang</option>
                    <option value="Inpari 32">Inpari 32</option>
                    <option value="IR64">IR64</option>
                </select>
            </div>

            <div class="sb-field">
                <label>Tanggal persemaian *</label>
                <input
                    type="date"
                    id="psTglSemai"
                    name="tanggal_semai"
                    class="sb-input"
                    value="{{ \Carbon\Carbon::parse($data['tanggal_semai'])->format('Y-m-d') }}"
                >
            </div>

            <div class="sb-field">
                <label>Jumlah benih (kg) *</label>
                <input
                    type="number"
                    name="jumlah_benih"
                    class="sb-input"
                    value="{{ $data['jumlah_benih'] }}"
                >
            </div>

            <div class="sb-field">
                <label>Tanggal pemeriksaan *</label>
                <input
                    type="date"
                    id="psTglPeriksa"
                    name="tanggal_periksa"
                    class="sb-input"
                    value="{{ \Carbon\Carbon::parse($data['tanggal_periksa'])->format('Y-m-d') }}"
                >
            </div>

            <div class="sb-field">
                <label>Kondisi pertumbuhan *</label>
                <select name="kondisi" class="sb-select">
                    <option value="{{ $data['kondisi'] }}" selected>{{ $data['kondisi'] }}</option>
                    <option value="Baik · Hijau merata">Baik · Hijau merata</option>
                    <option value="Cukup">Cukup</option>
                    <option value="Perlu perhatian">Perlu perhatian</option>
                </select>
            </div>

            <div class="sb-field">
                <label>Daya tumbuh (%)</label>
                <input
                    type="number"
                    name="daya_tumbuh"
                    class="sb-input"
                    value="{{ $data['daya_tumbuh'] }}"
                    min="0"
                    max="100"
                >
            </div>

            <div class="sb-field">
                <label>Target pindah tanam</label>
                <input
                    type="date"
                    name="target_pindah"
                    class="sb-input"
                    value="{{ \Carbon\Carbon::parse($data['target_pindah'])->format('Y-m-d') }}"
                >
            </div>
        </div>

        <div class="sb-age-box">
            <p class="sb-age-box__title" id="psBannerUmur">Umur bibit: {{ $umurBibit }} hari</p>
            <p class="sb-age-box__desc">Selisih tanggal pemeriksaan dan tanggal persemaian.</p>
        </div>

        <div class="sb-field">
            <label>Catatan pertumbuhan</label>
            <input
                type="text"
                name="catatan"
                class="sb-input"
                value="{{ $data['catatan'] }}"
            >
        </div>

        <div class="sb-actions">
            <button type="submit" class="sb-btn-primary">Simpan pemeriksaan</button>
            <a href="{{ route('fase-tanam.penanaman') }}" class="sb-btn-secondary">Lihat jadwal pindah tanam</a>
        </div>
    </form>
</div>

{{-- RIWAYAT PEMERIKSAAN BIBIT --}}
<div class="sb-card">
    <h2 class="sb-card__title">Riwayat pemeriksaan bibit</h2>
    <p class="sb-card__sub">Kelompok bibit {{ $data['varietas'] }} · {{ number_format($data['jumlah_benih'], 0, ',', '.') }} kg</p>

    <div class="sb-table-wrap">
        <table class="sb-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Umur</th>
                    <th>Pertumbuhan</th>
                    <th>Daya tumbuh</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['riwayat'] as $riwayat)
                    <tr>
                        <td><strong>{{ $riwayat['tanggal'] }}</strong></td>
                        <td>{{ $riwayat['umur'] }} hari</td>
                        <td>{{ $riwayat['kondisi'] }} · {{ $riwayat['tinggi'] }}</td>
                        <td>{{ $riwayat['daya_tumbuh'] }}</td>
                        <td>{{ $riwayat['petugas'] ?? 'Bu Sari' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center">Belum ada riwayat pemeriksaan bibit.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($contoh)
    <p class="sb-footer-note">
        Data demonstrasi · Pembaruan {{ $waktu }}. Semua waktu dalam WIB.
    </p>
@endif

@push('scripts')
<script>
    (function () {
        const tglSemai = document.getElementById('psTglSemai');
        const tglPeriksa = document.getElementById('psTglPeriksa');
        const bannerUmur = document.getElementById('psBannerUmur');
        const cardUmur = document.getElementById('psCardUmur');

        function updateUmur() {
            if (!tglSemai || !tglPeriksa || !tglSemai.value || !tglPeriksa.value) return;
            const d1 = new Date(tglSemai.value);
            const d2 = new Date(tglPeriksa.value);
            const hari = Math.max(0, Math.round((d2 - d1) / (1000 * 60 * 60 * 24)));
            if (bannerUmur) bannerUmur.textContent = `Umur bibit: ${hari} hari`;
            if (cardUmur) cardUmur.textContent = `${hari} hari`;
        }

        tglSemai?.addEventListener('change', updateUmur);
        tglPeriksa?.addEventListener('change', updateUmur);
    })();
</script>
@endpush
@endsection