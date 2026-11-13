# Laporan Perubahan & Daftar Error — Smart Sawah

**Tanggal pemeriksaan:** 5 November 2026
**Disusun oleh:** opencode (hanya dokumentasi — tidak ada kode aplikasi yang diubah)
**Repositori:** `C:\laragon\www\Smart_Monitoring_Pertanian`
**Branch:** `main`

> Dokumen ini adalah file baru. Isinya murni catatan hasil pemeriksaan
> (read-only) terhadap kondisi repositori, bukan perubahan kode.

> **Status tindak lanjut (5 Nov 2026):** E1, E3, E4, E5, dan E6 sudah
> diperbaiki. Rincian ada di `DOKUMENTASI.md`. E2 dan temuan lainnya masih
> menunggu keputusan Lead/pemilik.

---

## 1. Ringkasan

1. Ada **3 perubahan yang belum di-commit**: `routes/web.php`, `resources/css/smart-sawah.css`, dan file baru `resources/views/pages/Riwayat.blade.php`.
2. **Error utama (blocker):** halaman `/riwayat` menghasilkan **HTTP 500** karena memakai `<x-app-layout>`, padahal komponen tersebut tidak ada. Layout proyek ada di `resources/views/layouts/app.blade.php` dan dipakai lewat `@extends('layouts.app')`.
3. **Error potensial (tinggi):** migrasi `2026_10_20_174252_add_kode_wilayah_to_lahan_table` berstatus **Pending**, padahal kolom `kode_wilayah` **sudah ada** di tabel `lahan`. Menjalankan `php artisan migrate` sekarang akan gagal `Duplicate column name 'kode_wilayah'`.
4. **Test gagal:** `php artisan test` → 1 dari 2 test gagal karena driver `pdo_sqlite` tidak tersedia di PHP CLI.
5. Beberapa masalah tampilan: ikon `calendar`, `chevron-down`, `sprout` tidak ada; komponen `notice` mengabaikan isi banner; `topbar` mengabaikan `title`/`subtitle`; breakpoint 380 px hilang dari CSS.

---

## 2. Metode pemeriksaan (read-only)

Perintah yang dijalankan (tanpa mengubah kode):

```powershell
git status
git diff
git log --stat --oneline -4
php -l routes\web.php
php artisan route:list --except-vendor
php artisan migrate:status
php artisan db:table lahan
php artisan test
php artisan tinker   # (render view pages.Riwayat)
```

Selain itu dilakukan pembacaan file: `AGENTS.md`, `routes/web.php`,
`resources/views/pages/Riwayat.blade.php`, `resources/views/layouts/app.blade.php`,
`resources/views/components/*`, `resources/css/smart-sawah.css`,
migrasi `create_lahan_table` dan `add_kode_wilayah_to_lahan_table`,
`database/seeders/SawahSeeder.php`, `.gitignore`, `phpunit.xml`.

---

## 3. Perubahan yang sudah dilakukan

### 3.1 Perubahan yang belum di-commit (working tree)

| File | Status | Ringkasan perubahan |
|---|---|---|
| `routes/web.php` | Modified | Route `/riwayat` diarahkan ke `pages.Riwayat` (sebelumnya `pages.placeholder`); komentar diubah; ada baris kosong ganda; file tidak diakhiri newline. |
| `resources/css/smart-sawah.css` | Modified | Menambah gaya `.history-filters`, `.filter-group`, `.filter-info`, `.badge--success`; mengubah beberapa komentar; file tidak diakhiri newline. |
| `resources/views/pages/Riwayat.blade.php` | **Baru (untracked)** | Halaman Riwayat versi statis/demo: 3 kartu metrik, filter tanggal & pengukuran, tabel 6 baris, banner ringkasan. 149 baris. |

Diff `routes/web.php`:

```diff
-// Halaman lain belum ada di desain: sementara memakai placeholder.
+// Halaman lain:
 Route::view('/kondisi-tanah', 'pages.placeholder', ['title' => 'Kondisi Tanah'])->name('kondisi-tanah');
 Route::view('/rekomendasi', 'pages.placeholder', ['title' => 'Rekomendasi'])->name('rekomendasi');
-Route::view('/riwayat', 'pages.placeholder', ['title' => 'Riwayat'])->name('riwayat');
+
+
+Route::view('/riwayat', 'pages.Riwayat', ['title' => 'Riwayat'])->name('riwayat');
```

Diff CSS (ringkas):

```diff
+/* ===== Riwayat Page Filters ===== */
+.history-filters { display: flex; align-items: flex-end; gap: 14px; margin: 4px 0; }
+.filter-group { display: flex; flex-direction: column; gap: 4px; }
+.filter-info { margin-left: auto; text-align: right; line-height: 1.3; }
+.badge--success { background: transparent; color: var(--green-700); font-weight: 700; padding: 0; }
```

Isi halaman baru `Riwayat.blade.php` (poin penting):

- Baris 1: `<x-app-layout title="Riwayat - Smart Sawah">`
- Baris 5: `<x-sidebar active="riwayat" />`
- Baris 10: `<x-topbar title="Riwayat" subtitle="Selasa, 6 Oktober 2026 · ..." />`
- Baris 13–16 dan 139–145: `<x-notice type="info">` dan `<x-notice type="success">` berisi teks.
- Baris 49–69: filter dengan ikon `calendar`, `chevron-down`, `sprout`.
- Baris 72–136: tabel statis berisi data contoh (68%, 6,4, dst.).
- Semua data masih **hardcoded/demo**, belum memakai `DashboardService`.

### 3.2 Riwayat commit terakhir (sudah masuk Git)

| Commit | Penulis | Ringkasan |
|---|---|---|
| `46a8b36` | cacashalsa27 (5 Nov 2026) | "Update penyelesaian fitur beranda": CSS layout dibuat full width (`!important`), `border-radius: 0`, ikon sidebar jadi bulat, **breakpoint 380 px dihapus**, dan `desktop.ini` ikut ter-commit. |
| `fd975e1` | — | Menambah `AGENTS.md` dan `AGENTS.laravel-default.md`. |
| `2060e35` | indraaa4i (21 Okt 2026) | Menambah migrasi `add_kode_wilayah_to_lahan_table`; `SawahSeeder` menambah `kode_wilayah => 32.12.13.2005` dan mengubah `lokasi` dari `'Lokasi contoh'` menjadi `'Indramayu'`. |
| `1b80cdb` | — | Commit awal dashboard. |

---

## 4. Daftar error & masalah

| ID | Tingkat | Masalah | Status |
|---|---|---|---|
| E1 | **Blocker** | `<x-app-layout>` tidak ada → `/riwayat` HTTP 500 | Terbukti |
| E2 | **Tinggi** | Migrasi `add_kode_wilayah` pending padahal kolom sudah ada | Terbukti |
| E3 | Sedang | `x-topbar` mengabaikan `title`/`subtitle` | Terbukti dari kode |
| E4 | Sedang | `x-notice` mengabaikan `type` dan isi slot | Terbukti dari kode |
| E5 | Sedang | Ikon `calendar`, `chevron-down`, `sprout` tidak terdefinisi | Terbukti dari kode |
| E6 | Sedang | `.gitignore` belum mengabaikan `*.sql` (T3) | Terbukti |
| E7 | Sedang | `php artisan test` gagal: driver sqlite tidak ada | Terbukti |
| E8 | Rendah | `public/build/manifest.json` tidak ada | Terbukti |
| E9 | Rendah | Nama view `pages.Riwayat` memakai huruf kapital (tidak konsisten) | Catatan |
| E10 | Rendah | Kelas `.history-table-card` dipakai tapi tidak didefinisikan | Catatan |
| E11 | Rendah | Halaman Riwayat masih data demo + inline style | Catatan |
| E12 | Catatan | Breakpoint 380 px hilang; desain layout diubah `!important` | Perlu konfirmasi |
| E13 | Catatan | `desktop.ini` ter-track di Git | Catatan |
| E14 | Catatan | Status `AGENTS.md` (T2, `lokasi`) berbeda dari isi repo | Catatan |
| E15 | Catatan | Variabel `$lahan` tidak terdefinisi di controller saat menambah `phase`; diperbaiki dengan menangkap return value `lahanTerpilih` ke variabel lokal sebelum dipassing ke view | Diverifikasi |

### E1 — Blocker: `<x-app-layout>` tidak ditemukan

**Gejala:** membuka `/riwayat` → HTTP 500.

**Bukti:**

- `storage/logs/laravel.log` berulang kali mencatat:

  ```text
  local.ERROR: Unable to locate a class or view for component [app-layout].
  ```

  Terakhir pada `2026-11-05 16:14:56`.
- Render view langsung:

  ```text
  InvalidArgumentException: Unable to locate a class or view for component [app-layout].
  ```

**Penyebab:**

- `Riwayat.blade.php` baris 1 memakai sintaks komponen `<x-app-layout>`.
- Laravel akan mencari `resources/views/components/app-layout.blade.php` (tidak ada).
- Layout proyek yang benar ada di `resources/views/layouts/app.blade.php` dan dipakai dengan `@extends('layouts.app')` (lihat `beranda.blade.php` dan `placeholder.blade.php`).
- Layout tersebut sudah memuat `shell`, `sidebar`, `topbar`, dan `notice` sendiri.

**Saran perbaikan (belum dikerjakan, menunggu izin):**

1. Ganti baris 1 menjadi `@extends('layouts.app')`.
2. Tambah `@section('title', 'Riwayat')` dan `@section('content') ... @endsection`.
3. Hapus `shell`, `shell__inner`, `<x-sidebar>`, `<main class="main">`, dan `<x-topbar>` dari halaman karena sudah disediakan layout (mencegah sidebar/topbar ganda).
4. Perbaiki banner `notice` setelah E4 diputuskan.

**Catatan:** bahkan jika komponen `app-layout` dibuat, prop `title` tidak akan bekerja karena layout memakai `@yield('title')`, bukan prop.

### E2 — Tinggi: migrasi `kode_wilayah` akan gagal "duplicate column"

**Bukti:**

```text
Migration name .. Batch / Status
2026_10_20_174252_add_kode_wilayah_to_lahan_table .. Pending
```

`php artisan db:table lahan` menunjukkan kolom `kode_wilayah varchar(20) nullable` **sudah ada**.

`create_lahan_table` baris 17 juga sudah membuat kolom yang sama:

```php
$table->string('kode_wilayah', 20)->nullable();  // tambahan: kode adm4 BMKG
```

**Dampak:**

- Menjalankan `php artisan migrate` pada database ini (atau instalasi baru) akan gagal dengan `Duplicate column name 'kode_wilayah'`.
- Ini persis risiko yang disebut di tugas **T1**.

**Saran:** jangan jalankan `migrate` sebelum Lead memutuskan. Opsi yang bisa dipertimbangkan Lead: membuat migrasi aman diulang dengan `Schema::hasColumn`, atau menghapus migrasi (butuh persetujuan), atau membiarkannya pending.

### E3 — Sedang: `x-topbar` mengabaikan `title` dan `subtitle`

`topbar.blade.php` hanya mendefinisikan prop:

```php
@props(['daftarLahan' => collect(), 'lahan' => null, 'phase' => null, 'appName' => 'Smart Sawah'])
```

Akibatnya `title="Riwayat"` dan `subtitle="..."` pada `Riwayat.blade.php` baris 10 tidak ditampilkan. Judul/subtitle halaman tidak akan muncul.

### E4 — Sedang: `x-notice` mengabaikan `type` dan isi banner

`notice.blade.php` hanya berisi footer statis dan **tidak memakai `$slot`** maupun `$type`. Maka:

- `<x-notice type="info"> ...teks... </x-notice>` → teks di dalamnya tidak tampil.
- `<x-notice type="success"> ...teks... </x-notice>` → teks juga tidak tampil.

Artinya kedua banner di halaman Riwayat (baris 13–16 dan 139–145) tidak akan terlihat.

### E5 — Sedang: ikon yang dipakai belum ada

`icon.blade.php` mendefinisikan: `menu`, `home`, `droplet`, `bulb`, `clock`, `leaf`, `shield`, `flask`, `cloud-sun`, `cloud-rain`, `arrow-right`, `arrow-up-right`, `check-circle`, `info`, `sun`, `cloud`, `alert`, `footprints`.

Ikon yang dipakai `Riwayat.blade.php` tetapi **tidak ada**:

- `calendar` (baris 53)
- `chevron-down` (baris 55, 63)
- `sprout` (baris 61)

Karena `icon.blade.php` memakai `{!! $paths[$name] ?? '' !!}`, ikon yang tidak ada akan menghasilkan `<svg>` kosong (bukan error, tetapi tampil kosong).

### E6 — Sedang: `.gitignore` belum mengabaikan `*.sql` (T3)

Isi `.gitignore` saat ini sudah memuat `.env`, tetapi **tidak** memuat `*.sql`.

Hasil `git ls-files`:

- `.env` **tidak** ter-track (hanya `.env.example` yang ter-track) — aman.
- Tidak ada file `*.sql` yang ter-track — aman.

Jadi belum ada kebocoran, tetapi aturan T3 belum terpenuhi sepenuhnya.

### E7 — Sedang: `php artisan test` gagal karena driver sqlite

Ringkasan hasil:

```text
tests: 2, passed: 1, failed: 1
Tests\Feature\ExampleTest::test_the_application_returns_a_successful_response
Expected response status code [200] but received 500.
PDOException: could not find driver
Connection: sqlite, Database: :memory:
```

**Penyebab:** `phpunit.xml` memakai `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:`, tetapi ekstensi `pdo_sqlite` tidak aktif di PHP CLI yang dipakai.

**Catatan tambahan:** `tests/Feature/ExampleTest.php` tidak memakai `RefreshDatabase`, sehingga meskipun driver sqlite tersedia, test akan memakai database `:memory:` kosong dan berpotensi gagal dengan "no such table: lahan".

### E8 — Rendah: manifest Vite tidak ada

- `public/hot` **ada** (biasanya menandakan `npm run dev` pernah/sedang jalan).
- `public/build/manifest.json` **tidak ada**.

Saat ini bukan error kode. Namun jika halaman dibuka tanpa `npm run dev` dan tanpa `npm run build`, `@vite(...)` bisa gagal dengan "Vite manifest not found". Untuk produksi perlu `npm run build`.

### E9 — Rendah: penamaan view `pages.Riwayat`

Route memakai `pages.Riwayat` (huruf R kapital) dan file-nya juga `Riwayat.blade.php`. Ini **bekerja** di Windows dan Linux karena nama file cocok. Namun tidak konsisten dengan konvensi Laravel (`kebab-case`/huruf kecil). Jika nanti file di-rename menjadi `riwayat.blade.php`, route harus ikut diubah.

### E10 — Rendah: kelas CSS yang tidak ada

- `history-table-card` dipakai di `Riwayat.blade.php` baris 72, tetapi tidak didefinisikan di CSS. Tidak error, hanya tidak ada gaya khusus.
- `resources/css/smart-sawah.css` dan `routes/web.php` tidak diakhiri newline.

### E11 — Rendah: halaman Riwayat masih mock

- Data metrik, tabel, tanggal, dan jam masih hardcoded.
- Memakai beberapa `style="..."` inline.
- Belum memakai `DashboardService`/data nyata.
- Judul topbar hardcoded "Selasa, 6 Oktober 2026".

Sesuai `AGENTS.md` bagian 5, logika data seharusnya di `app/Services` dan Blade hanya menampilkan. Untuk tahap desain ini mungkin wajar, tetapi perlu dicatat.

### E12 — Catatan: perubahan desain pada commit `46a8b36`

- `.shell` dan `.shell__inner` dipaksa full width dengan `!important`, `border-radius: 0`, `min-height: 100vh`.
- Ikon sidebar diubah menjadi bulat (`border-radius: 99px`).
- Blok `@media (max-width: 380px)` **dihapus**, padahal `AGENTS.md` bagian 5 menyebut breakpoint responsif: 1100, 1024, 860, 640, **380** px.

Perlu konfirmasi apakah perubahan ini memang sesuai desain Figma.

### E13 — Catatan: `desktop.ini` ter-track di Git

`desktop.ini` ikut ter-commit pada `46a8b36`. Isinya:

```ini
[.ShellClassInfo]
LocalizedResourceName=@Smart_Monitoring_Pertanian,0
```

Tidak berbahaya, tetapi bukan bagian aplikasi. Penghapusan file ter-track butuh persetujuan karena mengubah isi Git.

### E14 — Catatan: `AGENTS.md` vs kondisi repo

- Tugas **T2** (tambah `kode_wilayah` di `SawahSeeder`) **sudah terlihat selesai** di repo.
- Namun `lokasi` di seeder sudah berubah dari `'Lokasi contoh'` menjadi `'Indramayu'`, sedangkan `AGENTS.md` meminta `lokasi` jangan diubah (masih menunggu pemilik, pertanyaan terbuka #2).
- `AGENTS.md` belum mencatat bahwa migrasi `add_kode_wilayah` berstatus **Pending**.

---

## 5. Kesesuaian dengan tugas AKTIF di `AGENTS.md`

| ID | Status menurut dokumen | Temuan |
|---|---|---|
| T1 | AKTIF | Migrasi sudah dibaca. Isinya menambah `kode_wilayah` tanpa pengecekan `Schema::hasColumn`. **Belum diubah/dihapus** sesuai instruksi. Risiko duplicate column terkonfirmasi (lihat E2). |
| T2 | AKTIF | `SawahSeeder` sudah memuat `'kode_wilayah' => '32.12.13.2005'`. Catatan: `lokasi` juga berubah menjadi `'Indramayu'` (di luar instruksi T2). Seeder tidak dijalankan. |
| T3 | AKTIF | `.env` sudah diabaikan dan tidak ter-track. `*.sql` **belum** diabaikan. Tidak ada `.env`/`*.sql` yang ter-track. |

Tugas **MENUNGGU** (T4–T7) tidak disentuh.

---

## 6. Yang belum bisa diverifikasi

- Halaman `/riwayat` **belum bisa dinilai tampilannya** karena masih 500 (E1).
- Beranda belum diuji ulang lewat browser pada sesi ini (pemeriksaan hanya lewat kode dan log).
- `php artisan test` tidak bisa dijadikan bukti kualitas karena driver sqlite tidak tersedia (E7).
- Perilaku cuaca BMKG tidak diuji ulang pada sesi ini.

---

## 7. Rekomendasi urutan perbaikan (usulan, belum dikerjakan)

1. **Perbaiki E1** lebih dulu: ubah `Riwayat.blade.php` memakai `@extends('layouts.app')` dan hapus sidebar/topbar ganda. Ini membuka halaman `/riwayat`.
2. **Putuskan E2 bersama Lead** sebelum menjalankan `php artisan migrate`.
3. Sesuaikan komponen: tambah ikon (E5), dukung slot/type pada `notice` (E4), dan tampilkan judul halaman pada `topbar` (E3).
4. Tambahkan `*.sql` ke `.gitignore` (E6/T3).
5. Aktifkan `pdo_sqlite` atau sesuaikan konfigurasi test (E7).
6. Konfirmasi desain E12 dan keputusan untuk `desktop.ini` (E13).
7. Setelah halaman jalan, ganti data demo dengan data dari service (E11) sesuai desain final.

---

## 8. Lampiran output perintah

### `git status --short`

```text
 M resources/css/smart-sawah.css
 M routes/web.php
?? resources/views/pages/Riwayat.blade.php
```

### `php artisan route:list --except-vendor`

```text
 GET|HEAD / .. beranda › DashboardController
 GET|HEAD kondisi-tanah .. kondisi-tanah
 GET|HEAD rekomendasi .. rekomendasi
 GET|HEAD riwayat .. riwayat

 Showing [4] routes
```

### `php artisan migrate:status`

```text
 Migration name .. Batch / Status
 2026_10_16_080946_create_sessions_table .. [1] Ran
 2026_10_16_080954_create_cache_table .. [1] Ran
 2026_10_16_081004_create_jobs_table .. [1] Ran
 2026_10_17_100001_create_petani_table .. [1] Ran
 2026_10_17_100002_create_lahan_table .. [1] Ran
 2026_10_20_174252_add_kode_wilayah_to_lahan_table .. Pending
```

### `php artisan db:table lahan` (ringkas)

```text
 Columns .. 10
 kode_wilayah varchar, nullable .. varchar(20)
 ...
```

### Render `pages.Riwayat`

```text
InvalidArgumentException: Unable to locate a class or view for component [app-layout].
```

### `php artisan test` (ringkas)

```text
tests: 2, passed: 1, failed: 1
Expected response status code [200] but received 500.
PDOException: could not find driver (Connection: sqlite, Database: :memory:)
```

### `php -l routes\web.php`

```text
No syntax errors detected in routes\web.php
```
