@extends('layouts.app')

@section('content')
<div class="pm-page">

    {{-- HEADER --}}
    <div class="pm-header">
        <div>
            <div class="pm-breadcrumb">Fase Tanam / Pemeliharaan</div>
            <h1>Pemeliharaan Tanaman</h1>
            <p class="pm-subtitle">
                Catat, pantau, dan kelola aktivitas pemeliharaan tanaman.
            </p>
        </div>

        <div class="pm-status">
            <span class="pm-status-dot"></span>
            Data contoh
        </div>
    </div>

    {{-- TAB FASE TANAM --}}
    <div class="pm-tabs">
        <x-fase-tab :tab="$tab" />
    </div>

    {{-- TOP SELECTOR --}}
    <div class="pm-topbar">
        <div class="pm-topbar-left">
            <div class="pm-select-box">
                <span class="pm-select-label">Siklus & Lahan</span>
                <strong>
                    {{ $pemeliharaan['siklus'] ?? 'MH-2026-A' }}
                    ·
                    {{ $pemeliharaan['blok'] ?? 'Blok A' }}
                </strong>
            </div>

            <div class="pm-hst-badge">
                Pemeliharaan · {{ $pemeliharaan['hst'] ?? 6 }} HST
            </div>
        </div>
    </div>

    {{-- RINGKASAN --}}
    <div class="pm-summary">

        <div class="pm-summary-card">
            <div class="pm-card-icon icon-fertilizer">✦</div>
            <div class="pm-card-label">Pupuk berikutnya</div>
            <div class="pm-card-value">
                {{ $pemeliharaan['pemupukan'] ?? '11 Nov' }}
            </div>
            <div class="pm-card-note">
                Pemupukan susulan I · 7 HST
            </div>
        </div>

        <div class="pm-summary-card">
            <div class="pm-card-icon icon-water">≈</div>
            <div class="pm-card-label">Tinggi air terakhir</div>
            <div class="pm-card-value">
                {{ $pemeliharaan['tinggi_air'] ?? '3 cm' }}
            </div>
            <div class="pm-card-note">
                Diperiksa hari ini · 07.00
            </div>
        </div>

        <div class="pm-summary-card">
            <div class="pm-card-icon icon-pest">⌕</div>
            <div class="pm-card-label">Pemeriksaan hama</div>
            <div class="pm-card-value">
                {{ $pemeliharaan['jam_hama'] ?? '15.00' }}
            </div>
            <div class="pm-card-note">
                Hari ini · {{ $pemeliharaan['petugas'] ?? 'Pak Budi' }}
            </div>
        </div>

    </div>

    {{-- CATAT KEGIATAN PEMELIHARAAN --}}
    <div class="pm-panel pm-activity-panel">

        <div class="pm-panel-heading">
            <div>
                <h2>Catat kegiatan pemeliharaan</h2>
                <p>Pilih kategori untuk menambahkan catatan sesuai kegiatan lapangan.</p>
            </div>
        </div>

        <div class="pm-category-grid">

            <button type="button" class="pm-category-btn"
                onclick="openPmModal('modalPemupukan')">
                Pemupukan
            </button>

            <button type="button" class="pm-category-btn"
                onclick="openPmModal('modalPengairan')">
                Pengairan
            </button>

            <button type="button" class="pm-category-btn"
                onclick="openPmModal('modalHama')">
                Pengendalian hama
            </button>

            <button type="button" class="pm-category-btn"
                onclick="openPmModal('modalPenyakit')">
                Penyakit
            </button>

            <button type="button" class="pm-category-btn"
                onclick="openPmModal('modalPemeriksaan')">
                Pemeriksaan
            </button>

        </div>
    </div>

    {{-- PEMERIKSAAN TANAMAN --}}
    <div class="pm-panel">
        <div class="pm-panel-heading">
            <div>
                <h2>Pemeriksaan tanaman</h2>
                <p>Catat kondisi pertumbuhan tanaman saat pemeriksaan.</p>
            </div>
        </div>

        <form id="formPemeriksaanUtama" class="pm-main-form">

            <div class="pm-form-grid">

                <div class="pm-field">
                    <label>Tanggal pemeriksaan</label>
                    <input type="date" name="tanggal" class="pm-input"
                        value="{{ now('Asia/Jakarta')->format('Y-m-d') }}">
                </div>

                <div class="pm-field">
                    <label>Petugas</label>
                    <input type="text" name="petugas" class="pm-input"
                        placeholder="Nama petugas">
                </div>

                <div class="pm-field">
                    <label>Kondisi tanaman</label>
                    <select name="kondisi" class="pm-input">
                        <option value="">Pilih kondisi</option>
                        <option>Baik</option>
                        <option>Normal</option>
                        <option>Kurang baik</option>
                        <option>Perlu penanganan</option>
                    </select>
                </div>

                <div class="pm-field">
                    <label>Tinggi tanaman (cm)</label>
                    <input type="number" name="tinggi" class="pm-input"
                        min="0" step="0.1" placeholder="Contoh: 25">
                </div>

                <div class="pm-field">
                    <label>Intensitas serangan hama (%)</label>
                    <input type="number" name="hama" class="pm-input"
                        min="0" max="100" placeholder="0 - 100">
                </div>

                <div class="pm-field">
                    <label>Gejala penyakit</label>
                    <input type="text" name="gejala" class="pm-input"
                        placeholder="Masukkan gejala jika ditemukan">
                </div>

                <div class="pm-field pm-field-full">
                    <label>Hasil pemeriksaan / catatan</label>
                    <textarea name="catatan" class="pm-input pm-textarea"
                        placeholder="Tuliskan hasil pemeriksaan tanaman..."></textarea>
                </div>

            </div>

            <div class="pm-form-actions">
                <button type="reset" class="pm-btn pm-btn-secondary">
                    Reset
                </button>
                <button type="submit" class="pm-btn pm-btn-primary">
                    Simpan pemeriksaan
                </button>
            </div>

        </form>
    </div>

    {{-- RIWAYAT --}}
    <div class="pm-panel">
        <div class="pm-panel-heading">
            <div>
                <h2>Riwayat pemeliharaan</h2>
                <p>Daftar aktivitas pemeliharaan tanaman yang tercatat.</p>
            </div>
        </div>

        <div class="pm-table-wrap">
            <table class="pm-table" id="pmHistoryTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Detail kegiatan</th>
                        <th>Petugas</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>10 Nov 2026</td>
                        <td><span class="pm-type pm-type-water">Pengairan</span></td>
                        <td>Pengairan sawah · Tinggi air 3 cm</td>
                        <td>Pak Budi</td>
                        <td><span class="pm-status-label">Selesai</span></td>
                    </tr>

                    <tr>
                        <td>10 Nov 2026</td>
                        <td><span class="pm-type pm-type-check">Pemeriksaan</span></td>
                        <td>Pemeriksaan kondisi tanaman</td>
                        <td>Pak Budi</td>
                        <td><span class="pm-status-label">Selesai</span></td>
                    </tr>

                    <tr>
                        <td>9 Nov 2026</td>
                        <td><span class="pm-type pm-type-fertilizer">Pemupukan</span></td>
                        <td>Pemupukan dasar</td>
                        <td>Pak Asep</td>
                        <td><span class="pm-status-label">Selesai</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>


{{-- ================================================= --}}
{{-- MODAL PEMUPUKAN --}}
{{-- ================================================= --}}
<div class="pm-modal-overlay" id="modalPemupukan">
    <div class="pm-modal">

        <div class="pm-modal-header">
            <div>
                <h3>Catat pemupukan</h3>
                <p>Tambahkan data kegiatan pemupukan tanaman.</p>
            </div>
            <button type="button" class="pm-close"
                onclick="closePmModal('modalPemupukan')">×</button>
        </div>

        <form class="pm-modal-form" data-category="Pemupukan"
            data-detail="Pemupukan"
            onsubmit="savePmActivity(event, this)">

            <div class="pm-modal-body">
                <div class="pm-form-grid">

                    <div class="pm-field">
                        <label>Tanggal kegiatan <span>*</span></label>
                        <input type="date" name="tanggal" class="pm-input" required>
                    </div>

                    <div class="pm-field">
                        <label>Jenis pupuk <span>*</span></label>
                        <select name="jenis" class="pm-input" required>
                            <option value="">Pilih jenis pupuk</option>
                            <option>Urea</option>
                            <option>NPK</option>
                            <option>SP-36</option>
                            <option>KCl</option>
                            <option>Organik</option>
                            <option>ZA</option>
                            <option>Lainnya</option>
                        </select>
                    </div>

                    <div class="pm-field">
                        <label>Jumlah digunakan (kg) <span>*</span></label>
                        <input type="number" name="jumlah" class="pm-input"
                            min="0" step="0.1" placeholder="Jumlah pupuk" required>
                    </div>

                    <div class="pm-field">
                        <label>Luas aplikasi (ha) <span>*</span></label>
                        <input type="number" name="luas" class="pm-input"
                            min="0" step="0.01" placeholder="Luas lahan" required>
                    </div>

                    <div class="pm-field">
                        <label>Metode aplikasi</label>
                        <select name="metode" class="pm-input">
                            <option value="">Pilih metode</option>
                            <option>Tabur</option>
                            <option>Tugal</option>
                            <option>Larikan</option>
                            <option>Semprot</option>
                            <option>Lainnya</option>
                        </select>
                    </div>

                    <div class="pm-field">
                        <label>Petugas</label>
                        <input type="text" name="petugas" class="pm-input"
                            placeholder="Nama petugas">
                    </div>

                </div>

                <div class="pm-note">
                    Contoh data kegiatan. Dosis mengikuti rencana pemupukan petani.
                </div>
            </div>

            <div class="pm-modal-footer">
                <button type="button" class="pm-btn pm-btn-secondary"
                    onclick="closePmModal('modalPemupukan')">Batal</button>
                <button type="submit" class="pm-btn pm-btn-primary">
                    Simpan pemupukan
                </button>
            </div>

        </form>
    </div>
</div>


{{-- ================================================= --}}
{{-- MODAL PENGAIRAN --}}
{{-- ================================================= --}}
<div class="pm-modal-overlay" id="modalPengairan">
    <div class="pm-modal">

        <div class="pm-modal-header">
            <div>
                <h3>Catat pengairan</h3>
                <p>Tambahkan data kegiatan pengairan sawah.</p>
            </div>
            <button type="button" class="pm-close"
                onclick="closePmModal('modalPengairan')">×</button>
        </div>

        <form class="pm-modal-form" data-category="Pengairan"
            data-detail="Pengairan"
            onsubmit="savePmActivity(event, this)">

            <div class="pm-modal-body">
                <div class="pm-form-grid">

                    <div class="pm-field">
                        <label>Tanggal kegiatan <span>*</span></label>
                        <input type="date" name="tanggal" class="pm-input" required>
                    </div>

                    <div class="pm-field">
                        <label>Metode pengairan <span>*</span></label>
                        <select name="metode" class="pm-input" required>
                            <option value="">Pilih metode</option>
                            <option>Irigasi</option>
                            <option>Pompa air</option>
                            <option>Pengairan manual</option>
                            <option>Gravitasi</option>
                            <option>Lainnya</option>
                        </select>
                    </div>

                    <div class="pm-field">
                        <label>Tinggi air awal (cm)</label>
                        <input type="number" name="awal" class="pm-input"
                            min="0" step="0.1" placeholder="Tinggi air awal">
                    </div>

                    <div class="pm-field">
                        <label>Tinggi air akhir (cm) <span>*</span></label>
                        <input type="number" name="akhir" class="pm-input"
                            min="0" step="0.1" placeholder="Tinggi air akhir" required>
                    </div>

                    <div class="pm-field">
                        <label>Durasi (menit)</label>
                        <input type="number" name="durasi" class="pm-input"
                            min="0" placeholder="Durasi pengairan">
                    </div>

                    <div class="pm-field">
                        <label>Petugas</label>
                        <input type="text" name="petugas" class="pm-input"
                            placeholder="Nama petugas">
                    </div>

                </div>
            </div>

            <div class="pm-modal-footer">
                <button type="button" class="pm-btn pm-btn-secondary"
                    onclick="closePmModal('modalPengairan')">Batal</button>
                <button type="submit" class="pm-btn pm-btn-primary">
                    Simpan pengairan
                </button>
            </div>

        </form>
    </div>
</div>


{{-- ================================================= --}}
{{-- MODAL PENGENDALIAN HAMA --}}
{{-- ================================================= --}}
<div class="pm-modal-overlay" id="modalHama">
    <div class="pm-modal">

        <div class="pm-modal-header">
            <div>
                <h3>Catat pengendalian hama</h3>
                <p>Catat hasil pengamatan dan tindakan pengendalian hama.</p>
            </div>
            <button type="button" class="pm-close"
                onclick="closePmModal('modalHama')">×</button>
        </div>

        <form class="pm-modal-form" data-category="Pengendalian hama"
            data-detail="Pengendalian hama"
            onsubmit="savePmActivity(event, this)">

            <div class="pm-modal-body">
                <div class="pm-form-grid">

                    <div class="pm-field">
                        <label>Tanggal kegiatan <span>*</span></label>
                        <input type="date" name="tanggal" class="pm-input" required>
                    </div>

                    <div class="pm-field">
                        <label>Jenis hama <span>*</span></label>
                        <input type="text" name="jenis" class="pm-input"
                            placeholder="Jenis hama yang ditemukan" required>
                    </div>

                    <div class="pm-field">
                        <label>Luas terdampak (ha)</label>
                        <input type="number" name="luas" class="pm-input"
                            min="0" step="0.01" placeholder="Luas terdampak">
                    </div>

                    <div class="pm-field">
                        <label>Intensitas serangan (%)</label>
                        <input type="number" name="intensitas" class="pm-input"
                            min="0" max="100" placeholder="0 - 100">
                    </div>

                    <div class="pm-field">
                        <label>Tindakan <span>*</span></label>
                        <select name="tindakan" class="pm-input" required>
                            <option value="">Pilih tindakan</option>
                            <option>Pengamatan</option>
                            <option>Pengendalian mekanis</option>
                            <option>Pengendalian biologis</option>
                            <option>Pengendalian sesuai arahan petugas</option>
                            <option>Lainnya</option>
                        </select>
                    </div>

                    <div class="pm-field">
                        <label>Petugas</label>
                        <input type="text" name="petugas" class="pm-input"
                            placeholder="Nama petugas">
                    </div>

                </div>
            </div>

            <div class="pm-modal-footer">
                <button type="button" class="pm-btn pm-btn-secondary"
                    onclick="closePmModal('modalHama')">Batal</button>
                <button type="submit" class="pm-btn pm-btn-primary">
                    Simpan pengamatan hama
                </button>
            </div>

        </form>
    </div>
</div>


{{-- ================================================= --}}
{{-- MODAL PENYAKIT --}}
{{-- ================================================= --}}
<div class="pm-modal-overlay" id="modalPenyakit">
    <div class="pm-modal">

        <div class="pm-modal-header">
            <div>
                <h3>Catat pemeriksaan penyakit</h3>
                <p>Catat gejala penyakit dan penanganan tanaman.</p>
            </div>
            <button type="button" class="pm-close"
                onclick="closePmModal('modalPenyakit')">×</button>
        </div>

        <form class="pm-modal-form" data-category="Penyakit"
            data-detail="Pemeriksaan penyakit"
            onsubmit="savePmActivity(event, this)">

            <div class="pm-modal-body">
                <div class="pm-form-grid">

                    <div class="pm-field">
                        <label>Tanggal pemeriksaan <span>*</span></label>
                        <input type="date" name="tanggal" class="pm-input" required>
                    </div>

                    <div class="pm-field">
                        <label>Jenis penyakit / gejala <span>*</span></label>
                        <input type="text" name="jenis" class="pm-input"
                            placeholder="Jenis penyakit atau gejala" required>
                    </div>

                    <div class="pm-field">
                        <label>Intensitas gejala (%)</label>
                        <input type="number" name="intensitas" class="pm-input"
                            min="0" max="100" placeholder="0 - 100">
                    </div>

                    <div class="pm-field">
                        <label>Tindakan <span>*</span></label>
                        <input type="text" name="tindakan" class="pm-input"
                            placeholder="Tindakan penanganan" required>
                    </div>

                    <div class="pm-field">
                        <label>Kondisi daun</label>
                        <select name="daun" class="pm-input">
                            <option value="">Pilih kondisi daun</option>
                            <option>Hijau normal</option>
                            <option>Menguning</option>
                            <option>Bercak</option>
                            <option>Layu</option>
                            <option>Rusak</option>
                            <option>Lainnya</option>
                        </select>
                    </div>

                    <div class="pm-field">
                        <label>Petugas</label>
                        <input type="text" name="petugas" class="pm-input"
                            placeholder="Nama petugas">
                    </div>

                </div>
            </div>

            <div class="pm-modal-footer">
                <button type="button" class="pm-btn pm-btn-secondary"
                    onclick="closePmModal('modalPenyakit')">Batal</button>
                <button type="submit" class="pm-btn pm-btn-primary">
                    Simpan pemeriksaan penyakit
                </button>
            </div>

        </form>
    </div>
</div>


{{-- ================================================= --}}
{{-- MODAL PEMERIKSAAN --}}
{{-- ================================================= --}}
<div class="pm-modal-overlay" id="modalPemeriksaan">
    <div class="pm-modal">

        <div class="pm-modal-header">
            <div>
                <h3>Catat pemeriksaan tanaman</h3>
                <p>Catat kondisi dan perkembangan tanaman.</p>
            </div>
            <button type="button" class="pm-close"
                onclick="closePmModal('modalPemeriksaan')">×</button>
        </div>

        <form class="pm-modal-form" data-category="Pemeriksaan"
            data-detail="Pemeriksaan tanaman"
            onsubmit="savePmActivity(event, this)">

            <div class="pm-modal-body">
                <div class="pm-form-grid">

                    <div class="pm-field">
                        <label>Tanggal pemeriksaan <span>*</span></label>
                        <input type="date" name="tanggal" class="pm-input" required>
                    </div>

                    <div class="pm-field">
                        <label>Tinggi tanaman (cm)</label>
                        <input type="number" name="tinggi" class="pm-input"
                            min="0" step="0.1" placeholder="Tinggi tanaman">
                    </div>

                    <div class="pm-field">
                        <label>Kondisi pertumbuhan <span>*</span></label>
                        <select name="kondisi" class="pm-input" required>
                            <option value="">Pilih kondisi</option>
                            <option>Baik</option>
                            <option>Normal</option>
                            <option>Kurang baik</option>
                            <option>Perlu penanganan</option>
                        </select>
                    </div>

                    <div class="pm-field pm-field-full">
                        <label>Catatan</label>
                        <textarea name="catatan" class="pm-input pm-textarea"
                            placeholder="Catatan hasil pemeriksaan tanaman"></textarea>
                    </div>

                </div>
            </div>

            <div class="pm-modal-footer">
                <button type="button" class="pm-btn pm-btn-secondary"
                    onclick="closePmModal('modalPemeriksaan')">Batal</button>
                <button type="submit" class="pm-btn pm-btn-primary">
                    Simpan pemeriksaan
                </button>
            </div>

        </form>
    </div>
</div>


{{-- ================================================= --}}
{{-- CSS --}}
{{-- ================================================= --}}
<style>
.pm-page {
    color: #20352b;
    padding-bottom: 30px;
}

.pm-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
}

.pm-breadcrumb {
    color: #819188;
    font-size: 12px;
    margin-bottom: 9px;
}

.pm-header h1 {
    font-size: 25px;
    font-weight: 700;
    margin: 0;
    color: #20352b;
}

.pm-subtitle {
    color: #7b8981;
    font-size: 13px;
    margin: 7px 0 0;
}

.pm-status {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #eaf5ed;
    color: #187748;
    padding: 8px 12px;
    border-radius: 30px;
    font-size: 12px;
    white-space: nowrap;
}

.pm-status-dot {
    width: 7px;
    height: 7px;
    background: #26935b;
    border-radius: 50%;
}

.pm-tabs {
    margin-bottom: 20px;
}

/* TOP SELECTOR */

.pm-topbar {
    background: white;
    border: 1px solid #e8eee9;
    border-radius: 12px;
    padding: 15px 18px;
    margin-bottom: 18px;
}

.pm-topbar-left {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.pm-select-box {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.pm-select-label {
    color: #839188;
    font-size: 11px;
}

.pm-select-box strong {
    font-size: 14px;
    font-weight: 600;
}

.pm-hst-badge {
    background: #f0f7f2;
    color: #187748;
    padding: 8px 11px;
    border-radius: 7px;
    font-size: 12px;
}

/* RINGKASAN */

.pm-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
    margin-bottom: 18px;
}

.pm-summary-card {
    background: white;
    border: 1px solid #e8eee9;
    border-radius: 12px;
    padding: 18px;
    min-width: 0;
}

.pm-card-icon {
    width: 35px;
    height: 35px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-bottom: 13px;
}

.icon-fertilizer {
    color: #25804b;
    background: #eaf5ed;
}

.icon-water {
    color: #2a83a0;
    background: #eaf5fa;
}

.icon-pest {
    color: #b07835;
    background: #fbf2e6;
}

.pm-card-label {
    color: #77857c;
    font-size: 12px;
    margin-bottom: 8px;
}

.pm-card-value {
    color: #20352b;
    font-size: 23px;
    font-weight: 700;
    margin-bottom: 5px;
}

.pm-card-note {
    font-size: 12px;
    color: #849188;
}

/* PANEL */

.pm-panel {
    background: white;
    border: 1px solid #e8eee9;
    border-radius: 13px;
    padding: 20px;
    margin-bottom: 18px;
}

.pm-panel-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    gap: 12px;
}

.pm-panel-heading h2 {
    color: #20352b;
    font-size: 16px;
    font-weight: 700;
    margin: 0;
}

.pm-panel-heading p {
    color: #839087;
    font-size: 12px;
    margin: 6px 0 0;
}

/* BAR CATAT KEGIATAN - DESAIN */

.pm-activity-panel {
    padding: 12px 14px 16px;
}

.pm-activity-panel .pm-panel-heading {
    margin-bottom: 10px;
}

.pm-category-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 10px;
    width: 100%;
}

.pm-category-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    height: 36px;
    padding: 6px 8px;
    background: #ffffff;
    border: 1px solid #e1eae3;
    border-radius: 9px;
    color: #244331;
    font-size: 11px;
    font-weight: 500;
    font-family: inherit;
    white-space: nowrap;
    cursor: pointer;
    transition: background .2s, border-color .2s;
}

.pm-category-btn:hover {
    background: #f5faf6;
    border-color: #b9d7c1;
    color: #187748;
}

.pm-category-btn:focus-visible {
    outline: 2px solid #80b894;
    outline-offset: 2px;
}

/* FORM */

.pm-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 17px;
}

.pm-field {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.pm-field-full {
    grid-column: 1 / -1;
}

.pm-field label {
    font-size: 12px;
    color: #42564a;
    font-weight: 600;
    margin-bottom: 8px;
}

.pm-field label span {
    color: #d94b4b;
}

.pm-input {
    width: 100%;
    min-height: 40px;
    border: 1px solid #dfe8e1;
    border-radius: 8px;
    padding: 10px 11px;
    font-size: 12px;
    color: #34463b;
    background: white;
    outline: none;
    box-sizing: border-box;
    font-family: inherit;
}

.pm-input:focus {
    border-color: #23804b;
    box-shadow: 0 0 0 3px rgba(35,128,75,.08);
}

.pm-input::placeholder {
    color: #a5b0a8;
}

.pm-textarea {
    min-height: 90px;
    resize: vertical;
}

.pm-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

/* BUTTON */

.pm-btn {
    border: none;
    border-radius: 8px;
    padding: 10px 15px;
    min-height: 39px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: .15s;
}

.pm-btn-primary {
    background: #187748;
    color: white;
}

.pm-btn-primary:hover {
    background: #126239;
}

.pm-btn-secondary {
    background: #f4f7f4;
    border: 1px solid #e1e9e2;
    color: #53645a;
}

.pm-btn-secondary:hover {
    background: #edf2ed;
}

/* TABEL RIWAYAT */

.pm-table-wrap {
    overflow-x: auto;
}

.pm-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}

.pm-table th {
    background: #f8faf8;
    color: #839087;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    padding: 12px;
    border-bottom: 1px solid #e9eee9;
}

.pm-table td {
    padding: 13px 12px;
    border-bottom: 1px solid #edf1ed;
    color: #4d5e53;
    font-size: 12px;
}

.pm-table tr:last-child td {
    border-bottom: none;
}

.pm-type {
    display: inline-block;
    padding: 5px 8px;
    border-radius: 6px;
    font-size: 11px;
    white-space: nowrap;
}

.pm-type-water {
    color: #287f9c;
    background: #eaf5fa;
}

.pm-type-check {
    color: #477c53;
    background: #edf6ed;
}

.pm-type-fertilizer {
    color: #9a7835;
    background: #fbf5e7;
}

.pm-status-label {
    display: inline-block;
    color: #25804a;
    background: #eaf5ed;
    padding: 5px 8px;
    border-radius: 6px;
    font-size: 11px;
}

/* MODAL */

.pm-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(23, 35, 28, .45);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 20px;
    overflow-y: auto;
}

.pm-modal-overlay.pm-show {
    display: flex;
}

.pm-modal {
    width: 100%;
    max-width: 580px;
    background: white;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 18px 55px rgba(0,0,0,.16);
    animation: pmModalIn .18s ease;
    margin: auto;
}

@keyframes pmModalIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pm-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px 22px;
    border-bottom: 1px solid #edf1ed;
}

.pm-modal-header h3 {
    color: #20352b;
    font-size: 17px;
    font-weight: 700;
    margin: 0;
}

.pm-modal-header p {
    color: #849188;
    font-size: 12px;
    margin: 6px 0 0;
}

.pm-close {
    border: none;
    background: transparent;
    color: #7f8b83;
    font-size: 25px;
    line-height: 1;
    cursor: pointer;
    padding: 0 2px;
}

.pm-close:hover {
    color: #263c2e;
}

.pm-modal-body {
    padding: 21px 22px;
    max-height: 65vh;
    overflow-y: auto;
}

.pm-note {
    margin-top: 17px;
    padding: 11px 13px;
    background: #f5f9f5;
    color: #708078;
    border-radius: 8px;
    font-size: 11px;
    line-height: 1.6;
}

.pm-modal-footer {
    padding: 15px 22px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-top: 1px solid #edf1ed;
    background: #fcfdfc;
}

/* RESPONSIVE */

@media (max-width: 900px) {
    .pm-category-grid {
        grid-template-columns: repeat(5, minmax(125px, 1fr));
        overflow-x: auto;
        padding-bottom: 2px;
    }
}

@media (max-width: 650px) {
    .pm-header {
        flex-direction: column;
    }

    .pm-summary {
        grid-template-columns: 1fr;
    }

    .pm-category-grid {
        grid-template-columns: repeat(5, minmax(125px, 1fr));
        overflow-x: auto;
    }

    .pm-panel {
        padding: 15px;
    }

    .pm-activity-panel {
        padding: 12px;
    }

    .pm-form-grid {
        grid-template-columns: 1fr;
    }

    .pm-field-full {
        grid-column: auto;
    }

    .pm-modal-overlay {
        padding: 10px;
        align-items: flex-start;
    }

    .pm-modal {
        margin: auto 0;
    }

    .pm-modal-header,
    .pm-modal-body,
    .pm-modal-footer {
        padding-left: 16px;
        padding-right: 16px;
    }

    .pm-modal-footer {
        flex-wrap: wrap;
    }

    .pm-modal-footer .pm-btn {
        flex: 1;
    }
}
</style>


{{-- ================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ================================================= --}}
<script>
    // Buka modal
    function openPmModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;

        modal.classList.add('pm-show');
        document.body.style.overflow = 'hidden';

        // Isi tanggal default hari ini
        const dateInput = modal.querySelector('input[type="date"]');

        if (dateInput && !dateInput.value) {
            const today = new Date();

            dateInput.value =
                `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
        }
    }

    // Tutup modal
    function closePmModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;

        modal.classList.remove('pm-show');
        document.body.style.overflow = '';
    }

    // Klik di luar modal untuk menutup
    document.querySelectorAll('.pm-modal-overlay').forEach(function(modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.remove('pm-show');
                document.body.style.overflow = '';
            }
        });
    });

    // Tutup modal dengan Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.pm-modal-overlay.pm-show').forEach(function(modal) {
                modal.classList.remove('pm-show');
            });

            document.body.style.overflow = '';
        }
    });

    // Format tanggal
    function formatPmDate(dateString) {
        if (!dateString) return '-';

        const date = new Date(dateString + 'T00:00:00');

        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    }

    // Simpan kegiatan ke tabel riwayat (sementara frontend)
    function savePmActivity(event, form) {
        event.preventDefault();

        const formData = new FormData(form);
        const category = form.dataset.category || 'Pemeliharaan';
        const tanggal = formData.get('tanggal') || '';
        const petugas = formData.get('petugas') || '-';

        let detail = [];

        formData.forEach(function(value, key) {
            if (value && key !== 'tanggal' && key !== 'petugas') {
                detail.push(value);
            }
        });

        const detailText = detail.length
            ? detail.join(' · ')
            : (form.dataset.detail || category);

        const tbody = document.querySelector('#pmHistoryTable tbody');
        if (!tbody) return;

        const row = document.createElement('tr');

        const dateCell = document.createElement('td');
        dateCell.textContent = formatPmDate(tanggal);

        const categoryCell = document.createElement('td');
        const categoryBadge = document.createElement('span');
        categoryBadge.className = 'pm-type pm-type-check';
        categoryBadge.textContent = category;
        categoryCell.appendChild(categoryBadge);

        const detailCell = document.createElement('td');
        detailCell.textContent = detailText;

        const officerCell = document.createElement('td');
        officerCell.textContent = petugas;

        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');
        statusBadge.className = 'pm-status-label';
        statusBadge.textContent = 'Selesai';
        statusCell.appendChild(statusBadge);

        row.append(
            dateCell,
            categoryCell,
            detailCell,
            officerCell,
            statusCell
        );

        // Masukkan data terbaru ke bagian atas
        tbody.prepend(row);

        // Reset form & tutup modal
        form.reset();

        const modal = form.closest('.pm-modal-overlay');

        if (modal) {
            modal.classList.remove('pm-show');
        }

        document.body.style.overflow = '';

        alert('Kegiatan pemeliharaan berhasil dicatat (data demo).');
    }

    // Form pemeriksaan utama
    document.getElementById('formPemeriksaanUtama')
        .addEventListener('submit', function(event) {
            event.preventDefault();

            alert('Pemeriksaan berhasil dicatat (data demo).');

            this.reset();
        });
</script>

@endsection