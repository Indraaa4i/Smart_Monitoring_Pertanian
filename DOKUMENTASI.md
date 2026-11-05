# DOKUMENTASI PERBAIKAN — Smart Sawah

**Tanggal:** 5 November 2026
**Branch:** `main`
**Ruang lingkup:** perbaikan error halaman Riwayat dan komponen pendukungnya.
**Catatan penting:** tidak ada migrasi, tabel, atau data database yang diubah.
`vendor/`, `node_modules/`, dan `.env` tidak disentuh.

Dokumen ini melengkapi laporan awal di
`docs/LAPORAN-PERUBAHAN-DAN-ERROR.md`.

---

## 1. Ringkasan perbaikan

| ID | Masalah | Status |
|---|---|---|
| E1 | `<x-app-layout>` tidak ada → `/riwayat` HTTP 500 | **Diperbaiki** |
| E3 | `x-topbar` mengabaikan `title`/`subtitle` | **Diperbaiki** |
| E4 | `x-notice` mengabaikan `type` dan isi slot | **Diperbaiki** |
| E5 | Ikon `calendar`, `chevron-down`, `sprout` tidak ada | **Diperbaiki** |
| E6 | `.gitignore` belum mengabaikan `*.sql` | **Diperbaiki** |

Yang **sengaja belum** diperbaiki (butuh keputusan Lead/pemilik atau di luar
lingkup kode) dijelaskan di bagian 5.

---

## 2. Detail perbaikan

### 2.1 E1 — Halaman `/riwayat` HTTP 500

**Sebelum:**

`resources/views/pages/Riwayat.blade.php` memakai:

```blade
<x-app-layout title="Riwayat - Smart Sawah">
    <div class="shell">
        <div class="shell__inner">
            <x-sidebar active="riwayat" />
            <main class="main">
                <x-topbar title="Riwayat" subtitle="..." />
                ...
```

Masalah:

- Laravel mencari `resources/views/components/app-layout.blade.php`, yang tidak ada.
- Layout proyek yang benar ada di `resources/views/layouts/app.blade.php`.
- Layout tersebut **sudah** memuat `shell`, `shell__inner`, `sidebar`, `topbar`,
  dan `notice` sendiri, sehingga kalau dibiarkan akan terjadi duplikasi.

**Sesudah:**

`Riwayat.blade.php` sekarang mengikuti pola halaman lain
(`beranda.blade.php` dan `placeholder.blade.php`):

```blade
@extends('layouts.app')

@section('title', 'Riwayat')
@section('page-title', 'Riwayat')
@section('page-subtitle', 'Selasa, 6 Oktober 2026 · Lihat kembali hasil pemantauan sawah')

@section('content')
    ...
@endsection
```

Pembungkus `shell`, `shell__inner`, `sidebar`, `main`, dan `topbar` dihapus dari
halaman karena sudah disediakan oleh layout.

**File:** `resources/views/pages/Riwayat.blade.php`

---

### 2.2 E3 — Judul halaman pada `topbar`

**Sebelum:** `topbar.blade.php` hanya punya prop
`daftarLahan`, `lahan`, `phase`, `appName`. Prop `title`/`subtitle` dari halaman
diabaikan.

**Sesudah:**

- `topbar.blade.php` menerima prop opsional `title` dan `subtitle`.
- Jika tidak diisi, tampilan kembali ke default (nama aplikasi dan
  "Dashboard pantauan sawah untuk petani"), sehingga **Beranda tidak berubah**.
- `layouts/app.blade.php` membaca section `page-title` dan `page-subtitle`
  lalu meneruskannya ke `topbar`.

```blade
{{-- topbar.blade.php --}}
<p class="topbar__name">{{ $title ?? $appName }}</p>
<p class="topbar__sub">{{ $subtitle ?? 'Dashboard pantauan sawah untuk petani' }}</p>
```

```blade
{{-- layouts/app.blade.php --}}
@php
    $pageTitle = trim(View::getSection('page-title'));
    $pageSubtitle = trim(View::getSection('page-subtitle'));
@endphp
...
<x-topbar ... :title="$pageTitle ?: null" :subtitle="$pageSubtitle ?: null" />
```

Halaman lain yang tidak mendefinisikan `page-title`/`page-subtitle` tetap
memakai tampilan lama.

**File:**
- `resources/views/components/topbar.blade.php`
- `resources/views/layouts/app.blade.php`

---

### 2.3 E4 — `x-notice` mendukung isi banner

**Sebelum:** `notice.blade.php` hanya menampilkan footer statis dan tidak
memakai `$slot`, sehingga banner di halaman Riwayat tidak muncul.

**Sesudah:** komponen menampilkan isi slot jika ada; jika kosong, footer default
tetap tampil seperti sebelumnya.

```blade
@props(['type' => 'info'])
@if ($slot->isEmpty())
    <footer class="notice">
        <x-icon name="info" :size="14" />
        <p>Smart Sawah membantu memantau ...</p>
    </footer>
@else
    <div @class(['notice', 'notice--'.$type])>
        {{ $slot }}
    </div>
@endif
```

Tambahan gaya di `resources/css/smart-sawah.css`:

```css
.notice--info { background: var(--green-50); }
.notice--success { align-items: flex-start; background: var(--green-50); border-color: var(--green-100); }
.notice--success strong { color: var(--green-900); }
```

Penggunaan lama `<x-notice />` di layout tetap berfungsi tanpa perubahan.

**File:**
- `resources/views/components/notice.blade.php`
- `resources/css/smart-sawah.css`

---

### 2.4 E5 — Ikon yang hilang

Ditambahkan tiga ikon ke `resources/views/components/icon.blade.php`:

- `calendar`
- `chevron-down`
- `sprout`

Ketiganya dipakai oleh filter di halaman Riwayat. Sebelumnya ikon tampil kosong
karena `$paths[$name] ?? ''`.

**File:** `resources/views/components/icon.blade.php`

---

### 2.5 E6 — `.gitignore` mengabaikan `*.sql`

Ditambahkan satu baris pada `.gitignore`:

```text
*.sql
```

Dengan demikian dump database tidak akan ikut ter-commit. `.env` sudah diabaikan
sebelumnya, dan tidak ada `.env`/`*.sql` yang ter-track di Git.

**File:** `.gitignore`

---

## 3. Daftar file yang diubah/dibuat

| File | Status | Keterangan |
|---|---|---|
| `resources/views/pages/Riwayat.blade.php` | Diubah | Memakai `@extends('layouts.app')`; duplikasi shell/sidebar/topbar dihapus. |
| `resources/views/components/notice.blade.php` | Diubah | Mendukung `$slot` dan `type`; footer default dipertahankan. |
| `resources/views/components/topbar.blade.php` | Diubah | Prop opsional `title` dan `subtitle`. |
| `resources/views/layouts/app.blade.php` | Diubah | Meneruskan `page-title`/`page-subtitle` ke topbar. |
| `resources/views/components/icon.blade.php` | Diubah | Tambah `calendar`, `chevron-down`, `sprout`. |
| `resources/css/smart-sawah.css` | Diubah | Tambah gaya `.notice--info`, `.notice--success`. |
| `.gitignore` | Diubah | Tambah `*.sql`. |
| `DOKUMENTASI.md` | **Baru** | Dokumen ini. |

Perubahan `routes/web.php`, `resources/css/smart-sawah.css` (bagian Riwayat), dan
pembuatan `Riwayat.blade.php` sebelumnya sudah ada di working tree; dokumen ini
mencatat perbaikan yang dilakukan setelahnya.

---

## 4. Verifikasi

### 4.1 Kompilasi Blade

```text
php artisan view:cache
 INFO Blade templates cached successfully.

php artisan view:clear
 INFO Compiled views cleared successfully.
```

### 4.2 Uji render route

Dilakukan lewat HTTP kernel Laravel (bukan menebak dari kode):

```text
/            => HTTP 200 | panjang 15363 | tidak ditemukan: -
/riwayat     => HTTP 200 | panjang 13864 | tidak ditemukan: -
/kondisi-tanah => HTTP 200 | panjang 4717 | tidak ditemukan: -
```

Pemeriksaan isi `/riwayat`:

```text
/riwayat => HTTP 200 | tidak ditemukan: -
```

Elemen yang dicek dan ditemukan: subtitle halaman, ikon `calendar`,
`chevron-down`, `sprout`, `notice--info`, `notice--success`, dan teks
"KONDISI AMAN".

### 4.3 Status Git setelah perbaikan

```text
 M .gitignore
 M resources/css/smart-sawah.css
 M resources/views/components/icon.blade.php
 M resources/views/components/notice.blade.php
 M resources/views/components/topbar.blade.php
 M resources/views/layouts/app.blade.php
 M routes/web.php
?? docs/
?? resources/views/pages/Riwayat.blade.php
```

### 4.4 Yang masih gagal (bukan akibat perbaikan ini)

`php artisan test` masih gagal karena driver `pdo_sqlite` tidak tersedia di PHP
CLI. Ini masalah lingkungan, bukan kode halaman Riwayat (lihat bagian 5).

---

## 5. Belum diperbaiki (butuh keputusan)

| ID | Alasan belum diperbaiki |
|---|---|
| E2 | Migrasi `add_kode_wilayah_to_lahan_table` **Pending** padahal kolom sudah ada. Menjalankan `php artisan migrate` akan gagal `Duplicate column name`. Sesuai `AGENTS.md` T1, migrasi tidak boleh diubah/dihapus tanpa keputusan Lead. **Jangan jalankan `migrate` dulu.** |
| E7 | `pdo_sqlite` tidak aktif di PHP CLI. Perlu mengaktifkan ekstensi di `php.ini` atau menyesuaikan konfigurasi test; bukan perubahan kode aplikasi. |
| E8 | `public/build/manifest.json` belum ada. Jalankan `npm run build` untuk produksi atau `npm run dev` saat pengembangan. |
| E9 | Nama view `pages.Riwayat` masih berhuruf kapital. Berfungsi, tetapi tidak konsisten dengan konvensi Laravel. Perlu keputusan sebelum rename. |
| E10 | `.history-table-card` belum punya gaya khusus (tidak error). Beberapa file juga belum diakhiri newline. |
| E11 | Data halaman Riwayat masih demo/hardcoded dan memakai inline style. Menunggu desain final dan data nyata. |
| E12 | Perubahan desain pada commit `46a8b36` (full width `!important`, `border-radius: 0`, breakpoint 380 px dihapus) perlu konfirmasi terhadap Figma. |
| E13 | `desktop.ini` masih ter-track di Git. Penghapusan file ter-track butuh persetujuan. |
| E14 | `AGENTS.md` belum mencatat bahwa migrasi `kode_wilayah` pending dan bahwa `lokasi` seeder berubah menjadi "Indramayu". |

---

## 6. Cara menguji manual

Dari root proyek:

```powershell
npm run dev
php artisan serve
```

Lalu buka:

- `http://127.0.0.1:8000/` → Beranda tetap tampil seperti sebelumnya.
- `http://127.0.0.1:8000/riwayat` → harus **HTTP 200**, judul topbar "Riwayat",
  subtitle tampil, kedua banner tampil, dan ikon filter tampil.
- `http://127.0.0.1:8000/kondisi-tanah` dan `/rekomendasi` → placeholder tetap
  berfungsi.

Jika muncul error, cek `storage/logs/laravel.log` dan pastikan tidak ada lagi
pesan `Unable to locate a class or view for component [app-layout]`.

---

## 7. Catatan tambahan

- Perbaikan ini tidak mengubah skema database dan tidak menjalankan migrasi.
- Tidak ada paket Composer/npm baru.
- Perubahan desain halaman Riwayat mengikuti struktur yang sudah ada di halaman
  Beranda; tidak membuat komponen baru.
- Jika Lead menginginkan pendekatan lain (misalnya membuat komponen
  `app-layout`), perbaikan ini bisa disesuaikan.
