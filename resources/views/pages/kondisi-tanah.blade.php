@extends('layouts.app')

@section('title', 'Kondisi Tanah')

@section('content')
    <div class="page-stack">
        <section class="page-hero">
            <h1 class="page-hero__title">Kondisi Tanah</h1>
            <p class="page-hero__sub">Lihat kelembapan dan pH tanah sawah Anda.</p>
        </section>

        <p class="page-note">Contoh tampilan · Angka dan batas aman perlu disesuaikan dengan lahan.</p>

        <section class="soil-summary soil-summary--offline" aria-live="polite">
            <x-icon name="shield" :size="24" />
            <div>
                <h2 class="soil-summary__title">Sensor belum terhubung</h2>
                <p class="soil-summary__text">Data belum tersedia. Periksa daya dan sambungan sensor.</p>
            </div>
        </section>

        <div class="soil-grid">
            @foreach ($parameter as $p)
                <x-soil-card :parameter="$p" />
            @endforeach
        </div>

        <section class="panel">
            <h2 class="panel__title">Status Sensor</h2>
            <div class="sensor-list">
                @foreach ($parameter as $p)
                    <div class="sensor-item">
                        <x-icon :name="$p['ikon']" :size="18" />
                        <div class="sensor-item__info">
                            <p class="sensor-item__name">{{ $p['sensor'] }}</p>
                            <p class="sensor-item__time">Terakhir terhubung: belum pernah</p>
                        </div>
                        <x-status-pill status="offline" label="Offline" />
                    </div>
                @endforeach
            </div>
        </section>

        <section class="panel">
            <h2 class="panel__title">Pembacaan terbaru</h2>
            <p class="panel__sub">{{ $tanggal }}</p>

            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Waktu</th>
                            <th scope="col">Kelembapan</th>
                            <th scope="col">pH</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="empty-state">
                <x-icon name="info" :size="22" />
                <h3 class="empty-state__title">Belum ada data, Pak Tani</h3>
                <p class="empty-state__text">Nyalakan sensor dan periksa sambungannya. Pembacaan akan muncul di sini.</p>
            </div>
        </section>

        <p class="page-note">Batas aman di atas adalah contoh. Sesuaikan dengan kondisi lahan dan arahan penyuluh.</p>
    </div>
@endsection