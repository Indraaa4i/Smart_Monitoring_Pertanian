<div class="fase-riwayat">
    {{-- Filter arsip --}}
    <form class="riwayat-filter" method="GET" action="{{ route('fase-tanam') }}">
        <input type="hidden" name="tab" value="riwayat">
        @if ($lahan)
            <input type="hidden" name="lahan" value="{{ $lahan->id }}">
        @endif

        <label class="riwayat-filter__field riwayat-filter__field--cari">
            <span>Cari musim atau varietas</span>
            <input
                type="search"
                name="q"
                value="{{ $riwayat['filter']['q'] }}"
                placeholder="Contoh: Inpari 32 atau MK-2026-A"
            >
        </label>

        <label class="riwayat-filter__field">
            <span>Tahun</span>
            <select name="tahun">
                <option value="semua" @selected($riwayat['filter']['tahun'] === 'semua')>
                    Semua tahun
                </option>
                @foreach ($riwayat['opsi']['tahun'] as $tahun)
                    <option
                        value="{{ $tahun }}"
                        @selected((string) $riwayat['filter']['tahun'] === (string) $tahun)
                    >
                        Tahun {{ $tahun }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="riwayat-filter__field">
            <span>Lahan</span>
            <select name="blok">
                <option value="semua" @selected($riwayat['filter']['blok'] === 'semua' || $riwayat['filter']['blok'] === '')>
                    Semua lahan
                </option>
                @foreach ($riwayat['opsi']['lahan'] as $namaLahan)
                    <option
                        value="{{ $namaLahan }}"
                        @selected($riwayat['filter']['blok'] === $namaLahan)
                    >
                        {{ $namaLahan }}
                    </option>
                @endforeach
            </select>
        </label>

        <button type="submit" class="riwayat-filter__submit">Terapkan</button>
        <a class="riwayat-filter__reset" href="{{ route('fase-tanam', ['tab' => 'riwayat']) }}">
            Reset
        </a>
    </form>

    {{-- Kartu statistik --}}
    <section class="riwayat-statistik" aria-label="Ringkasan hasil panen">
        <article class="riwayat-statistik__kartu">
            <h2>Siklus selesai</h2>
            <strong>{{ $riwayat['statistik']['jumlahSiklusFormat'] }}</strong>
            <p>Arsip pada filter yang dipilih</p>
        </article>

        <article class="riwayat-statistik__kartu">
            <h2>Total hasil panen</h2>
            <strong>{{ $riwayat['statistik']['totalPanenFormat'] }}</strong>
            <p>{{ $riwayat['sumberHasil'] }}</p>
        </article>

        <article class="riwayat-statistik__kartu">
            <h2>Produktivitas rata-rata</h2>
            <strong>{{ $riwayat['statistik']['produktivitasFormat'] }}</strong>
            <p>Total panen ÷ {{ $riwayat['statistik']['totalLuasFormat'] }}</p>
        </article>
    </section>

    {{-- Grafik hasil panen --}}
    <section class="riwayat-panel" aria-labelledby="riwayat-grafik-judul">
        <div class="riwayat-panel__kepala">
            <div>
                <h2 id="riwayat-grafik-judul">Grafik hasil panen per lahan</h2>
                <p>Perbandingan hasil panen pada siklus yang sudah selesai.</p>
            </div>
            <span class="riwayat-panel__satuan">Ton</span>
        </div>

        @if (! $riwayat['grafik']['kosong'])
            <div class="riwayat-chart-wrap">
                <svg
                    class="riwayat-chart"
                    viewBox="0 0 {{ $riwayat['grafik']['width'] }} {{ $riwayat['grafik']['height'] }}"
                    role="img"
                    aria-labelledby="riwayat-chart-title riwayat-chart-desc"
                    preserveAspectRatio="xMidYMid meet"
                >
                    <title id="riwayat-chart-title">Grafik hasil panen setiap lahan</title>
                    <desc id="riwayat-chart-desc">
                        Grafik batang membandingkan total gabah kering panen dalam ton untuk setiap blok sawah.
                    </desc>

                    <g class="riwayat-chart__kisi">
                        @foreach ($riwayat['grafik']['ticks'] as $tick)
                            <line
                                x1="{{ $riwayat['grafik']['plotKiri'] }}"
                                x2="{{ $riwayat['grafik']['plotKanan'] }}"
                                y1="{{ $tick['y'] }}"
                                y2="{{ $tick['y'] }}"
                            />
                            <text x="48" y="{{ $tick['y'] + 4 }}">{{ $tick['label'] }}</text>
                        @endforeach
                    </g>

                    <line
                        class="riwayat-chart__sumbu"
                        x1="{{ $riwayat['grafik']['plotKiri'] }}"
                        x2="{{ $riwayat['grafik']['plotKanan'] }}"
                        y1="{{ $riwayat['grafik']['dasar'] }}"
                        y2="{{ $riwayat['grafik']['dasar'] }}"
                    />

                    <g>
                        @foreach ($riwayat['grafik']['batang'] as $batang)
                            <rect
                                class="riwayat-chart__batang"
                                x="{{ $batang['x'] }}"
                                y="{{ $batang['y'] }}"
                                width="{{ $batang['width'] }}"
                                height="{{ $batang['height'] }}"
                                rx="9"
                            />
                            <text
                                class="riwayat-chart__nilai"
                                x="{{ $batang['tengah'] }}"
                                y="{{ max(15, $batang['y'] - 9) }}"
                                text-anchor="middle"
                            >{{ $batang['nilai'] }}</text>
                            <text
                                class="riwayat-chart__label"
                                x="{{ $batang['tengah'] }}"
                                y="252"
                                text-anchor="middle"
                            >{{ $batang['label'] }}</text>
                        @endforeach
                    </g>
                </svg>
            </div>
        @else
            <div class="riwayat-kosong riwayat-kosong--grafik">
                Tidak ada data hasil panen untuk filter yang dipilih.
            </div>
        @endif
    </section>

    {{-- Arsip siklus tanam --}}
    <section class="riwayat-panel" aria-labelledby="riwayat-arsip-judul">
        <div class="riwayat-panel__kepala">
            <div>
                <h2 id="riwayat-arsip-judul">Arsip siklus tanam</h2>
                <p>Semua hasil ditampilkan dalam {{ strtolower($riwayat['sumberHasil']) }}.</p>
            </div>
        </div>

        @if (count($riwayat['arsip']))
            <div class="riwayat-tabel-bungkus">
                <table class="riwayat-tabel">
                    <thead>
                        <tr>
                            <th scope="col">Siklus / lahan</th>
                            <th scope="col">Varietas / luas</th>
                            <th scope="col">Periode{{ $riwayat['filter']['tahun'] !== 'semua' ? ' '.$riwayat['filter']['tahun'] : '' }}</th>
                            <th scope="col">Hasil panen</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat['arsip'] as $siklus)
                            <tr>
                                <th scope="row">
                                    <strong>{{ $siklus['kode'] }} · {{ $siklus['lahan'] }}</strong>
                                </th>
                                <td>{{ $siklus['varietas'] }} · {{ $siklus['luas'] }}</td>
                                <td>{{ $siklus['periode'] }}</td>
                                <td>{{ $siklus['hasil'] }} · {{ $siklus['produktivitas'] }}</td>
                                <td><span class="riwayat-status">{{ $siklus['status'] }}</span></td>
                                <td>
                                    <details class="riwayat-aksi">
                                        <summary>Evaluasi</summary>
                                        <div class="riwayat-aksi__isi">
                                            @if ($siklus['evaluasi'])
                                                <strong>Kendala musim lalu</strong>
                                                <p>{{ $siklus['evaluasi']['kendala'] }}</p>
                                                <strong>Evaluasi petani</strong>
                                                <p>{{ $siklus['evaluasi']['saran'] }}</p>
                                            @else
                                                <p>Catatan evaluasi untuk siklus ini belum tersedia.</p>
                                            @endif
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="riwayat-kosong">
                <strong>Belum ada arsip untuk filter yang dipilih.</strong>
                <p>Coba ubah kata kunci, tahun, atau pilihan lahan.</p>
            </div>
        @endif
    </section>

    {{-- Evaluasi terakhir --}}
    @if ($riwayat['evaluasi'])
        <section class="riwayat-panel" aria-labelledby="riwayat-evaluasi-judul">
            <div class="riwayat-panel__kepala">
                <div>
                    <h2 id="riwayat-evaluasi-judul">
                        Evaluasi terakhir · {{ $riwayat['evaluasi']['kode'] }}
                    </h2>
                    <p>
                        {{ $riwayat['evaluasi']['lahan'] }} · Panen
                        {{ $riwayat['evaluasi']['tanggalPanen'] }} ·
                        Penanggung jawab {{ $riwayat['evaluasi']['penanggungJawab'] }}
                    </p>
                </div>
            </div>

            <div class="riwayat-evaluasi">
                <article class="riwayat-evaluasi__kendala">
                    <h3>Kendala musim lalu</h3>
                    <p>{{ $riwayat['evaluasi']['kendala'] }}</p>
                </article>
                <article class="riwayat-evaluasi__saran">
                    <h3>Evaluasi petani</h3>
                    <p>{{ $riwayat['evaluasi']['saran'] }}</p>
                </article>
            </div>
        </section>
    @endif

    @if (! count($riwayat['arsip']))
        <section class="riwayat-panel riwayat-panel--kosong">
            <h2>Belum ada arsip pada pilihan ini</h2>
            <p>Siklus yang sudah selesai akan tampil di sini setelah hasil panen dan evaluasi dicatat.</p>
            <a href="{{ route('fase-tanam', ['tab' => 'siklus']) }}" class="riwayat-tombol-sekunder">
                Lihat siklus tanam
            </a>
        </section>
    @else
        <section class="riwayat-panel riwayat-panel--info">
            <h2>Arsip musim tanam</h2>
            <p>Siklus yang belum selesai tidak dimasukkan ke dalam perhitungan hasil panen.</p>
            <a href="{{ route('fase-tanam', ['tab' => 'siklus']) }}" class="riwayat-tombol-sekunder">
                Lihat siklus aktif
            </a>
        </section>
    @endif
</div>
