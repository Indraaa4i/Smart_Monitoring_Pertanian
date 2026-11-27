<?php

namespace App\Services;

use App\Models\Lahan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Menyiapkan data halaman Persiapan Lahan.
 *
 * Catatan pemeriksaan disimpan sementara di session karena tabel
 * persiapan_lahan belum tersedia. Data IoT dibaca secara defensif:
 * bila tabel/kolom tidak tersedia, halaman menampilkan status offline.
 */
class FaseTanamPersiapanService
{
    private const SESSION_KEY = 'persiapan_lahan_riwayat';

    public function data(?Lahan $lahan, $daftarLahan): array
    {
        $sensor = $this->sensorTerkini($lahan);

        $riwayatSession = collect(session(self::SESSION_KEY, []))
            ->filter(fn (array $item) => (int) ($item['lahan_id'] ?? 0) === (int) ($lahan?->id ?? 0))
            ->map(fn (array $item) => $this->formatRiwayat($item))
            ->values();

        $riwayatContoh = collect(config('sawah_persiapan_lahan.riwayat_contoh', []))
            ->filter(function (array $item) use ($lahan) {
                if (!$lahan) {
                    return false;
                }

                $namaContoh = mb_strtolower((string) ($item['lahan'] ?? ''));
                $namaLahan = mb_strtolower((string) $lahan->nama_lahan);

                return $namaContoh === ''
                    || str_contains($namaLahan, $namaContoh)
                    || str_contains($namaContoh, $namaLahan);
            })
            ->map(fn (array $item) => $this->formatRiwayat($item))
            ->values();

        $riwayat = $riwayatSession
            ->concat($riwayatContoh)
            ->sortByDesc('tanggal_iso')
            ->values()
            ->all();

        return [
            'contoh' => true,
            'sensor' => $sensor,
            'daftarLahan' => $daftarLahan,
            'lahan' => $lahan,
            'opsi' => [
                'kondisi' => config('sawah_persiapan_lahan.kondisi', []),
                'metode' => config('sawah_persiapan_lahan.metode', []),
            ],
            'riwayat' => $riwayat,
            'jumlah_catatan' => count($riwayat),
            'status_persiapan' => count($riwayat) > 0
                ? 'Sudah ada catatan'
                : 'Belum ada catatan',
            'tanggal_default' => now('Asia/Jakarta')->toDateString(),
        ];
    }

    /**
     * Menyimpan hasil pemeriksaan manual ke session untuk lahan terpilih.
     * Foto disimpan pada public storage; session hanya menyimpan path-nya.
     */
    public function simpan(Lahan $lahan, array $data, array $fotoPaths = []): void
    {
        $semuaRiwayat = session(self::SESSION_KEY, []);

        $catatan = [
            'id' => (string) Str::uuid(),
            'lahan_id' => (int) $lahan->id,
            'lahan' => $lahan->nama_lahan,
            'tanggal' => $data['tanggal_pemeriksaan'],
            'kegiatan' => 'Pemeriksaan persiapan lahan',
            'sumber' => 'Input manual',
            'kondisi_tanah' => $data['kondisi_tanah'],
            'metode_pengolahan' => $data['metode_pengolahan'],
            'ph' => (float) $data['ph'],
            'kelembapan' => (float) $data['kelembapan'],
            'ketinggian_air' => (float) $data['ketinggian_air'],
            'hasil' => $this->ringkasanHasil($data),
            'petugas' => $data['penanggung_jawab'] ?: 'Belum diisi',
            'catatan' => $data['catatan'] ?? '',
            'foto' => $fotoPaths,
            'dibuat_pada' => now('Asia/Jakarta')->toDateTimeString(),
        ];

        array_unshift($semuaRiwayat, $catatan);
        session()->put(self::SESSION_KEY, $semuaRiwayat);
    }

    /**
     * Membaca sensor hanya jika tabel dan kolomnya benar-benar tersedia.
     * Skema dasar data_sensor yang diketahui memuat ph, kelembapan, dan waktu.
     * Ketinggian air hanya dibaca bila kolomnya memang ada.
     */
    private function sensorTerkini(?Lahan $lahan): array
    {
        $kosong = [
            'tersedia' => false,
            'waktu' => null,
            'label_waktu' => 'Belum ada pembaruan sensor',
            'pesan' => 'Data sensor belum tersedia. Gunakan input manual untuk mencatat pemeriksaan.',
            'parameter' => [
                'ph' => $this->parameterKosong('pH tanah', ''),
                'kelembapan' => $this->parameterKosong('Kelembapan', '%'),
                'ketinggian_air' => $this->parameterKosong('Ketinggian air', 'cm'),
            ],
        ];

        if (!$lahan || !Schema::hasTable('data_sensor')) {
            $kosong['pesan'] = 'Tabel data_sensor belum aktif. Data IoT belum dapat ditampilkan.';
            return $kosong;
        }

        if (!Schema::hasColumn('data_sensor', 'lahan_id')
            || !Schema::hasColumn('data_sensor', 'waktu')) {
            $kosong['pesan'] = 'Struktur tabel sensor belum lengkap untuk membaca data terbaru.';
            return $kosong;
        }

        $kolom = ['id', 'lahan_id', 'waktu'];
        foreach (['ph', 'kelembapan', 'ketinggian_air'] as $namaKolom) {
            if (Schema::hasColumn('data_sensor', $namaKolom)) {
                $kolom[] = $namaKolom;
            }
        }

        if (!in_array('ph', $kolom, true) && !in_array('kelembapan', $kolom, true)) {
            $kosong['pesan'] = 'Kolom pH dan kelembapan belum tersedia pada tabel data_sensor.';
            return $kosong;
        }

        $record = DB::table('data_sensor')
            ->where('lahan_id', $lahan->id)
            ->latest('waktu')
            ->first($kolom);

        if (!$record) {
            $kosong['pesan'] = 'Belum ada data sensor untuk lahan ini.';
            return $kosong;
        }

        $waktu = Carbon::parse($record->waktu, 'Asia/Jakarta');
        $batasOffline = max(1, (int) config('sawah.offline_after_minutes', 30));

        if ($waktu->lt(now('Asia/Jakarta')->subMinutes($batasOffline))) {
            $kosong['pesan'] = 'Data sensor terakhir sudah melewati batas pembaruan. Periksa koneksi sensor.';
            $kosong['label_waktu'] = 'Terakhir: '.$waktu->locale('id')->translatedFormat('j M Y · H.i').' WIB';
            return $kosong;
        }

        $nilai = [
            'ph' => property_exists($record, 'ph') ? $record->ph : null,
            'kelembapan' => property_exists($record, 'kelembapan') ? $record->kelembapan : null,
            'ketinggian_air' => property_exists($record, 'ketinggian_air') ? $record->ketinggian_air : null,
        ];

        $tersedia = collect($nilai)->contains(fn ($value) => $value !== null);

        if (!$tersedia) {
            $kosong['pesan'] = 'Data sensor terbaru belum memiliki nilai pengukuran.';
            return $kosong;
        }

        return [
            'tersedia' => true,
            'waktu' => $waktu->toDateTimeString(),
            'label_waktu' => 'Diperbarui '.$waktu->locale('id')->translatedFormat('j M Y · H.i').' WIB',
            'pesan' => 'Nilai berikut berasal dari pembacaan sensor terbaru.',
            'parameter' => [
                'ph' => $this->parameterSensor('pH tanah', $nilai['ph'], ''),
                'kelembapan' => $this->parameterSensor('Kelembapan', $nilai['kelembapan'], '%'),
                'ketinggian_air' => $this->parameterSensor('Ketinggian air', $nilai['ketinggian_air'], 'cm'),
            ],
        ];
    }

    private function parameterKosong(string $judul, string $satuan): array
    {
        return [
            'judul' => $judul,
            'nilai' => '—',
            'satuan' => $satuan,
            'status' => 'Offline',
            'tersedia' => false,
        ];
    }

    private function parameterSensor(string $judul, mixed $nilai, string $satuan): array
    {
        if ($nilai === null) {
            return [
                'judul' => $judul,
                'nilai' => '—',
                'satuan' => $satuan,
                'status' => 'Belum tersedia',
                'tersedia' => false,
            ];
        }

        $desimal = $judul === 'pH tanah' ? 1 : ($judul === 'Kelembapan' ? 1 : 0);

        return [
            'judul' => $judul,
            'nilai' => number_format((float) $nilai, $desimal, ',', ''),
            'satuan' => $satuan,
            'status' => 'Online',
            'tersedia' => true,
        ];
    }

    private function formatRiwayat(array $item): array
    {
        $tanggal = (string) ($item['tanggal'] ?? now('Asia/Jakarta')->toDateString());

        try {
            $tanggalLabel = Carbon::parse($tanggal, 'Asia/Jakarta')
                ->locale('id')
                ->translatedFormat('j M Y');
        } catch (\Throwable) {
            $tanggalLabel = $tanggal;
        }

        $foto = collect($item['foto'] ?? [])
            ->map(fn (string $path) => Storage::disk('public')->url($path))
            ->all();

        return [
            ...$item,
            'tanggal_iso' => $tanggal,
            'tanggal_label' => $tanggalLabel,
            'kegiatan' => $item['kegiatan'] ?? 'Pemeriksaan persiapan lahan',
            'sumber' => $item['sumber'] ?? 'Data contoh',
            'hasil' => $item['hasil'] ?? 'Catatan pemeriksaan tersimpan',
            'petugas' => $item['petugas'] ?? '—',
            'foto_url' => $foto,
        ];
    }

    private function ringkasanHasil(array $data): string
    {
        return 'pH '.number_format((float) $data['ph'], 1, ',', '')
            .' · Kelembapan '.number_format((float) $data['kelembapan'], 1, ',', '').'%'
            .' · Air '.number_format((float) $data['ketinggian_air'], 1, ',', '').' cm';
    }
}
