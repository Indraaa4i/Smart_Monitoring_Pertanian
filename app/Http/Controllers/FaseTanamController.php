<?php

namespace App\Http\Controllers;

use App\Models\Lahan;
use App\Services\DashboardService;
use App\Services\FaseTanamRingkasanService;
use App\Services\FaseTanamService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FaseTanamController extends Controller
{
    // ==================================================
    // RINGKASAN & SIKLUS TANAM
    // ==================================================
    public function __invoke(
        Request $request,
        FaseTanamService $service,
        DashboardService $dashboard,
        FaseTanamRingkasanService $ringkasan,
    ) {
        $daftarLahan = Lahan::orderBy('nama_lahan')->get();

        $lahan = $daftarLahan->firstWhere(
            'id',
            (int) $request->query('lahan')
        ) ?? $daftarLahan->first();

        $tabAktif = $request->query('tab', 'ringkasan');

        $dataContoh = $ringkasan->dataContoh($tabAktif);

        // ==================================================
        // DATA SIKLUS TANAM
        // ==================================================
        $dataSiklus = [
            'summary' => [
                'total'   => 2,
                'aktif'   => 1,
                'rencana' => 1,
            ],

            'siklus' => [
                [
                    'nama'      => 'Siklus Tanam 1',
                    'lahan'      => 'Blok A',
                    'musim'      => 'MH-2026-A',
                    'varietas'   => 'Inpari 32',
                    'luas_ha'    => 1.2,
                    'mulai'      => '2026-11-04',
                    'akhir'      => '2027-02-22',
                    'tahap'      => 'Pemeliharaan',
                    'status'     => 'Aktif',
                    'catatan'    => 'Siklus tanam aktif pada Blok A.',
                ],

                [
                    'nama'      => 'Siklus Tanam 2',
                    'lahan'      => 'Blok B',
                    'musim'      => 'MH-2026-B',
                    'varietas'   => 'Ciherang',
                    'luas_ha'    => 1.3,
                    'mulai'      => '2026-11-21',
                    'akhir'      => '2027-03-10',
                    'tahap'      => 'Persemaian',
                    'status'     => 'Rencana',
                    'catatan'    => 'Bibit sedang disiapkan untuk rencana penanaman Blok B.',
                ],
            ],
        ];

        return view('pages.fase-tanam', [
            'title'        => 'Fase Tanam',
            'tabAktif'     => $tabAktif,

            'daftarLahan'  => $daftarLahan,
            'lahan'         => $lahan,

            'phase'         => $lahan
                ? $dashboard->phase($lahan)
                : null,

            'siklus'        => $service->siklusAktif(),
            'dataSiklus'    => $dataSiklus,

            'contoh'        => config('sawah_fase_tanam.contoh'),

            'waktu' => now('Asia/Jakarta')
                ->locale('id')
                ->translatedFormat('j M Y · H.i').' WIB',

            ...$dataContoh,
        ]);
    }


    // ==================================================
    // PERSEMAIAN BIBIT
    // ==================================================
    public function persemaian(
        Request $request,
        DashboardService $dashboard,
        FaseTanamRingkasanService $ringkasan,
    ) {
        $daftarLahan = Lahan::orderBy('nama_lahan')->get();

        $lahan = $daftarLahan->firstWhere(
            'id',
            (int) $request->query('lahan')
        ) ?? $daftarLahan->first();

        $persemaian = $ringkasan->persemaian();

        $data = $persemaian;

        // Hitung umur bibit
        $umurBibit = Carbon::parse(
            $persemaian['tanggal_semai'],
            'Asia/Jakarta'
        )->diffInDays(now('Asia/Jakarta'));

        $contoh = config('sawah_fase_tanam.contoh');

        // Tab navigasi
        $dataContoh = $ringkasan->dataContoh('persemaian');

        $tab = $dataContoh['tab'];

        foreach ($tab as &$item) {
            $item['aktif'] = $item['label'] === 'Persemaian bibit';
        }

        unset($item);

        $dataContoh['tab'] = $tab;

        return view('pages.persemaian-bibit', [
            'title'        => 'Persemaian Bibit',
            'tabAktif'     => 'persemaian',

            'daftarLahan'  => $daftarLahan,
            'lahan'         => $lahan,

            'phase'         => $lahan
                ? $dashboard->phase($lahan)
                : null,

            'waktu' => now('Asia/Jakarta')
                ->locale('id')
                ->translatedFormat('j M Y · H.i').' WIB',

            'persemaian'   => $persemaian,
            'data'          => $data,
            'umurBibit'     => $umurBibit,
            'contoh'        => $contoh,

            ...$persemaian,
            ...$dataContoh,
        ]);
    }


    // ==================================================
    // PENANAMAN
    // ==================================================
    public function penanaman(
        Request $request,
        DashboardService $dashboard,
        FaseTanamRingkasanService $ringkasan,
    ) {
        $daftarLahan = Lahan::orderBy('nama_lahan')->get();

        $lahan = $daftarLahan->firstWhere(
            'id',
            (int) $request->query('lahan')
        ) ?? $daftarLahan->first();

        $tanggalTanam = Carbon::create(
            2026, 11, 4, 0, 0, 0, 'Asia/Jakarta'
        );

        $penanaman = [
            'blok'         => 'Blok A',
            'siklus'       => 'MH-2026-A',
            'tanggal'      => $tanggalTanam,
            'luas'         => '1,2',
            'varietas'     => 'Inpari 32',
            'metode'       => 'Jajar legowo 2:1',
            'jumlah_bibit'  => 300000,
            'jarak_tanam'  => '25 × 12,5 × 50 cm',
            'umur_bibit'   => 20,
            'catatan'      => 'Penanaman merata, 2–3 bibit per lubang. Tinggi air 2 cm.',
            'hst'          => (int) $tanggalTanam->diffInDays(
                now('Asia/Jakarta')
            ),
        ];

        $dataContoh = $ringkasan->dataContoh('penanaman');

        $tab = $dataContoh['tab'];

        foreach ($tab as &$item) {
            $item['aktif'] = $item['label'] === 'Penanaman';
        }

        unset($item);

        $dataContoh['tab'] = $tab;

        return view('pages.penanaman', [
            'title'        => 'Penanaman',
            'tabAktif'     => 'penanaman',

            'daftarLahan'  => $daftarLahan,
            'lahan'         => $lahan,

            'phase'         => $lahan
                ? $dashboard->phase($lahan)
                : null,

            'waktu' => now('Asia/Jakarta')
                ->locale('id')
                ->translatedFormat('j M Y · H.i').' WIB',

            'penanaman'    => $penanaman,

            'blokB' => [
                'nama'   => 'Blok B',
                'status' => 'Persemaian sedang berjalan. Target pindah tanam 21 Nov 2026.',
            ],

            'varietas' => [
                'Inpari 32',
                'Inpari 42',
                'Ciherang',
                'IR64',
            ],

            'metode' => [
                'Jajar legowo 2:1',
                'Jajar legowo 4:1',
                'Tegel',
            ],

            ...$dataContoh,
        ]);
    }


    // ==================================================
    // PEMELIHARAAN
    // ==================================================
    public function pemeliharaan(
        Request $request,
        DashboardService $dashboard,
        FaseTanamRingkasanService $ringkasan,
    ) {
        $daftarLahan = Lahan::orderBy('nama_lahan')->get();

        $lahan = $daftarLahan->firstWhere(
            'id',
            (int) $request->query('lahan')
        ) ?? $daftarLahan->first();

        $sekarang = now('Asia/Jakarta')->locale('id');

        $pemeliharaan = [
            'siklus'     => 'MH-2026-A',
            'blok'       => 'Blok A',
            'hst'        => 6,
            'pemupukan'  => '11 Nov',
            'tinggi_air' => '3 cm',
            'jam_hama'   => '15.00',
            'petugas'    => 'Pak Budi',
        ];

        $dataContoh = $ringkasan->dataContoh('pemeliharaan');

        $tab = $dataContoh['tab'];

        foreach ($tab as &$item) {
            $item['aktif'] = $item['label'] === 'Pemeliharaan';
        }

        unset($item);

        $dataContoh['tab'] = $tab;

        return view('pages.pemeliharaan', [
            'title'        => 'Pemeliharaan',
            'tabAktif'     => 'pemeliharaan',

            'daftarLahan'  => $daftarLahan,
            'lahan'         => $lahan,

            'phase'         => $lahan
                ? $dashboard->phase($lahan)
                : null,

            'waktu'         => $sekarang->translatedFormat('j M Y · H.i').' WIB',
            'sekarang'      => $sekarang,

            'pemeliharaan'  => $pemeliharaan,

            ...$dataContoh,
        ]);
    }
}