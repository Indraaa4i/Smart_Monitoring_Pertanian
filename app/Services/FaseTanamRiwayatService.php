<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/** Menyiapkan statistik dan koordinat grafik dari data contoh Riwayat Tanam. */
class FaseTanamRiwayatService
{
    private const ZONA_WAKTU = 'Asia/Jakarta';

    /**
     * @param array{q?: string, tahun?: string|int, blok?: string} $filters
     */
    public function data(array $filters = []): array
    {
        $konfigurasi = config('sawah_riwayat_tanam', []);
        $semuaSiklus = collect($konfigurasi['siklus'] ?? []);

        $kataKunci = trim((string) ($filters['q'] ?? ''));
        $tahunDiminta = trim((string) ($filters['tahun'] ?? ''));
        $blokDiminta = trim((string) ($filters['blok'] ?? ''));

        $daftarTahun = $semuaSiklus
            ->pluck('tahun')
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        // Secara default tampilkan tahun terbaru, sesuai desain contoh (Tahun 2026).
        $tahunTerpilih = $tahunDiminta !== ''
            ? $tahunDiminta
            : (string) ($daftarTahun->first() ?? '');

        $siklus = $semuaSiklus
            ->filter(function (array $item) use ($kataKunci, $tahunTerpilih, $blokDiminta): bool {
                $cocokKataKunci = $kataKunci === '' || str_contains(
                    mb_strtolower(implode(' ', [
                        $item['kode'] ?? '',
                        $item['musim'] ?? '',
                        $item['lahan'] ?? '',
                        $item['varietas'] ?? '',
                    ])),
                    mb_strtolower($kataKunci)
                );

                $cocokTahun = $tahunTerpilih === 'semua'
                    || $tahunTerpilih === ''
                    || (string) ($item['tahun'] ?? '') === $tahunTerpilih;

                $cocokLahan = $blokDiminta === ''
                    || $blokDiminta === 'semua'
                    || ($item['lahan'] ?? '') === $blokDiminta;

                return $cocokKataKunci && $cocokTahun && $cocokLahan;
            })
            ->values();

        $totalSiklus = $siklus->count();
        $totalPanen = (float) $siklus->sum(
            fn (array $item): float => (float) ($item['hasil_panen_ton'] ?? 0)
        );
        $totalLuas = (float) $siklus->sum(
            fn (array $item): float => (float) ($item['luas_ha'] ?? 0)
        );

        // Produktivitas rata-rata dihitung tertimbang: total panen / total luas.
        $produktivitasRataRata = $totalLuas > 0
            ? $totalPanen / $totalLuas
            : 0.0;

        $arsip = $siklus->map(fn (array $item): array => $this->formatSiklus($item))
            ->all();

        $evaluasiTerakhir = $siklus->first(
            fn (array $item): bool => ! empty($item['evaluasi'])
        );

        return [
            'contoh' => (bool) ($konfigurasi['contoh'] ?? true),
            'sumberHasil' => (string) ($konfigurasi['sumber_hasil'] ?? 'Hasil panen'),
            'filter' => [
                'q' => $kataKunci,
                'tahun' => $tahunTerpilih,
                'blok' => $blokDiminta,
            ],
            'opsi' => [
                'tahun' => $daftarTahun->all(),
                'lahan' => $semuaSiklus->pluck('lahan')->filter()->unique()->values()->all(),
            ],
            'statistik' => [
                'jumlahSiklus' => $totalSiklus,
                'jumlahSiklusFormat' => $totalSiklus.' siklus',
                'keteranganSiklus' => $siklus->pluck('musim')->filter()->unique()->implode(', ') ?: 'Belum ada arsip pada filter ini',
                'totalPanen' => $totalPanen,
                'totalPanenFormat' => $this->formatAngka($totalPanen).' ton',
                'totalLuas' => $totalLuas,
                'totalLuasFormat' => $this->formatAngka($totalLuas).' ha',
                'produktivitas' => $produktivitasRataRata,
                'produktivitasFormat' => $this->formatAngka($produktivitasRataRata).' t/ha',
            ],
            'grafik' => $this->buatGrafik($siklus),
            'arsip' => $arsip,
            'evaluasi' => $evaluasiTerakhir
                ? $this->formatEvaluasi($evaluasiTerakhir)
                : null,
        ];
    }

    private function formatSiklus(array $item): array
    {
        return [
            'kode' => (string) $item['kode'],
            'lahan' => (string) $item['lahan'],
            'varietas' => (string) $item['varietas'],
            'luas' => $this->formatAngka((float) $item['luas_ha']).' ha',
            'periode' => $this->tanggalSingkat($item['tanggal_mulai'])
                .'–'.$this->tanggalSingkat($item['tanggal_panen']),
            'hasil' => $this->formatAngka((float) $item['hasil_panen_ton']).' ton',
            'produktivitas' => $this->formatAngka((float) $item['produktivitas_ton_ha']).' t/ha',
            'status' => (string) ($item['status'] ?? 'Selesai'),
            'evaluasi' => $item['evaluasi'] ?? null,
        ];
    }

    private function formatEvaluasi(array $item): array
    {
        $evaluasi = $item['evaluasi'];

        return [
            'kode' => (string) $item['kode'],
            'lahan' => (string) $item['lahan'],
            'tanggalPanen' => CarbonImmutable::parse(
                $item['tanggal_panen'],
                self::ZONA_WAKTU
            )->locale('id')->translatedFormat('j M Y'),
            'penanggungJawab' => (string) ($evaluasi['penanggung_jawab'] ?? 'Belum dicatat'),
            'kendala' => (string) ($evaluasi['kendala'] ?? 'Belum ada catatan kendala.'),
            'saran' => (string) ($evaluasi['saran'] ?? 'Belum ada evaluasi petani.'),
        ];
    }

    /** Menghitung posisi batang dan garis kisi agar Blade hanya bertugas menampilkan. */
    private function buatGrafik(Collection $siklus): array
    {
        $lebar = 820;
        $tinggi = 290;
        $kiri = 66;
        $kanan = 24;
        $atas = 24;
        $dasar = 226;
        $tinggiPlot = $dasar - $atas;
        $lebarPlot = $lebar - $kiri - $kanan;

        $nilaiTertinggi = (float) $siklus->max(
            fn (array $item): float => (float) ($item['hasil_panen_ton'] ?? 0)
        );
        $maksimum = max(2, (int) (ceil($nilaiTertinggi / 2) * 2));

        $ticks = [];
        for ($nilai = 0; $nilai <= $maksimum; $nilai += 2) {
            $ticks[] = [
                'label' => (string) $nilai,
                'y' => round($dasar - (($nilai / $maksimum) * $tinggiPlot), 2),
            ];
        }

        $jumlah = max(1, $siklus->count());
        $lebarSlot = $lebarPlot / $jumlah;
        $lebarBatang = min(112, $lebarSlot * 0.52);
        $batang = [];

        foreach ($siklus->values() as $index => $item) {
            $nilai = (float) ($item['hasil_panen_ton'] ?? 0);
            $tinggiBatang = ($nilai / $maksimum) * $tinggiPlot;
            $x = $kiri + ($index * $lebarSlot) + (($lebarSlot - $lebarBatang) / 2);

            $batang[] = [
                'x' => round($x, 2),
                'y' => round($dasar - $tinggiBatang, 2),
                'width' => round($lebarBatang, 2),
                'height' => round($tinggiBatang, 2),
                'tengah' => round($x + ($lebarBatang / 2), 2),
                'label' => (string) $item['lahan'],
                'nilai' => $this->formatAngka($nilai),
            ];
        }

        return [
            'width' => $lebar,
            'height' => $tinggi,
            'plotKiri' => $kiri,
            'plotKanan' => $lebar - $kanan,
            'dasar' => $dasar,
            'ticks' => $ticks,
            'batang' => $batang,
            'kosong' => $siklus->isEmpty(),
        ];
    }

    private function tanggalSingkat(string $tanggal): string
    {
        return CarbonImmutable::parse($tanggal, self::ZONA_WAKTU)
            ->locale('id')
            ->translatedFormat('j M');
    }

    private function formatAngka(float $nilai): string
    {
        $format = number_format($nilai, 2, ',', '.');

        return rtrim(rtrim($format, '0'), ',');
    }
}
