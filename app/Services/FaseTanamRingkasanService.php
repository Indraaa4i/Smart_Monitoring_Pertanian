<?php

namespace App\Services;

use Carbon\CarbonImmutable;

/**
 * Data contoh untuk bagian lain halaman Ringkasan Fase Tanam
 * (tab, kartu ringkasan, progres tahapan, aktivitas terdekat).
 *
 * Masih statis mengikuti desain Figma 10 Nov 2026. Diganti data nyata
 * setelah tabel siklus dan aktivitas tersedia.
 */
class FaseTanamRingkasanService
{
    /** Tanggal tanam siklus MH-2026-A (sama dengan card siklus aktif). */
    private const TANGGAL_TANAM = '2026-11-04';

    /**
     * @return array{tab: array, kartu: array, tahapan: array, aktivitas: array}
     */
    public function dataContoh(string $tabAktif = 'ringkasan'): array
    {
        return [
            'tab' => $this->tab($tabAktif),
            'kartu' => $this->kartu(),
            'tahapan' => $this->tahapan(),
            'aktivitas' => $this->aktivitas(),
        ];
    }

    /**
     * Tab bagian Fase Tanam.
     * Tab Ringkasan dan Siklus mengikuti tab aktif.
     */
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
            ['label' => 'Persiapan lahan'],
            [
                'label' => 'Persemaian bibit',
                'url'   => route('fase-tanam.persemaian'),
            ],
            [
                'label' => 'Penanaman',
                'url' => route('fase-tanam.penanaman'),
            ],
            ['label' => 'Pemeliharaan'],
            ['label' => 'Jadwal & aktivitas'],
            ['label' => 'Riwayat tanam'],
        ];
    }

    private function kartu(): array
    {
        return [
            [
                'judul' => 'Siklus aktif',
                'nilai' => '2 siklus',
                'ket' => 'Blok A dan Blok B',
            ],
            [
                'judul' => 'Luas dikelola',
                'nilai' => '2,5 ha',
                'ket' => '3 blok termasuk 1 rencana',
            ],
            [
                'judul' => 'Kegiatan hari ini',
                'nilai' => '3 kegiatan',
                'ket' => '1 selesai · 2 menunggu',
            ],
            [
                'judul' => 'Panen terdekat',
                'nilai' => '22 Feb 2027',
                'ket' => 'Perkiraan · Blok A',
            ],
        ];
    }

    private function tahapan(): array
    {
        // Isi 'url' pada tahap yang halaman detailnya sudah tersedia.
        return [
            'keterangan' => 'Siklus MH-2026-A · Tahap panen diselesaikan saat siklus ditutup.',
            'daftar' => [
                [
                    'nama' => 'Persiapan lahan',
                    'meta' => '20–28 Okt · Selesai',
                    'status' => 'selesai',
                ],
                [
                    'nama' => 'Persemaian bibit',
                    'meta' => '15 Okt–3 Nov · Selesai',
                    'status' => 'selesai',
                ],
                [
                    'nama' => 'Penanaman',
                    'meta' => '4 Nov · Selesai',
                    'status' => 'selesai',
                ],
                [
                    'nama' => 'Pemeliharaan',
                    'meta' => $this->hst().' HST · Berlangsung',
                    'status' => 'berlangsung',
                ],
                [
                    'nama' => 'Panen & evaluasi',
                    'meta' => '22 Feb 2027 · Rencana',
                    'status' => 'rencana',
                ],
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
                    'prioritas' => [
                        'teks' => 'Normal',
                        'warna' => 'hijau',
                    ],
                    'status' => [
                        'teks' => 'Selesai',
                        'warna' => 'hijau',
                    ],
                    'aksi' => 'Lihat jadwal',
                ],
                [
                    'kegiatan' => 'Pemeriksaan hama',
                    'lahan' => 'Blok A · Hari ini, 15.00',
                    'prioritas' => [
                        'teks' => 'Tinggi',
                        'warna' => 'merah',
                    ],
                    'status' => [
                        'teks' => 'Menunggu',
                        'warna' => 'kuning',
                    ],
                    'aksi' => 'Lihat jadwal',
                ],
                [
                    'kegiatan' => 'Periksa bibit',
                    'lahan' => 'Blok B · Hari ini, 16.00',
                    'prioritas' => [
                        'teks' => 'Normal',
                        'warna' => 'hijau',
                    ],
                    'status' => [
                        'teks' => 'Menunggu',
                        'warna' => 'kuning',
                    ],
                    'aksi' => 'Lihat bibit',
                ],
            ],
        ];
    }

    /**
     * Data contoh untuk halaman Persemaian Bibit.
     */
    public function persemaian(): array
    {
        return [
            'siklus'          => 'MH-2026-B',
            'blok'            => 'Blok B',
            'varietas'        => 'Ciherang',
            'tanggal_semai'   => '2026-11-01',
            'tanggal_periksa' => '2026-11-10',
            'jumlah_benih'    => 20,
            'daya_tumbuh'    => 92,
            'kondisi'         => 'Baik',
            'target_pindah'   => '2026-11-21',
            'catatan'          => 'Pertumbuhan bibit baik dan merata.',
            'riwayat' => [
                [
                    'tanggal'      => '10 Nov 2026',
                    'umur'          => 9,
                    'kondisi'      => 'Baik',
                    'tinggi'        => '12-14 cm',
                    'daya_tumbuh' => '92%',
                ],
                [
                    'tanggal'      => '5 Nov 2026',
                    'umur'          => 4,
                    'kondisi'      => 'Mulai berkecambah',
                    'tinggi'        => '3-5 cm',
                    'daya_tumbuh' => '88%',
                ],
            ],
        ];
    }

    /** Hari setelah tanam (HST) dihitung dari tanggal tanam sampai hari ini (WIB). */
    private function hst(): int
    {
        $tz = 'Asia/Jakarta';

        $tanam = CarbonImmutable::parse(
            self::TANGGAL_TANAM,
            $tz
        )->startOfDay();

        $hariIni = CarbonImmutable::now($tz)->startOfDay();

        return max(0, (int) $tanam->diffInDays($hariIni));
    }
}
