<div class="jadwal-halaman" data-jadwal-page data-jadwal-buka-form="{{ $errors->any() ? '1' : '0' }}">
    @if (session('success'))
        <div class="jadwal-alert jadwal-alert--success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="jadwal-alert jadwal-alert--error" role="alert">
            <strong>Periksa kembali isian aktivitas.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filter lahan dan status --}}
    <form class="jadwal-filter panel" method="GET" action="{{ route('fase-tanam') }}">
        <input type="hidden" name="tab" value="jadwal">
        <input type="hidden" name="bulan" value="{{ $jadwal['bulan'] }}">
        <input type="hidden" name="tanggal" value="{{ $jadwal['tanggal_terpilih'] }}">

        <label class="jadwal-field">
            <span>Lahan</span>
            <select name="lahan_id">
                <option value="">Semua lahan</option>
                @foreach ($jadwal['opsi_lahan'] as $opsi)
                    <option value="{{ $opsi['id'] }}"
                        @selected((string) $jadwal['filter']['lahan_id'] === (string) $opsi['id'])>
                        {{ $opsi['nama'] }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="jadwal-field">
            <span>Status</span>
            <select name="status">
                <option value="semua" @selected($jadwal['filter']['status'] === 'semua')>Semua status</option>
                <option value="menunggu" @selected($jadwal['filter']['status'] === 'menunggu')>Menunggu</option>
                <option value="selesai" @selected($jadwal['filter']['status'] === 'selesai')>Selesai</option>
            </select>
        </label>

        <button type="submit" class="btn-fase">Terapkan filter</button>

        <button type="button" class="btn-fase btn-fase--utama"
            data-jadwal-modal-buka="jadwal-form-dialog">
            + Tambah aktivitas
        </button>
    </form>

    {{-- Kalender bulanan --}}
    <section class="panel jadwal-panel" aria-labelledby="jadwal-kalender-judul">
        <div class="jadwal-panel__heading">
            <div>
                <h2 id="jadwal-kalender-judul" class="jadwal-panel__title">Kalender pertanian</h2>
                <p class="jadwal-panel__sub">
                    {{ $jadwal['bulan_label'] }} · Ketuk tanggal bertanda untuk melihat kegiatan.
                </p>
            </div>
        </div>

        <div class="jadwal-calendar-toolbar">
            <a class="btn-fase"
                href="{{ route('fase-tanam', array_filter([
                    'tab' => 'jadwal',
                    'bulan' => $jadwal['bulan_sebelumnya'],
                    'lahan_id' => $jadwal['filter']['lahan_id'],
                    'status' => $jadwal['filter']['status'] !== 'semua' ? $jadwal['filter']['status'] : null,
                ])) }}">
                Bulan sebelumnya
            </a>

            <h3>{{ $jadwal['bulan_label'] }}</h3>

            <div class="jadwal-calendar-toolbar__right">
                <a class="btn-fase"
                    href="{{ route('fase-tanam', array_filter([
                        'tab' => 'jadwal',
                        'bulan' => substr($jadwal['hari_ini'], 0, 7),
                        'tanggal' => $jadwal['hari_ini'],
                        'lahan_id' => $jadwal['filter']['lahan_id'],
                        'status' => $jadwal['filter']['status'] !== 'semua' ? $jadwal['filter']['status'] : null,
                    ])) }}">
                    Hari ini · {{ $jadwal['hari_ini_label'] }}
                </a>
                <a class="btn-fase"
                    href="{{ route('fase-tanam', array_filter([
                        'tab' => 'jadwal',
                        'bulan' => $jadwal['bulan_berikutnya'],
                        'lahan_id' => $jadwal['filter']['lahan_id'],
                        'status' => $jadwal['filter']['status'] !== 'semua' ? $jadwal['filter']['status'] : null,
                    ])) }}">
                    Bulan berikutnya
                </a>
            </div>
        </div>

        <div class="jadwal-calendar" role="group" aria-label="Kalender aktivitas pertanian">
            @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $namaHari)
                <div class="jadwal-calendar__weekday">{{ $namaHari }}</div>
            @endforeach

            @foreach ($jadwal['kalender'] as $hari)
                <a
                    class="jadwal-day
                        {{ $hari['dalam_bulan'] ? '' : 'is-outside' }}
                        {{ $hari['hari_ini'] ? 'is-today' : '' }}
                        {{ $hari['terpilih'] ? 'is-selected' : '' }}
                        {{ $hari['jumlah'] > 0 ? 'has-activity' : '' }}"
                    href="{{ route('fase-tanam', $hari['url_parameter']) }}"
                    @if ($hari['hari_ini']) aria-current="date" @endif
                    aria-label="{{ $hari['tanggal'] }}{{ $hari['jumlah'] ? ', '.$hari['jumlah'].' kegiatan' : '' }}">
                    <span class="jadwal-day__number">{{ $hari['nomor'] }}</span>

                    @if ($hari['jumlah'] > 0)
                        <span class="jadwal-day__event">
                            {{ $hari['jumlah'] }} kegiatan
                        </span>
                        <span class="jadwal-day__first">{{ $hari['kegiatan_pertama'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <p class="jadwal-calendar__footnote">
            Tanggal berwarna hijau menandakan hari ini. Pilih tanggal untuk melihat daftar kegiatan.
        </p>
    </section>

    {{-- Aktivitas pada tanggal terpilih --}}
    <section class="panel jadwal-panel" aria-labelledby="jadwal-harian-judul">
        <div class="jadwal-panel__heading">
            <div>
                <h2 id="jadwal-harian-judul" class="jadwal-panel__title">
                    {{ $jadwal['tanggal_terpilih_label'] }}
                </h2>
                <p class="jadwal-panel__sub">
                    {{ $jadwal['ringkasan_tanggal']['total'] }} kegiatan ·
                    {{ $jadwal['ringkasan_tanggal']['selesai'] }} selesai ·
                    {{ $jadwal['ringkasan_tanggal']['menunggu'] }} menunggu
                </p>
            </div>

            <button type="button" class="btn-fase btn-fase--utama"
                data-jadwal-modal-buka="jadwal-form-dialog">
                + Tambah aktivitas
            </button>
        </div>

        @if (count($jadwal['aktivitas_tanggal']) > 0)
            <div class="jadwal-table-wrap">
                <table class="jadwal-table">
                    <thead>
                        <tr>
                            <th>Kegiatan</th>
                            <th>Lahan / waktu</th>
                            <th>Prioritas</th>
                            <th>Pengingat</th>
                            <th>Status / aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jadwal['aktivitas_tanggal'] as $aktivitas)
                            <tr>
                                <td>
                                    <strong>{{ $aktivitas['kegiatan'] }}</strong>
                                    @if (! empty($aktivitas['catatan']))
                                        <small class="jadwal-table__note">{{ $aktivitas['catatan'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $aktivitas['lahan_nama'] }} · {{ $aktivitas['waktu_label'] }}
                                </td>
                                <td>
                                    <span class="jadwal-pill jadwal-pill--{{ $aktivitas['prioritas_warna'] }}">
                                        {{ $aktivitas['prioritas_label'] }}
                                    </span>
                                </td>
                                <td>{{ $aktivitas['pengingat_label'] }}</td>
                                <td>
                                    <div class="jadwal-row-actions">
                                        <span class="jadwal-pill jadwal-pill--{{ $aktivitas['status'] === 'selesai' ? 'selesai' : 'menunggu' }}">
                                            {{ $aktivitas['status_label'] }}
                                        </span>

                                        <button type="button" class="btn-fase btn-fase--kecil"
                                            data-jadwal-detail
                                            data-kegiatan="{{ $aktivitas['kegiatan'] }}"
                                            data-lahan="{{ $aktivitas['lahan_nama'] }}"
                                            data-tanggal="{{ $aktivitas['tanggal_label'] }}"
                                            data-waktu="{{ $aktivitas['waktu_label'] }}"
                                            data-prioritas="{{ $aktivitas['prioritas_label'] }}"
                                            data-pengingat="{{ $aktivitas['pengingat_label'] }}"
                                            data-status="{{ $aktivitas['status_label'] }}"
                                            data-catatan="{{ $aktivitas['catatan'] }}">
                                            Detail kegiatan
                                        </button>

                                        @if ($aktivitas['status'] !== 'selesai')
                                            <form method="POST"
                                                action="{{ route('fase-tanam.aktivitas.selesai', $aktivitas['id']) }}">
                                                @csrf
                                                <input type="hidden" name="bulan" value="{{ $jadwal['bulan'] }}">
                                                <input type="hidden" name="tanggal" value="{{ $jadwal['tanggal_terpilih'] }}">
                                                <input type="hidden" name="lahan_id" value="{{ $jadwal['filter']['lahan_id'] }}">
                                                <input type="hidden" name="status" value="{{ $jadwal['filter']['status'] }}">
                                                <button type="submit" class="btn-fase btn-fase--kecil">
                                                    Tandai selesai
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="jadwal-empty">
                <strong>Belum ada kegiatan pada tanggal ini.</strong>
                <p>Pilih tanggal lain atau tambahkan aktivitas baru.</p>
                <button type="button" class="btn-fase btn-fase--utama"
                    data-jadwal-modal-buka="jadwal-form-dialog">
                    Tambah aktivitas
                </button>
            </div>
        @endif
    </section>

    {{-- Kegiatan berikutnya --}}
    <section class="panel jadwal-panel" aria-labelledby="jadwal-berikutnya-judul">
        <div class="jadwal-panel__heading">
            <div>
                <h2 id="jadwal-berikutnya-judul" class="jadwal-panel__title">Berikutnya</h2>
                <p class="jadwal-panel__sub">Kegiatan mendatang yang masih menunggu.</p>
            </div>

            <button type="button" class="btn-fase"
                data-jadwal-modal-buka="jadwal-form-dialog">
                Tambah pengingat
            </button>
        </div>

        @if (count($jadwal['aktivitas_berikutnya']) > 0)
            <ul class="jadwal-upcoming">
                @foreach ($jadwal['aktivitas_berikutnya'] as $aktivitas)
                    <li>
                        <strong>{{ $aktivitas['tanggal_label'] }} · {{ $aktivitas['waktu_label'] }}</strong>
                        — {{ $aktivitas['kegiatan'] }}, {{ $aktivitas['lahan_nama'] }}
                    </li>
                @endforeach
            </ul>
        @else
            <div class="jadwal-empty jadwal-empty--ringkas">
                Belum ada kegiatan mendatang.
            </div>
        @endif
    </section>

    {{-- Dialog tambah aktivitas --}}
    <dialog class="jadwal-dialog" id="jadwal-form-dialog" data-jadwal-dialog>
        <div class="jadwal-dialog__header">
            <div>
                <p class="jadwal-dialog__eyebrow">Jadwal pertanian</p>
                <h2>Tambah aktivitas</h2>
            </div>
            <button type="button" class="jadwal-dialog__close"
                data-jadwal-dialog-tutup aria-label="Tutup">&times;</button>
        </div>

        <form method="POST" action="{{ route('fase-tanam.aktivitas.store') }}" class="jadwal-form">
            @csrf
            <input type="hidden" name="bulan" value="{{ $jadwal['bulan'] }}">
            <input type="hidden" name="filter_lahan" value="{{ $jadwal['filter']['lahan_id'] }}">
            <input type="hidden" name="filter_status" value="{{ $jadwal['filter']['status'] }}">

            <label class="jadwal-field jadwal-field--full">
                <span>Nama kegiatan</span>
                <input type="text" name="kegiatan" maxlength="120"
                    value="{{ old('kegiatan') }}"
                    placeholder="Contoh: Pemeriksaan hama" required>
            </label>

            <label class="jadwal-field">
                <span>Lahan</span>
                <select name="lahan_id" required @disabled(count($jadwal['opsi_lahan']) === 0)>
                    <option value="">Pilih lahan</option>
                    @foreach ($jadwal['opsi_lahan'] as $opsi)
                        <option value="{{ $opsi['id'] }}" @selected((string) old('lahan_id') === (string) $opsi['id'])>
                            {{ $opsi['nama'] }}
                        </option>
                    @endforeach
                </select>
                @if (count($jadwal['opsi_lahan']) === 0)
                    <small>Tambahkan data lahan terlebih dahulu.</small>
                @endif
            </label>

            <label class="jadwal-field">
                <span>Tanggal</span>
                <input type="date" name="tanggal"
                    value="{{ old('tanggal', $jadwal['tanggal_terpilih']) }}" required>
            </label>

            <label class="jadwal-field">
                <span>Waktu</span>
                <input type="time" name="waktu" value="{{ old('waktu', '08:00') }}" required>
            </label>

            <label class="jadwal-field">
                <span>Prioritas</span>
                <select name="prioritas" required>
                    <option value="normal" @selected(old('prioritas', 'normal') === 'normal')>Normal</option>
                    <option value="tinggi" @selected(old('prioritas') === 'tinggi')>Tinggi</option>
                </select>
            </label>

            <label class="jadwal-field">
                <span>Pengingat</span>
                <select name="pengingat" required>
                    <option value="0" @selected(old('pengingat') === '0')>Tidak ada</option>
                    <option value="30" @selected(old('pengingat', '30') === '30')>30 menit sebelum</option>
                    <option value="60" @selected(old('pengingat') === '60')>1 jam sebelum</option>
                </select>
            </label>

            <label class="jadwal-field jadwal-field--full">
                <span>Catatan (opsional)</span>
                <textarea name="catatan" rows="3" maxlength="500"
                    placeholder="Tambahkan keterangan kegiatan...">{{ old('catatan') }}</textarea>
            </label>

            <p class="jadwal-form__hint">
                Pengingat pada tahap ini hanya tersimpan sebagai informasi jadwal, belum mengirim notifikasi otomatis.
            </p>

            <div class="jadwal-dialog__actions">
                <button type="button" class="btn-fase" data-jadwal-dialog-tutup>Batal</button>
                <button type="submit" class="btn-fase btn-fase--utama"
                    @disabled(count($jadwal['opsi_lahan']) === 0)>
                    Simpan aktivitas
                </button>
            </div>
        </form>
    </dialog>

    {{-- Dialog detail kegiatan --}}
    <dialog class="jadwal-dialog jadwal-dialog--detail" id="jadwal-detail-dialog" data-jadwal-detail-dialog>
        <div class="jadwal-dialog__header">
            <div>
                <p class="jadwal-dialog__eyebrow">Informasi kegiatan</p>
                <h2 data-detail-kegiatan>Detail kegiatan</h2>
            </div>
            <button type="button" class="jadwal-dialog__close"
                data-jadwal-detail-tutup aria-label="Tutup">&times;</button>
        </div>

        <dl class="jadwal-detail-list">
            <div><dt>Lahan</dt><dd data-detail-lahan>-</dd></div>
            <div><dt>Tanggal</dt><dd data-detail-tanggal>-</dd></div>
            <div><dt>Waktu</dt><dd data-detail-waktu>-</dd></div>
            <div><dt>Prioritas</dt><dd data-detail-prioritas>-</dd></div>
            <div><dt>Pengingat</dt><dd data-detail-pengingat>-</dd></div>
            <div><dt>Status</dt><dd data-detail-status>-</dd></div>
            <div class="jadwal-detail-list__full"><dt>Catatan</dt><dd data-detail-catatan>-</dd></div>
        </dl>

        <div class="jadwal-dialog__actions">
            <button type="button" class="btn-fase" data-jadwal-detail-tutup>Tutup</button>
        </div>
    </dialog>
</div>
