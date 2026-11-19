
<div class="cycle-page" data-cycle-page>

    {{-- Filter --}}
    <div class="cycle-filters">
        <input
            type="search"
            class="cycle-search"
            placeholder="Cari siklus atau varietas..."
            aria-label="Cari siklus atau varietas"
            data-cycle-search>

        <select data-cycle-status>
            <option value="">Semua status</option>
            <option value="Aktif">Aktif</option>
            <option value="Rencana">Rencana</option>
            <option value="Selesai">Selesai</option>
        </select>

        <select data-cycle-season>
            <option value="">Semua musim</option>
            @foreach ($dataSiklus['siklus'] as $cycle)
                <option value="{{ $cycle['musim'] }}">
                    {{ $cycle['musim'] }}
                </option>
            @endforeach
        </select>

        <button type="button" class="cycle-primary-button" disabled
            title="Fitur tambah siklus belum diaktifkan">
            + Tambah siklus
        </button>
    </div>

    {{-- Statistik --}}
    <div class="cycle-statistics">
        <article class="cycle-stat-card">
            <h3>Total siklus</h3>
            <strong>{{ $dataSiklus['summary']['total'] }}</strong>
            <p>Semua musim</p>
        </article>

        <article class="cycle-stat-card">
            <h3>Aktif</h3>
            <strong>{{ $dataSiklus['summary']['aktif'] }}</strong>
            <p>Siklus berjalan</p>
        </article>

        <article class="cycle-stat-card">
            <h3>Direncanakan</h3>
            <strong>{{ $dataSiklus['summary']['rencana'] }}</strong>
            <p>Belum memasuki tahap aktif</p>
        </article>
    </div>

    {{-- Tabel --}}
    <section class="cycle-table-card">
        <h2>Daftar siklus tanam</h2>

        <p class="cycle-table-description">
            {{ $dataSiklus['summary']['aktif'] }} siklus aktif.
            Gunakan Detail untuk melihat informasi.
        </p>

        <div class="cycle-table-wrapper">
            <table class="cycle-table">
                <thead>
                    <tr>
                        <th>Siklus / lahan</th>
                        <th>Varietas / luas</th>
                        <th>Periode</th>
                        <th>Tahap</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody data-cycle-table-body>
                    @foreach ($dataSiklus['siklus'] as $cycle)
                        <tr
                            data-cycle-row
                            data-status="{{ $cycle['status'] }}"
                            data-season="{{ $cycle['musim'] }}"
                            data-search="{{ strtolower($cycle['nama'].' '.$cycle['lahan'].' '.$cycle['varietas'].' '.$cycle['tahap']) }}">

                            <td>
                                <strong>
                                    {{ $cycle['nama'] }} · {{ $cycle['lahan'] }}
                                </strong>

                                @if ($cycle['status'] === 'Rencana')
                                    <small class="cycle-note">
                                        Jadwal pengolahan belum ditetapkan.
                                    </small>
                                @endif
                            </td>

                            <td>
                                {{ $cycle['varietas'] }} ·
                                {{ str_replace('.', ',', (string) $cycle['luas_ha']) }} ha
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($cycle['mulai'])->locale('id')->translatedFormat('j M Y') }}
                                –
                                {{ \Carbon\Carbon::parse($cycle['akhir'])->locale('id')->translatedFormat('j M Y') }}
                            </td>

                            <td>{{ $cycle['tahap'] }}</td>

                            <td>
                                <span class="cycle-status
                                    {{ $cycle['status'] === 'Aktif' ? 'is-active' : 'is-planned' }}">
                                    {{ $cycle['status'] }}
                                </span>
                            </td>

                            <td class="cycle-actions">
                                <button
                                    type="button"
                                    class="cycle-detail-button"
                                    data-cycle-detail
                                    data-name="{{ $cycle['nama'] }}"
                                    data-block="{{ $cycle['lahan'] }}"
                                    data-variety="{{ $cycle['varietas'] }}"
                                    data-area="{{ $cycle['luas_ha'] }}"
                                    data-start="{{ $cycle['mulai'] }}"
                                    data-end="{{ $cycle['akhir'] }}"
                                    data-stage="{{ $cycle['tahap'] }}"
                                    data-status="{{ $cycle['status'] }}"
                                    data-season="{{ $cycle['musim'] }}"
                                    data-note="{{ $cycle['catatan'] }}">
                                    Detail
                                </button>

                                <button type="button" disabled
                                    title="Edit belum diaktifkan">
                                    Edit
                                </button>

                                @if ($cycle['tahap'] === 'Persemaian')
                                    <button type="button" disabled
                                        title="Halaman bibit belum diaktifkan">
                                        Lihat bibit
                                    </button>
                                @endif

                                @if ($cycle['status'] === 'Rencana')
                                    <button type="button" disabled
                                        title="Hapus belum diaktifkan">
                                        Hapus
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    <tr data-cycle-empty hidden>
                        <td colspan="6">
                            Siklus tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="cycle-table-footer">
            Seluruh siklus contoh ditampilkan.
        </p>
    </section>

    {{-- Aturan --}}
    <section class="cycle-information">
        <h3>Satu lahan, satu siklus aktif</h3>

        <p>
            Selesaikan siklus sebelumnya sebelum memulai periode baru
            pada blok yang sama. Siklus yang sudah memiliki catatan
            kegiatan tidak dapat dihapus.
        </p>

        <button type="button" class="cycle-secondary-button" disabled>
            Lihat arsip musim sebelumnya
        </button>
    </section>

    {{-- Modal Detail --}}
    <dialog class="cycle-dialog" data-cycle-dialog>
        <div class="cycle-dialog-header">
            <div>
                <p>Informasi siklus</p>
                <h2 data-detail-title>Detail Siklus Tanam</h2>
            </div>

            <button type="button" data-cycle-close
                class="cycle-dialog-close" aria-label="Tutup">&times;</button>
        </div>

        <div class="cycle-dialog-content">
            <div class="cycle-detail-grid">
                <div><span>Varietas</span><strong data-detail-variety>-</strong></div>
                <div><span>Luas lahan</span><strong data-detail-area>-</strong></div>
                <div><span>Periode</span><strong data-detail-period>-</strong></div>
                <div><span>Tahap</span><strong data-detail-stage>-</strong></div>
                <div><span>Status</span><strong data-detail-status>-</strong></div>
                <div><span>Musim</span><strong data-detail-season>-</strong></div>
            </div>

            <div class="cycle-detail-description">
                <h3>Catatan</h3>
                <p data-detail-note>-</p>
            </div>
        </div>

        <div class="cycle-dialog-footer">
            <button type="button" data-cycle-close
                class="cycle-secondary-button">Tutup</button>
        </div>
    </dialog>
</div>
