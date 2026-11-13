@extends('layouts.app')

@section('title', 'Rekomendasi')

@section('content')
    <div class="page-stack">
        <section class="page-hero">
            <h1 class="page-hero__title">Rekomendasi</h1>
            <p class="page-hero__sub">Saran sederhana untuk merawat sawah hari ini.</p>
        </section>

        <p class="page-note">Contoh tampilan · Data tanah dan cuaca hanya ilustrasi. Batas aman perlu disesuaikan dengan lahan.</p>

        <div class="saran-grid">
            <article class="panel saran-card">
                <header class="saran-card__head">
                    <h2 class="panel__title panel__title--icon">
                        <x-icon name="clipboard-check" :size="20" />
                        Saran saat ini
                    </h2>
                    <x-status-pill status="offline" label="Belum ada data" />
                </header>

                <div class="chip-row">
                    @foreach ($parameter as $p)
                        <span class="chip chip--offline">
                            <x-icon :name="$p['ikon']" :size="16" />
                            {{ $p['chip'] }}: belum ada data
                        </span>
                    @endforeach
                </div>

                <h3 class="saran-card__title">Saran belum dapat dibuat</h3>
                <p class="saran-card__text">Tidak apa-apa, Pak Tani. Sambungkan sensor agar kondisi tanah bisa dibaca.</p>

                <div class="saran-card__steps">
                    <h4 class="saran-card__steps-title">Dua langkah yang bisa dilakukan</h4>
                    <ol class="step-list">
                        <li class="step-list__item">
                            <span class="step-list__no">1</span>
                            <div>
                                <p class="step-list__title">Periksa daya sensor</p>
                                <p class="step-list__text">Pastikan baterai terisi dan sensor menyala.</p>
                            </div>
                        </li>
                        <li class="step-list__item">
                            <span class="step-list__no">2</span>
                            <div>
                                <p class="step-list__title">Periksa sambungan</p>
                                <p class="step-list__text">Pasang ulang sambungan, lalu tunggu pembacaan.</p>
                            </div>
                        </li>
                    </ol>
                    <p class="saran-card__meta">
                        <x-icon name="clock" :size="14" />
                        Waktu dibuat: menunggu data sensor
                    </p>
                </div>
            </article>

            <aside class="panel cuaca-card">
                <header class="cuaca-card__head">
                    <h2 class="panel__title">Cuaca pendukung</h2>
                    <x-icon name="cloud-rain" :size="22" />
                </header>
                <p class="cuaca-card__source">Sumber: BMKG</p>
                <h3 class="cuaca-card__title">Cuaca belum tersedia</h3>
                <p class="cuaca-card__text">Coba lihat prakiraan BMKG kembali nanti.</p>
            </aside>
        </div>

        <section class="panel">
            <div class="panel__row">
                <h2 class="panel__title">Riwayat rekomendasi</h2>
                <div class="date-select">
                    <span>{{ now()->locale('id')->translatedFormat('j F Y') }}</span>
                    <x-icon name="chevron-down" :size="16" />
                </div>
            </div>

            <div class="empty-state">
                <x-icon name="clipboard-check" :size="24" />
                <h3 class="empty-state__title">Belum ada rekomendasi</h3>
                <p class="empty-state__text">Saran pertama akan muncul setelah sensor mengirim data. Sambil menunggu, periksa sawah secara langsung.</p>
            </div>
        </section>

        <section class="callout">
            <x-icon name="info" :size="20" />
            <div>
                <p class="callout__title">Saran bukan perintah</p>
                <p class="callout__text">Keputusan tetap di tangan petani. Tidak ada kendali irigasi otomatis.</p>
            </div>
        </section>
    </div>
@endsection