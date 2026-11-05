@extends('layouts.app')

@section('title', 'Riwayat')
@section('page-title', 'Riwayat')
@section('page-subtitle', 'Selasa, 6 Oktober 2026 · Lihat kembali hasil pemantauan sawah')

@section('content')
    {{-- Alert Banner Demonstrasi --}}
    <x-notice type="info">
        <x-icon name="info" />
        <span>Data demonstrasi · Angka, cuaca, dan batas adalah contoh, bukan pembacaan nyata atau panduan untuk semua sawah.</span>
    </x-notice>

    {{-- Ringkasan Metrik (3 Kartu Atas) --}}
    <div class="metrics">
        <div class="card metric">
            <div class="metric__head">
                <span class="muted">Kelembapan terakhir</span>
                <x-icon name="droplet" />
            </div>
            <div class="metric__value">68%</div>
            <p class="muted">Tanah cukup lembap</p>
        </div>

        <div class="card metric">
            <div class="metric__head">
                <span class="muted">pH terakhir</span>
                <x-icon name="flask" />
            </div>
            <div class="metric__value">6,4</div>
            <p class="muted">Keasaman sesuai batas</p>
        </div>

        <div class="card metric">
            <div class="metric__head">
                <span class="muted">Status selama rentang</span>
                <x-icon name="check-circle" />
            </div>
            <div class="metric__value">6 <span>dari 6</span></div>
            <p class="muted">Catatan dalam batas contoh</p>
        </div>
    </div>

    {{-- Filter Tanggal & Pengukuran --}}
    <div class="history-filters">
        <div class="filter-group">
            <label class="muted--xs">Tanggal</label>
            <div class="select-btn">
                <x-icon name="calendar" />
                <span>6 Oktober 2026</span>
                <x-icon name="chevron-down" />
            </div>
        </div>
        <div class="filter-group">
            <label class="muted--xs">Pengukuran</label>
            <div class="select-btn">
                <x-icon name="sprout" />
                <span>Semua pengukuran</span>
                <x-icon name="chevron-down" />
            </div>
        </div>
        <div class="filter-info muted--xs">
            Data contoh terakhir<br><strong>08.30 WIB</strong>
        </div>
    </div>

    {{-- Tabel Catatan Pemantauan --}}
    <div class="card history-table-card">
        <div class="card__head">
            <h2 class="card__title" style="font-size: 16px;">Catatan pemantauan</h2>
            <span class="muted--xs">6 Oktober 2026 · 06.00–08.30 WIB</span>
        </div>

        <div class="table-scroll" style="margin-top: 14px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal & waktu</th>
                        <th>Kelembapan</th>
                        <th>pH tanah</th>
                        <th>Status sawah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>6 Okt 2026 · 08.30 WIB</td>
                        <td><strong>68%</strong></td>
                        <td><strong>6,4</strong></td>
                        <td><span class="badge badge--success"><x-icon name="check-circle" /> KONDISI AMAN</span></td>
                        <td>Data contoh terakhir</td>
                    </tr>
                    <tr>
                        <td>6 Okt 2026 · 08.00 WIB</td>
                        <td><strong>67%</strong></td>
                        <td><strong>6,4</strong></td>
                        <td><span class="badge badge--success"><x-icon name="check-circle" /> KONDISI AMAN</span></td>
                        <td>pH naik dari 6,3 menjadi 6,4</td>
                    </tr>
                    <tr>
                        <td>6 Okt 2026 · 07.30 WIB</td>
                        <td><strong>66%</strong></td>
                        <td><strong>6,3</strong></td>
                        <td><span class="badge badge--success"><x-icon name="check-circle" /> KONDISI AMAN</span></td>
                        <td>Kelembapan naik dari 65% menjadi 66%</td>
                    </tr>
                    <tr>
                        <td>6 Okt 2026 · 07.00 WIB</td>
                        <td><strong>65%</strong></td>
                        <td><strong>6,3</strong></td>
                        <td><span class="badge badge--success"><x-icon name="check-circle" /> KONDISI AMAN</span></td>
                        <td>Sama dengan pembacaan sebelumnya</td>
                    </tr>
                    <tr>
                        <td>6 Okt 2026 · 06.30 WIB</td>
                        <td><strong>65%</strong></td>
                        <td><strong>6,3</strong></td>
                        <td><span class="badge badge--success"><x-icon name="check-circle" /> KONDISI AMAN</span></td>
                        <td>Kelembapan naik dari 64% menjadi 65%</td>
                    </tr>
                    <tr>
                        <td>6 Okt 2026 · 06.00 WIB</td>
                        <td><strong>64%</strong></td>
                        <td><strong>6,3</strong></td>
                        <td><span class="badge badge--success"><x-icon name="check-circle" /> KONDISI AMAN</span></td>
                        <td>Awal rentang pemantauan pagi ini</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="muted--xs" style="margin-top: 12px;">Menampilkan 6 catatan contoh · Terbaru lebih dahulu</p>
    </div>

    {{-- Banner Ringkasan Bawah --}}
    <x-notice type="success">
        <x-icon name="check-circle" />
        <div>
            <strong>Semua catatan pagi ini dalam batas contoh</strong>
            <p class="muted--xs" style="margin-top: 2px;">Kelembapan 64–68% dan pH 6,3–6,4 masih dalam batas Sawah Blok A: kelembapan 60–80%, pH 5,5–7,0. Riwayat adalah catatan pembacaan, bukan bukti tindakan pengairan.</p>
        </div>
    </x-notice>
@endsection
