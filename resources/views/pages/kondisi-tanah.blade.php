
@extends('layouts.app')

@section('title', 'Kondisi Tanah')

@section('content')
<div class="page-stack soil-dashboard">

    {{-- Header halaman --}}
    <section class="soil-heading">
        <div>
            <h1 class="page-hero__title">Kondisi Tanah</h1>
            <p class="page-hero__sub">
                Pantau setiap blok. Kenali masalah sebelum meluas.
            </p>
        </div>

        <div class="soil-heading__info">
            <span class="soil-sample-label">Data contoh</span>
            <small>{{ $tanggal }}</small>
        </div>
    </section>

    {{-- Ringkasan kondisi --}}
    <section class="soil-overview">
        <div>
            <h2>{{ $jumlahPerluDitangani }} dari {{ count($blok) }} blok perlu ditangani</h2>
            <p>Prioritaskan blok dengan kondisi tanah paling membutuhkan perhatian.</p>
        </div>

        <div class="soil-overview__statuses">
            <span class="soil-count soil-count--safe">
                {{ $jumlahAman }} Aman
            </span>
            <span class="soil-count soil-count--warning">
                {{ $jumlahPerhatian }} Perhatian
            </span>
            <span class="soil-count soil-count--danger">
                {{ $jumlahBahaya }} Bahaya
            </span>
        </div>
    </section>

    {{-- Peta blok dan prioritas --}}
    <div class="soil-top-grid">

        <section class="panel soil-map-panel">
            <div class="soil-section-heading">
                <div>
                    <h2 class="panel__title">Peta kondisi lahan</h2>
                    <p class="panel__sub">Tampilan atas · {{ count($blok) }} blok dipantau</p>
                </div>
                <span class="soil-map-icon">↑</span>
            </div>

            <div class="soil-field-map">
                <div class="soil-field-road"></div>

                <div class="soil-blocks">
                    @foreach ($blok as $item)
                        <article class="soil-map-block soil-map-block--{{ $item['status'] }}">
                            <div class="soil-map-card">
                                <div class="soil-map-card__heading">
                                    <strong>{{ $item['nama'] }}</strong>
                                    <span class="soil-status soil-status--{{ $item['status'] }}">
                                        {{ $item['label_status'] }}
                                    </span>
                                </div>

                                <div class="soil-map-values">
                                    <strong>{{ $item['kelembapan'] }}%</strong>
                                    <span>pH {{ number_format($item['ph'], 1, ',', '.') }}</span>
                                </div>

                                <small class="soil-online">
                                    <span></span>
                                    {{ $item['sensor_status'] }}
                                </small>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="panel soil-priority-panel">
            <h2 class="panel__title">Prioritas hari ini</h2>
            <p class="panel__sub">Tindakan per blok, bukan nilai rata-rata.</p>

            <div class="soil-priority-list">
                @forelse ($prioritas as $index => $item)
                    <article class="soil-priority soil-priority--{{ $item['status'] }}">
                        <h3>
                            <span>{{ sprintf('%02d', $index + 1) }}</span>
                            {{ $item['judul'] }}
                        </h3>
                        <p>{{ $item['deskripsi'] }}</p>
                    </article>
                @empty
                    <p class="soil-empty">Belum ada prioritas penanganan.</p>
                @endforelse
            </div>
        </section>

    </div>

    {{-- Detail setiap blok --}}
    <section class="soil-detail-section">
        <h2 class="soil-section-title">Detail setiap blok</h2>

        <div class="soil-detail-grid">
            @foreach ($blok as $item)
                <article class="panel soil-detail-card soil-detail-card--{{ $item['status'] }}">

                    <div class="soil-detail-heading">
                        <h3>{{ $item['nama'] }}</h3>
                        <span class="soil-status soil-status--{{ $item['status'] }}">
                            {{ $item['label_status'] }}
                        </span>
                    </div>

                    <p class="soil-condition">{{ $item['keterangan'] }}</p>

                    <div class="soil-measurements">
                        <div>
                            <span>Kelembapan tanah</span>
                            <strong>{{ $item['kelembapan'] }}%</strong>
                            <small>Ideal 60–80%</small>
                        </div>

                        <div>
                            <span>pH tanah</span>
                            <strong>{{ number_format($item['ph'], 1, ',', '.') }}</strong>
                            <small>Ideal 5,5–7,0</small>
                        </div>
                    </div>

                    <div class="soil-recommendation">
                        <strong>Rekomendasi</strong>
                        <p>{{ $item['rekomendasi'] }}</p>
                    </div>

                    <div class="soil-sensor-status">
                        <span class="soil-sensor-dot
                            {{ $item['online'] ? 'is-online' : 'is-offline' }}"></span>

                        <div>
                            <strong>
                                {{ $item['online'] ? 'Sensor online' : 'Sensor terputus' }}
                            </strong>
                            <small>{{ $item['sensor_id'] }} · {{ $item['waktu'] }}</small>
                        </div>
                    </div>

                </article>
            @endforeach
        </div>
    </section>

    {{-- Riwayat kondisi tanah --}}
    <section class="panel soil-history">
        <div class="soil-history__heading">
            <div>
                <h2 class="panel__title">Riwayat kondisi tanah</h2>
                <p class="panel__sub">
                    Perbandingan kelembapan dan pH setiap blok.
                </p>
            </div>
            <span class="soil-history-date">{{ $periode }}</span>
        </div>

        <div class="soil-history-grid">

            <div class="soil-chart">
                <h3>Kelembapan tanah (%)</h3>

                <svg viewBox="0 0 500 190" role="img"
                     aria-label="Grafik contoh kelembapan tanah">
                    <g class="soil-chart-grid">
                        <line x1="40" y1="20" x2="480" y2="20"/>
                        <line x1="40" y1="55" x2="480" y2="55"/>
                        <line x1="40" y1="90" x2="480" y2="90"/>
                        <line x1="40" y1="125" x2="480" y2="125"/>
                        <line x1="40" y1="160" x2="480" y2="160"/>
                    </g>

                    <g class="soil-chart-label">
                        <text x="4" y="24">100%</text>
                        <text x="10" y="59">80%</text>
                        <text x="10" y="94">60%</text>
                        <text x="10" y="129">40%</text>
                        <text x="10" y="164">20%</text>
                    </g>

                    <rect x="40" y="55" width="440" height="35"
                          class="soil-chart-ideal"/>

                    <polyline points="40,83 150,81 260,79 370,77 480,75"
                              class="soil-line soil-line--safe"/>

                    <polyline points="40,88 150,94 260,98 370,102 480,106"
                              class="soil-line soil-line--warning"/>

                    <polyline points="40,108 150,113 260,119 370,125 480,130"
                              class="soil-line soil-line--danger"/>

                    <g class="soil-chart-label soil-chart-time">
                        <text x="40" y="181">06.30</text>
                        <text x="145" y="181">07.00</text>
                        <text x="255" y="181">07.30</text>
                        <text x="365" y="181">08.00</text>
                        <text x="450" y="181">08.30</text>
                    </g>
                </svg>
            </div>

            <div class="soil-chart">
                <h3>Keasaman tanah (pH)</h3>

                <svg viewBox="0 0 500 190" role="img"
                     aria-label="Grafik contoh pH tanah">
                    <g class="soil-chart-grid">
                        <line x1="40" y1="20" x2="480" y2="20"/>
                        <line x1="40" y1="55" x2="480" y2="55"/>
                        <line x1="40" y1="90" x2="480" y2="90"/>
                        <line x1="40" y1="125" x2="480" y2="125"/>
                        <line x1="40" y1="160" x2="480" y2="160"/>
                    </g>

                    <g class="soil-chart-label">
                        <text x="15" y="24">8</text>
                        <text x="15" y="59">7</text>
                        <text x="15" y="94">6</text>
                        <text x="15" y="129">5</text>
                        <text x="15" y="164">4</text>
                    </g>

                    <rect x="40" y="55" width="440" height="53"
                          class="soil-chart-ideal"/>

                    <polyline points="40,80 150,77 260,77 370,74 480,74"
                              class="soil-line soil-line--safe"/>

                    <polyline points="40,98 150,102 260,105 370,108 480,111"
                              class="soil-line soil-line--warning"/>

                    <polyline points="40,111 150,115 260,119 370,123 480,128"
                              class="soil-line soil-line--danger"/>

                    <g class="soil-chart-label soil-chart-time">
                        <text x="40" y="181">06.30</text>
                        <text x="145" y="181">07.00</text>
                        <text x="255" y="181">07.30</text>
                        <text x="365" y="181">08.00</text>
                        <text x="450" y="181">08.30</text>
                    </g>
                </svg>
            </div>

        </div>

        <div class="soil-chart-legend">
            <span><i class="legend-safe"></i> Blok A</span>
            <span><i class="legend-warning"></i> Blok B</span>
            <span><i class="legend-danger"></i> Blok C</span>
            <small>Grafik ini masih data ilustrasi, bukan pembacaan sensor aktual.</small>
        </div>
    </section>

    <p class="soil-footnote">
        Aturan contoh: Aman 60–80% dan pH 5,5–7,0.
        Status dan rekomendasi perlu disesuaikan dengan jenis tanaman,
        fase pertumbuhan, karakteristik lahan, serta arahan penyuluh.
    </p>

</div>
@endsection
