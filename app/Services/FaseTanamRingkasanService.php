<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Route;

/**
 * Data contoh untuk bagian lain halaman Fase Tanam.
 * Data masih statis mengikuti desain Figma.
 */
class FaseTanamRingkasanService
{
    private const TANGGAL_TANAM = '2026-11-04';

    public function dataContoh(string $tabAktif = 'ringkasan'): array
    {
        return [
            'tab' => $this->tab($tabAktif),
            'kartu' => $this->kartu(),
            'tahapan' => $this->tahapan(),
            'aktivitas' => $this->aktivitas(),
        ];
    }

    private function tab(string $tabAktif = 'ringkasan'): array
    {
        return [
            [
                'label' => 'Ringkasan',
                'aktif' => $tabAktif === 'ringkasan',
                'url' => route('fase-tanam'),
            ],
            [
                'label' => 'Siklus tanam',
                'aktif' => $tabAktif === 'siklus',
                'url' => route('fase-tanam', ['tab' => 'siklus']),
            ],
            [
                'label' => 'Persiapan lahan',
                'aktif' => $tabAktif === 'persiapan',
                'url' => route('fase-tanam', ['tab' => 'persiapan']),
            ],
            [
                'label' => 'Persemaian bibit',
                'url' => route('fase-tanam.persemaian'),
            ],
            [
                'label' => 'Penanaman',
                'url' => route('fase-tanam.penanaman'),
            ],
            [
                'label' => 'Pemeliharaan',
                'url' => Route::has('fase-tanam.pemeliharaan')
                    ? route('fase-tanam.pemeliharaan')
                    : null,
            ],
            [
                'label' => 'Jadwal & aktivitas',
                'aktif' => $tabAktif === 'jadwal',
                'url' => route('fase-tanam', ['tab' => 'jadwal']),
            ],
            [
                'label' => 'Riwayat tanam',
                'aktif' => $tabAktif === 'riwayat',
                'url' => route('fase-tanam', ['tab' => 'riwayat']),
            ],
        ];
    }

    private function kartu(): array
    {
        return [
            ['judul' => 'Siklus aktif', 'nilai' => '2 siklus', 'ket' => 'Blok A dan Blok B'],
            ['judul' => 'Luas dikelola', 'nilai' => '2,5 ha', 'ket' => '3 blok termasuk 1 rencana'],
            ['judul' => 'Kegiatan hari ini', 'nilai' => '3 kegiatan', 'ket' => '1 selesai · 2 menunggu'],
            ['judul' => 'Panen terdekat', 'nilai' => '22 Feb 2027', 'ket' => 'Perkiraan · Blok A'],
        ];
    }

    /**
     * Daftar tahapan + progres yang dihitung otomatis dari status tiap tahap.
     * Satu-satunya sumber kebenaran: ubah 'status' di sini, progres ikut berubah.
     */
    public function tahapan(): array
    {
        $daftar = [
            [
                'nama' => 'Persiapan lahan',
                'meta' => '20–28 Okt · Selesai',
                'status' => 'selesai',
                'url' => route('fase-tanam', ['tab' => 'persiapan']),
            ],
            [
                'nama' => 'Persemaian bibit',
                'meta' => '15 Okt–3 Nov · Selesai',
                'status' => 'selesai',
                'url' => route('fase-tanam.persemaian'),
            ],
            [
                'nama' => 'Penanaman',
                'meta' => '4 Nov · Selesai',
                'status' => 'selesai',
                'url' => route('fase-tanam.penanaman'),
            ],
            [
                'nama' => 'Pemeliharaan',
                'meta' => $this->hst().' HST · Berlangsung',
                'status' => 'berlangsung',
                'url' => route('fase-tanam.pemeliharaan'),
            ],
            [
                'nama' => 'Panen & evaluasi',
                'meta' => '22 Feb 2027 · Rencana',
                'status' => 'rencana',
                'url' => route('fase-tanam.panen'),
            ],
        ];

        $total = count($daftar);
        $selesai = count(array_filter($daftar, fn ($t) => $t['status'] === 'selesai'));
        $persen = $total > 0 ? (int) round($selesai / $total * 100) : 0;

        return [
            'keterangan' => 'Siklus MH-2026-A · Tahap panen diselesaikan saat siklus ditutup.',
            'daftar' => $daftar,
            'progres' => [
                'selesai' => $selesai,
                'total' => $total,
                'persen' => $persen,
                'teks' => "{$selesai} dari {$total} tahap selesai · {$persen}% tahapan",
            ],
        ];
    }

    private function aktivitas(): array
    {
        return [
            'keterangan' => 'Pekerjaan terjadwal untuk Blok A dan Blok B.',
            'daftar' => [
                [
                    'kegiatan' => 'Periksa tinggi air',
                    'lahan' => 'Blok A · Hari ini, 07.00',
                    'prioritas' => ['teks' => 'Normal', 'warna' => 'hijau'],
                    'status' => ['teks' => 'Selesai', 'warna' => 'hijau'],
                    'aksi' => 'Lihat jadwal',
                ],
                [
                    'kegiatan' => 'Pemeriksaan hama',
                    'lahan' => 'Blok A · Hari ini, 15.00',
                    'prioritas' => ['teks' => 'Tinggi', 'warna' => 'merah'],
                    'status' => ['teks' => 'Menunggu', 'warna' => 'kuning'],
                    'aksi' => 'Lihat jadwal',
                ],
                [
                    'kegiatan' => 'Periksa bibit',
                    'lahan' => 'Blok B · Hari ini, 16.00',
                    'prioritas' => ['teks' => 'Normal', 'warna' => 'hijau'],
                    'status' => ['teks' => 'Menunggu', 'warna' => 'kuning'],
                    'aksi' => 'Lihat bibit',
                ],
            ],
        ];
    }

    public function persemaian(): array
    {
        return [
            'siklus' => 'MH-2026-B',
            'blok' => 'Blok B',
            'varietas' => 'Ciherang',
            'tanggal_semai' => '2026-11-01',
            'tanggal_periksa' => '2026-11-10',
            'jumlah_benih' => 20,
            'daya_tumbuh' => 92,
            'kondisi' => 'Baik',
            'target_pindah' => '2026-11-21',
            'catatan' => 'Pertumbuhan bibit baik dan merata.',
            'riwayat' => [
                [
                    'tanggal' => '10 Nov 2026',
                    'umur' => 9,
                    'kondisi' => 'Baik',
                    'tinggi' => '12-14 cm',
                    'daya_tumbuh' => '92%',
                ],
                [
                    'tanggal' => '5 Nov 2026',
                    'umur' => 4,
                    'kondisi' => 'Mulai berkecambah',
                    'tinggi' => '3-5 cm',
                    'daya_tumbuh' => '88%',
                ],
            ],
        ];
    }

    private function hst(): int
    {
        $zonaWaktu = 'Asia/Jakarta';
        $tanggalTanam = CarbonImmutable::parse(self::TANGGAL_TANAM, $zonaWaktu)->startOfDay();
        $hariIni = CarbonImmutable::now($zonaWaktu)->startOfDay();

        return max(0, (int) $tanggalTanam->diffInDays($hariIni));
    }
}
