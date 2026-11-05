# AGENTS.md — Smart Sawah (Laravel 13)

> File ini dibaca otomatis oleh opencode. Letakkan di **root proyek**
> (`C:\laragon\www\Smart_Monitoring_Pertanian\AGENTS.md`) dan commit ke Git.
> Jika `/init` dijalankan, **jangan hapus atau timpa** bagian di file ini; tambahkan saja.

## 0. Peran dan cara kerja

- **Lead AI (Claude)** = arsitek dan reviewer. Menentukan desain, skema database, dan daftar tugas (bagian 9).
- **opencode (kamu)** = eksekutor. Mengerjakan kode di repositori ini sesuai tugas yang berstatus **AKTIF**.
- **Pemilik proyek (manusia)** meneruskan laporanmu ke Lead AI untuk ditinjau. Kamu tidak bisa dan tidak perlu menghubungi Lead secara langsung.

Aturan kerja:

1. Baca seluruh file ini sebelum mengubah apa pun.
2. Kerjakan **hanya** tugas berstatus **AKTIF** (bagian 9). Tugas **MENUNGGU** jangan disentuh.
3. Jika perintah ambigu, bertentangan dengan file ini, atau butuh keputusan desain: **berhenti dan tanya**. Jangan menebak.
4. Jangan menambah fitur, paket Composer/npm, atau mengubah desain di luar tugas.
5. Untuk tugas berisiko (database, migrasi), jelaskan rencana dulu dan tunggu persetujuan. Pakai mode Plan bila tersedia.
6. Selesai tugas: verifikasi (bagian 8) dan laporkan dengan format bagian 8.
7. Pekerjaan baru **harus tercatat di tabel tugas (bagian 9) lebih dulu**. Kerjakan di branch `fitur/...` dan buka Pull Request. Jangan commit langsung ke `main`, dan jangan mencampur perubahan desain, kode, dan dokumen dalam satu commit.

## 1. Ringkasan proyek

Dashboard pemantauan kelembapan dan pH tanah sawah berbasis web-IoT untuk petani. Sistem **hanya memantau dan memberi saran**; keputusan dan tindakan tetap di tangan petani, **tanpa kendali irigasi otomatis**.

- Stack: Laravel 13.x, PHP 8.4, MySQL 8, Vite, Blade. Dev di Windows + Laragon (PowerShell).
- Frontend: Blade + CSS biasa (tanpa Tailwind/Bootstrap), komponen Blade anonim.
- Bahasa antarmuka: Indonesia. Komentar kode: Indonesia. Zona waktu: `Asia/Jakarta` (`APP_TIMEZONE`).
- **Tampilan yang berlaku adalah yang ada di `main` saat ini** (diputuskan pemilik pada 2026-11-06, termasuk layout full width dari commit `46a8b36`). Figma milik pemilik sedang disesuaikan mengikuti tampilan itu. Jika Figma dan `main` berbeda, `main` yang benar sampai Figma diperbarui. Jangan mengubah tampilan tanpa alasan jelas.

## 2. Status saat ini (terverifikasi dari dump database 22 Okt 2026)

- Tabel aplikasi **aktif hanya dua**: `petani` dan `lahan`. Satu petani (`pak_tani`, akun demo) dan satu lahan ("Sawah Blok A", id 1).
- `lahan.kode_wilayah` = **`32.12.13.2005`** (Desa Bulak Lor, Kec. Jatibarang, Kab. Indramayu menurut respons BMKG). Kode lama `32.12.13.2001`.
- Tiga tabel lain **menyusul**: `sensor`, `data_sensor`, `rekomendasi`. Migrasinya disimpan di `database/menyusul/` dan **tidak dijalankan**.
- Dashboard tetap berjalan tanpa `data_sensor`: `DashboardService` memakai `Schema::hasTable('data_sensor')`. Kartu sensor menampilkan "Data belum masuk".
- Cuaca diambil dari API BMKG (bagian 6) dan sudah berfungsi.
- Migrasi `2026_10_20_174252_add_kode_wilayah_to_lahan_table` (commit `2060e35`) menambah kolom yang sudah dibuat `create_lahan_table`. Di instalasi baru ia **gagal "Duplicate column name"**. Keputusan Lead: diberi pengecekan `Schema::hasColumn` (T1).
- Halaman Riwayat sudah ada sebagai **UI statis (demo)**. Datanya hardcoded dan belum memakai service.

### Daftar fitur

| Fitur | Status | Catatan |
|---|---|---|
| **Cuaca BMKG** | **Selesai, berfungsi** | Satu-satunya fitur yang memakai data nyata dari sumber eksternal. Lihat bagian 6. |
| Kerangka dashboard (layout, komponen, responsif) | Selesai | Tampilan saja. |
| Fase tanam "Hari ke-N" | Selesai (dasar) | Dihitung dari `lahan.tanggal_tanam` dan `config/sawah.php` → `phases`. |
| Pemilih lahan (dropdown) | Selesai (dasar) | Lewat `?lahan=ID`. Belum dibatasi per petani (menunggu login). |
| Data sensor kelembapan & pH | Belum | Menunggu T4 dan T5. Kartu menampilkan "Data belum masuk". |
| Rekomendasi tersimpan | Belum | Tabel menyusul. Saran saat ini berasal dari `config/sawah.php`. |
| Login petani | Belum | T6. |
| Halaman Riwayat | UI statis (demo) | Data hardcoded. Disambung ke data nyata setelah tabel `data_sensor` aktif (T12). |
| Halaman Kondisi Tanah, Rekomendasi | Placeholder | Belum ada desain. |

## 3. Struktur proyek

```
app/Enums/SensorStatus.php             status Aman / Waspada / Offline
app/Http/Controllers/DashboardController.php
app/Models/                            Petani, Lahan, Sensor, DataSensor, Rekomendasi
app/Services/DashboardService.php      semua logika data Beranda
app/Services/WeatherService.php        klien API BMKG + cache
config/sawah.php                       batas aman, teks saran, definisi parameter, konfigurasi cuaca
database/migrations/                   2 migrasi aktif (petani, lahan) + bawaan Laravel (cache, sessions, jobs)
database/menyusul/                     migrasi + seeder tabel yang belum diaktifkan
database/seeders/SawahSeeder.php
resources/css/smart-sawah.css          seluruh gaya (token di :root)
resources/js/smart-sawah.js            toggle sidebar mobile
resources/views/layouts/app.blade.php
resources/views/pages/                 beranda, riwayat (UI statis), placeholder
resources/views/components/            sidebar, topbar, welcome, phase, metric-card, weather-card, actions, history, notice, badge, icon
routes/web.php
```

## 4. Skema database (otomatis berlaku; ubah hanya atas persetujuan Lead)

**petani**: `id` PK; `username` varchar(50) UNIQUE; `password` varchar(255) hash; `remember_token`; timestamps; `deleted_at`.

**lahan**: `id` PK; `petani_id` FK→petani.id (RESTRICT); `nama_lahan` varchar(100); `luas_lahan` decimal(8,2) null (hektare); `lokasi` varchar(255) null; `kode_wilayah` varchar(20) null (adm4 BMKG); `tanggal_tanam` date null; timestamps; `deleted_at`; UNIQUE(`petani_id`,`nama_lahan`).

**Menyusul** (jangan diaktifkan sebelum T4 disetujui):
- `sensor`: `id`, `lahan_id` FK, `nama_sensor`, timestamps.
- `data_sensor`: `id`, `lahan_id` FK, `sensor_id` FK, `kelembapan` decimal(5,2) null, `ph` decimal(4,2) null, `waktu` datetime; UNIQUE(`sensor_id`,`waktu`); INDEX(`lahan_id`,`waktu`). `lahan_id` harus selalu diturunkan dari sensor yang mengirim.
- `rekomendasi`: `id`, `lahan_id` FK, `status_kelembapan`, `status_ph`, `rekomendasi_pengairan` text, timestamps.

Relasi: petani 1—0..* lahan; lahan 1—0..* sensor / data_sensor / rekomendasi; sensor 1—0..* data_sensor.

## 5. Aturan kode dan arsitektur

- **Blade hanya menampilkan.** Logika data berada di `app/Services`. Batas, teks, dan definisi parameter di `config/sawah.php`. Jangan menulis angka batas di view.
- Tabel berbahasa Indonesia: model wajib memakai `protected $table` eksplisit (Laravel akan menebak `petanis`/`lahans`). Gunakan `SoftDeletes` pada `petani` dan `lahan`.
- Kunci asing memakai `restrictOnDelete()`; riwayat data tidak boleh hilang diam-diam.
- Parameter ukur baru = tambah kolom di `data_sensor` (lewat migrasi baru) **dan** satu entri di `config/sawah.php` → `sensors` (kunci = nama kolom).
- **Jangan mengedit migrasi yang sudah dijalankan.** Perubahan skema = migrasi baru. Migrasi tambahan harus aman diulang (`Schema::hasColumn`) bila menyentuh kolom yang mungkin sudah ada.
- CSS: pakai token di `:root` dan kelas BEM yang ada. Breakpoint responsif: 1100, 1024, 860, 640 px (blok 380 px dihapus pada `46a8b36`; pemulihan opsional lewat T13). Komponen Blade tetap anonim di `resources/views/components`.
- Jangan menambah `!important` baru untuk mengakali layout. Yang sudah ada dari `46a8b36` dibiarkan sampai T13.
- Jangan memakai nama prop `$app` pada komponen Blade (bentrok dengan variabel global Laravel).
- Jangan menaruh `localStorage` atau kunci rahasia di front-end.

## 6. Fitur: Cuaca BMKG

**Status:** selesai dan berfungsi. Ini fitur data eksternal pertama dan, saat ini, satu-satunya yang sudah jadi.

**Tujuan:** menampilkan suhu, kondisi, dan jam hujan pertama hari ini pada kartu "Cuaca hari ini" untuk lahan terpilih. Hanya informasi pendukung, bukan penentu pengairan.

**File terkait:**
- `app/Services/WeatherService.php`
- `config/sawah.php` → `weather` (`base_url`, `timeout`, `cache_minutes`)
- `app/Services/DashboardService.php` → `forLahan()` (kunci `weather`)
- `resources/views/components/weather-card.blade.php`
- ikon `sun`, `cloud`, `cloud-sun`, `cloud-rain` di `components/icon.blade.php`
- kolom `lahan.kode_wilayah`

**Aturan:**
- Endpoint: `https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4={kode_wilayah}`. Tanpa API key. Dilaporkan ada batas sekitar 60 request/menit/IP; jangan memanggil di dalam loop.
- Hasil di-cache 30 menit dengan kunci `bmkg:forecast:{kode}` (+ salinan `:stale` 24 jam, + penanda gagal 2 menit). Kunci memuat kode, jadi mengganti kode otomatis memakai cache baru. `cache:clear` hanya untuk memaksa pembaruan.
- **Wajib mencantumkan "BMKG" sebagai sumber** di kartu cuaca (sudah ada). Jangan menghapusnya.
- Jangan hotlink ikon dari BMKG; pakai ikon internal.
- BMKG tidak memberi "peluang hujan %". Kartu menampilkan slot hujan pertama hari ini (mis. "Hujan ringan · 15.00").
- Jika `kode_wilayah` kosong atau BMKG tidak terjangkau, kartu menampilkan "Data cuaca belum tersedia" dan halaman tetap tampil. Jangan sampai halaman error.

**Cara uji:**

```powershell
php artisan tinker --execute="dump(app(App\Services\WeatherService::class)->forLahan(App\Models\Lahan::first()));"
```

Hasil yang benar: array berisi `temp`, `condition`, `icon`, `rain`, `note`. Hasil `null` berarti `kode_wilayah` kosong atau BMKG tidak terjangkau (cek `storage/logs/laravel.log`, cari "BMKG").

## 7. Larangan keras

- **Jangan** menjalankan `migrate:fresh`, `migrate:refresh`, `db:wipe`, `DROP`/`TRUNCATE`, atau menghapus data **tanpa izin tertulis dari pemilik proyek** di percakapan.
- **Jangan** menghapus atau menimpa file bawaan Laravel (mis. `app/Http/Controllers/Controller.php`, `config/*`, `bootstrap/*`). Saat menyalin, gabungkan file, bukan mengganti seluruh folder.
- **Jangan** mengedit `vendor/` atau `node_modules/`.
- **Jangan** mengunduh atau menjalankan skrip pemasang dari internet (mis. `php.new`, `curl | bash`, `iex`). PHP, Composer, Node, dan MySQL sudah tersedia lewat Laragon. Jika salah satunya tidak ditemukan, **berhenti dan laporkan**.
- **Jangan** mengarahkan test (`php artisan test`) ke database `smart_sawah`. Test harus memakai database terpisah atau sqlite `:memory:`; test dengan `RefreshDatabase` pada database utama akan menghapus datanya.
- **Jangan** membaca/menampilkan isi `.env`, dan jangan commit `.env`, dump `*.sql`, atau kredensial. Akun demo `pak_tani` hanya untuk pengembangan.
- **Jangan** membuat endpoint penerima data IoT, fitur login, atau mengaktifkan tabel "menyusul" sebelum tugasnya berstatus AKTIF.

## 8. Verifikasi dan format laporan

Setelah tugas, jalankan yang relevan (PowerShell, dari root proyek):

```powershell
composer dump-autoload
php artisan optimize:clear
php artisan migrate:status
php artisan route:list
Get-ChildItem app,resources,config,routes,database -Recurse -Include *.php | Select-String -Pattern "Plot\b|forPlot|SensorReading|'moisture'"   # harus kosong
php artisan test   # jika ada tes
```

Buka `http://127.0.0.1:8000` dan pastikan halaman memuat tanpa error (butuh `npm run dev` dan `php artisan serve`).

Format laporan ke pemilik proyek:

1. **Ringkasan** (2–3 kalimat).
2. **File diubah/dibuat/dihapus** (path lengkap).
3. **Perintah dijalankan + hasil** (tempel output apa adanya).
4. **Belum bisa diverifikasi** (jujur; jangan klaim berhasil tanpa bukti).
5. **Pertanyaan / butuh keputusan Lead.**

## 9. Daftar tugas (dikendalikan Lead; urut prioritas)

| ID | Status | Tugas |
|---|---|---|
| T1 | **AKTIF** | Keputusan Lead: ubah `2026_10_20_174252_add_kode_wilayah_to_lahan_table.php` agar `up()` langsung `return` bila `Schema::hasColumn('lahan', 'kode_wilayah')`, dan kosongkan `down()` (kolom dimiliki `create_lahan_table`). Ini pengecualian atas aturan "jangan edit migrasi yang sudah dijalankan" karena hanya menambah penjagaan; database yang sudah menjalankannya tidak terpengaruh. Verifikasi di database kosong terpisah (mis. `$env:DB_DATABASE="smart_sawah_uji"; php artisan migrate`), bukan di database utama. |
| T2 | SELESAI | `SawahSeeder` memuat `kode_wilayah` `32.12.13.2005`. `lokasi` kini "Indramayu". |
| T3 | SELESAI | `.gitignore` memuat `.env` dan `*.sql` (E6). Tidak ada `.env`/`*.sql` yang ter-track. |
| T8 | **AKTIF** | Ganti nama `pages/Riwayat.blade.php` menjadi `pages/riwayat.blade.php` dengan `git mv` (dua langkah lewat nama sementara, karena Windows tidak membedakan huruf besar-kecil) dan ubah route menjadi `pages.riwayat`. |
| T9 | **AKTIF** | `git rm --cached desktop.ini` dan tambahkan `desktop.ini` ke `.gitignore`. Tidak mengubah file lain. |
| T10 | MENUNGGU (pemilik) | Aktifkan `pdo_sqlite` di `php.ini` masing-masing komputer (pengaturan lokal, bukan perubahan repositori). Perbaiki `tests/Feature/ExampleTest.php` setelahnya. |
| T11 | SELESAI | Pemilik memutuskan tampilan saat ini (full width, sudut tidak membulat, ikon sidebar bulat) sebagai desain yang berlaku dan menyesuaikan Figma. Tidak perlu konfirmasi lagi. |
| T12 | MENUNGGU | Sambungkan Riwayat ke data nyata lewat service setelah T4. Ganti tanggal hardcoded dengan `now()`. |
| T13 | MENUNGGU (prioritas rendah) | Rapikan CSS dari `46a8b36` tanpa mengubah tampilan: hilangkan `!important` dengan memperbaiki aturan sumbernya, dan kembalikan blok 380 px (daftar menu sidebar satu kolom di layar sangat kecil). Verifikasi dengan membandingkan tangkapan layar sebelum dan sesudah di lebar 1440, 1024, 768, 375 px. |
| T4 | MENUNGGU | Aktifkan `sensor`, `data_sensor`, `rekomendasi`: pindahkan migrasi dari `database/menyusul/` ke `database/migrations/` lalu `php artisan migrate` (tanpa `fresh`). Butuh persetujuan. |
| T5 | MENUNGGU | Endpoint HTTP penerima data IoT (`POST` JSON, token per perangkat, simpan ke `data_sensor`, `lahan_id` dari sensor, idempoten lewat UNIQUE sensor+waktu). Butuh spesifikasi dari Lead. |
| T6 | MENUNGGU | Login petani. Pemilik proyek belum memutuskan **username** (sesuai ERD terbaru) atau **email**. Jangan dikerjakan. |
| T7 | MENUNGGU | Sinkronisasi ERD dengan database (dikerjakan Lead/pemilik, bukan opencode). |

## 10. Pertanyaan terbuka untuk pemilik proyek

1. Login memakai username atau email? (T6)
2. Isi `lahan.lokasi` yang lebih rinci? Seeder kini "Indramayu".

## 11. Cara memperbarui file ini

- **Fitur baru** = tambahkan satu subbagian "Fitur: …" mengikuti templat bagian 6 (Status, Tujuan, File terkait, Aturan, Cara uji). Lalu perbarui tabel fitur di bagian 2 dan daftar tugas di bagian 9.
- Hanya Lead dan pemilik proyek yang mengubah status tugas (bagian 9) dan skema (bagian 4). opencode boleh menambah catatan "Cara uji" setelah benar-benar diverifikasi.
- Jaga file tetap ringkas. Jika melebihi sekitar 300 baris, pindahkan rincian per fitur ke `docs/fitur/<nama>.md` dan daftarkan di `opencode.json` pada kunci `instructions` (misalnya `["docs/fitur/*.md"]`) agar dimuat otomatis.
- Catat setiap perubahan pada riwayat di bawah.

### Riwayat

| Tanggal | Perubahan |
|---|---|
| 2026-10-22 | Dokumen dibuat. |
| 2026-10-22 | Tambah daftar fitur (bagian 2). Bagian 6 menjadi fitur Cuaca BMKG. Tambah bagian 11. |
| 2026-11-05 | Tinjauan laporan tim: perbaikan E1, E3, E4, E5, E6 diterima. Keputusan E2 (T1), E9 (T8), E13 (T9). Tambah T8-T12, aturan branch/PR, larangan skrip pemasang dan test ke database utama. Riwayat dicatat sebagai UI statis. |
| 2026-11-06 | Pemilik menetapkan tampilan `main` saat ini sebagai desain yang berlaku; Figma disesuaikan. T11 selesai; tambah T13 (rapikan `!important` dan breakpoint 380 px, opsional). |