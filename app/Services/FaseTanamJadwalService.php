<?php

namespace App\Services;

use App\Models\Lahan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

/**
 * Menyusun kalender dan aktivitas Fase Tanam.
 *
 * Untuk tahap awal, data aktivitas disimpan di session Laravel.
 * Data ini belum menjadi arsip permanen di database.
 */
class FaseTanamJadwalService
{
    private const SESSION_KEY = 'fase_tanam.jadwal_aktivitas';
    private const ZONA_WAKTU = 'Asia/Jakarta';

    /**
     * Menyiapkan data kalender, aktivitas pada tanggal terpilih,
     * filter, ringkasan, dan kegiatan berikutnya.
     */
    public function data(array $filter, Collection $daftarLahan): array
    {
        $sekarang = CarbonImmutable::now(self::ZONA_WAKTU);
        $hariIni = $sekarang->startOfDay();
        $bulan = $this->parseBulan($filter['bulan'] ?? null, $hariIni);
        $tanggalTerpilih = $this->parseTanggal(
            $filter['tanggal'] ?? null,
            $bulan,
            $hariIni
        );

        $opsiLahan = $daftarLahan
            ->map(fn (Lahan $lahan) => [
                'id' => (int) $lahan->id,
                'nama' => $lahan->nama_lahan,
            ])
            ->values()
            ->all();

        $filterLahan = isset($filter['lahan_id']) && $filter['lahan_id'] !== ''
            ? (int) $filter['lahan_id']
            : null;

        $statusFilter = $filter['status'] ?? 'semua';
        if (! in_array($statusFilter, ['semua', 'menunggu', 'selesai'], true)) {
            $statusFilter = 'semua';
        }

        $semuaAktivitas = $this->ambilAktivitasAwal($daftarLahan);

        $aktivitasTerfilter = collect($semuaAktivitas)
            ->filter(function (array $aktivitas) use ($filterLahan, $statusFilter) {
                $sesuaiLahan = $filterLahan === null
                    || (int) ($aktivitas['lahan_id'] ?? 0) === $filterLahan;

                $sesuaiStatus = $statusFilter === 'semua'
                    || $aktivitas['status'] === $statusFilter;

                return $sesuaiLahan && $sesuaiStatus;
            })
            ->sortBy(fn (array $aktivitas) => $aktivitas['tanggal'].' '.$aktivitas['waktu'])
            ->values();

        $aktivitasTanggal = $aktivitasTerfilter
            ->filter(fn (array $aktivitas) => $aktivitas['tanggal'] === $tanggalTerpilih->format('Y-m-d'))
            ->values()
            ->map(fn (array $aktivitas) => $this->formatAktivitas($aktivitas))
            ->all();

        $jumlahSelesai = count(array_filter(
            $aktivitasTanggal,
            fn (array $aktivitas) => $aktivitas['status'] === 'selesai'
        ));

        $jumlahMenunggu = count($aktivitasTanggal) - $jumlahSelesai;

        $aktivitasUntukKalender = [];
        foreach ($aktivitasTerfilter as $aktivitas) {
            $aktivitasUntukKalender[$aktivitas['tanggal']][] = $aktivitas;
        }

        $kalender = $this->buatKalender(
            $bulan,
            $tanggalTerpilih,
            $hariIni,
            $aktivitasUntukKalender,
            $filter
        );

        $aktivitasBerikutnya = collect($semuaAktivitas)
            ->filter(function (array $aktivitas) use ($filterLahan, $sekarang) {
                $sesuaiLahan = $filterLahan === null
                    || (int) ($aktivitas['lahan_id'] ?? 0) === $filterLahan;

                $tanggalWaktu = $aktivitas['tanggal'].' '.$aktivitas['waktu'];

                return $sesuaiLahan
                    && $aktivitas['status'] !== 'selesai'
                    && $tanggalWaktu >= $sekarang->format('Y-m-d H:i');
            })
            ->sortBy(fn (array $aktivitas) => $aktivitas['tanggal'].' '.$aktivitas['waktu'])
            ->take(3)
            ->map(fn (array $aktivitas) => $this->formatAktivitas($aktivitas))
            ->all();

        return [
            'contoh' => (bool) config('sawah_jadwal_aktivitas.contoh', true),
            'bulan' => $bulan->format('Y-m'),
            'bulan_label' => ucfirst($bulan->locale('id')->translatedFormat('F Y')),
            'bulan_sebelumnya' => $bulan->subMonth()->format('Y-m'),
            'bulan_berikutnya' => $bulan->addMonth()->format('Y-m'),
            'hari_ini' => $hariIni->format('Y-m-d'),
            'hari_ini_label' => $hariIni->locale('id')->translatedFormat('j M'),
            'tanggal_terpilih' => $tanggalTerpilih->format('Y-m-d'),
            'tanggal_terpilih_label' => ucfirst(
                $tanggalTerpilih->locale('id')->translatedFormat('l, j F Y')
            ),
            'kalender' => $kalender,
            'aktivitas_tanggal' => $aktivitasTanggal,
            'aktivitas_berikutnya' => $aktivitasBerikutnya,
            'opsi_lahan' => $opsiLahan,
            'filter' => [
                'lahan_id' => $filterLahan,
                'status' => $statusFilter,
            ],
            'ringkasan_tanggal' => [
                'total' => count($aktivitasTanggal),
                'selesai' => $jumlahSelesai,
                'menunggu' => $jumlahMenunggu,
            ],
        ];
    }

    /**
     * Menambahkan aktivitas baru ke session.
     */
    public function tambah(array $data): array
    {
        $lahan = Lahan::query()->findOrFail((int) $data['lahan_id']);

        $aktivitas = [
            'id' => (string) Str::uuid(),
            'kegiatan' => trim($data['kegiatan']),
            'lahan_id' => (int) $lahan->id,
            'lahan_nama' => $lahan->nama_lahan,
            'tanggal' => $data['tanggal'],
            'waktu' => $data['waktu'],
            'prioritas' => $data['prioritas'],
            'pengingat' => (string) ($data['pengingat'] ?? '0'),
            'catatan' => trim($data['catatan'] ?? ''),
            'status' => 'menunggu',
        ];

        $aktivitasSaatIni = $this->ambilSessionAktivitas();
        array_unshift($aktivitasSaatIni, $aktivitas);

        session()->put(self::SESSION_KEY, $aktivitasSaatIni);

        return $aktivitas;
    }

    /**
     * Mengubah status aktivitas menjadi selesai.
     */
    public function tandaiSelesai(string $id): bool
    {
        $aktivitas = $this->ambilSessionAktivitas();
        $ditemukan = false;

        foreach ($aktivitas as &$item) {
            if (($item['id'] ?? null) === $id) {
                $item['status'] = 'selesai';
                $ditemukan = true;
                break;
            }
        }

        unset($item);

        if ($ditemukan) {
            session()->put(self::SESSION_KEY, $aktivitas);
        }

        return $ditemukan;
    }

    /**
     * Mengambil data session. Data contoh hanya dibuat sekali per session.
     */
    private function ambilAktivitasAwal(Collection $daftarLahan): array
    {
        if (! session()->has(self::SESSION_KEY)) {
            $lahanPertama = $daftarLahan->first();
            $tanggalDasar = CarbonImmutable::now(self::ZONA_WAKTU)->startOfDay();
            $contoh = config('sawah_jadwal_aktivitas.aktivitas_awal', []);

            $aktivitasAwal = [];

            foreach ($contoh as $index => $item) {
                $tanggal = $tanggalDasar->addDays((int) ($item['offset_hari'] ?? 0));

                $aktivitasAwal[] = [
                    'id' => 'contoh-'.($index + 1),
                    'kegiatan' => $item['kegiatan'],
                    'lahan_id' => $lahanPertama ? (int) $lahanPertama->id : null,
                    'lahan_nama' => $lahanPertama?->nama_lahan ?? 'Lahan belum tersedia',
                    'tanggal' => $tanggal->format('Y-m-d'),
                    'waktu' => $item['waktu'],
                    'prioritas' => $item['prioritas'] ?? 'normal',
                    'pengingat' => (string) ($item['pengingat'] ?? '0'),
                    'catatan' => $item['catatan'] ?? '',
                    'status' => $item['status'] ?? 'menunggu',
                ];
            }

            session()->put(self::SESSION_KEY, $aktivitasAwal);
        }

        return $this->ambilSessionAktivitas();
    }

    private function ambilSessionAktivitas(): array
    {
        return array_values(session()->get(self::SESSION_KEY, []));
    }

    private function parseBulan(?string $nilai, CarbonImmutable $hariIni): CarbonImmutable
    {
        if (! $nilai || ! preg_match('/^\d{4}-\d{2}$/', $nilai)) {
            return $hariIni->startOfMonth();
        }

        try {
            $hasil = CarbonImmutable::createFromFormat('!Y-m', $nilai, self::ZONA_WAKTU);

            if ($hasil && $hasil->format('Y-m') === $nilai) {
                return $hasil->startOfMonth();
            }
        } catch (Throwable) {
            // Gunakan bulan berjalan jika nilai tidak membentuk bulan yang valid.
        }

        return $hariIni->startOfMonth();
    }

    private function parseTanggal(
        ?string $nilai,
        CarbonImmutable $bulan,
        CarbonImmutable $hariIni
    ): CarbonImmutable {
        if ($nilai) {
            try {
                $tanggal = CarbonImmutable::createFromFormat('!Y-m-d', $nilai, self::ZONA_WAKTU);

                if (
                    $tanggal
                    && $tanggal->format('Y-m-d') === $nilai
                    && $tanggal->format('Y-m') === $bulan->format('Y-m')
                ) {
                    return $tanggal;
                }
            } catch (Throwable) {
                // Gunakan tanggal awal bulan jika tanggal tidak valid.
            }
        }

        return $bulan->format('Y-m') === $hariIni->format('Y-m')
            ? $hariIni
            : $bulan->startOfMonth();
    }

    private function buatKalender(
        CarbonImmutable $bulan,
        CarbonImmutable $tanggalTerpilih,
        CarbonImmutable $hariIni,
        array $aktivitasPerTanggal,
        array $filter
    ): array {
        $mulai = $bulan->startOfMonth()->startOfWeek(CarbonImmutable::MONDAY);
        $akhir = $bulan->endOfMonth()->endOfWeek(CarbonImmutable::SUNDAY);
        $cursor = $mulai;
        $kalender = [];

        while ($cursor->lessThanOrEqualTo($akhir)) {
            $tanggal = $cursor->format('Y-m-d');
            $daftar = $aktivitasPerTanggal[$tanggal] ?? [];
            $tautan = [
                'tab' => 'jadwal',
                'bulan' => $bulan->format('Y-m'),
                'tanggal' => $tanggal,
            ];

            if (! empty($filter['lahan_id'])) {
                $tautan['lahan_id'] = $filter['lahan_id'];
            }

            if (! empty($filter['status']) && $filter['status'] !== 'semua') {
                $tautan['status'] = $filter['status'];
            }

            $kalender[] = [
                'tanggal' => $tanggal,
                'nomor' => $cursor->format('j'),
                'dalam_bulan' => $cursor->format('Y-m') === $bulan->format('Y-m'),
                'hari_ini' => $tanggal === $hariIni->format('Y-m-d'),
                'terpilih' => $tanggal === $tanggalTerpilih->format('Y-m-d'),
                'jumlah' => count($daftar),
                'kegiatan_pertama' => $daftar[0]['kegiatan'] ?? null,
                'url_parameter' => $tautan,
            ];

            $cursor = $cursor->addDay();
        }

        return $kalender;
    }

    private function formatAktivitas(array $aktivitas): array
    {
        $tanggal = CarbonImmutable::parse(
            $aktivitas['tanggal'],
            self::ZONA_WAKTU
        )->locale('id');

        $prioritas = $aktivitas['prioritas'] ?? 'normal';
        $status = $aktivitas['status'] ?? 'menunggu';
        $pengingat = (string) ($aktivitas['pengingat'] ?? '0');

        return [
            ...$aktivitas,
            'tanggal_label' => $tanggal->translatedFormat('j M'),
            'waktu_label' => str_replace(':', '.', $aktivitas['waktu']),
            'prioritas_label' => ucfirst($prioritas),
            'prioritas_warna' => $prioritas === 'tinggi' ? 'tinggi' : 'normal',
            'status_label' => $status === 'selesai' ? 'Selesai' : 'Menunggu',
            'pengingat_label' => match ($pengingat) {
                '30' => '30 menit sebelum',
                '60' => '1 jam sebelum',
                default => 'Tidak ada',
            },
        ];
    }
}
