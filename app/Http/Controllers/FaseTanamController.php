<?php

namespace App\Http\Controllers;

use App\Models\Lahan;
use App\Services\DashboardService;
use App\Services\FaseTanamRingkasanService;
use App\Services\FaseTanamService;
use App\Services\FaseTanamSiklusService;
use Illuminate\Http\Request;

class FaseTanamController extends Controller
{
    public function __invoke(
        Request $request,
        FaseTanamService $service,
        DashboardService $dashboard,
        FaseTanamRingkasanService $ringkasan,
        FaseTanamSiklusService $siklusService,
    ) {
        // Untuk topbar (pilihan lahan dan pilih fase).
        // Data siklus masih menggunakan contoh dari config.
        $daftarLahan = Lahan::orderBy('nama_lahan')->get();

        $lahan = $daftarLahan->firstWhere(
            'id',
            (int) $request->query('lahan')
        ) ?? $daftarLahan->first();

        // Menentukan tab yang aktif
        $tabAktif = $request->query('tab') === 'siklus'
            ? 'siklus'
            : 'ringkasan';

        // Mengambil data siklus
        $dataSiklus = $siklusService->data();

        return view('pages.fase-tanam', [
            'daftarLahan' => $daftarLahan,
            'lahan' => $lahan,
            'phase' => $lahan ? $dashboard->phase($lahan) : null,
            'siklus' => $service->siklusAktif(),
            'contoh' => config('sawah_fase_tanam.contoh'),
            'waktu' => now()->locale('id')
                ->translatedFormat('j M Y · H.i').' WIB',

            'tabAktif' => $tabAktif,
            'dataSiklus' => $dataSiklus,

            // Data contoh: tab, kartu ringkasan,
            // progres tahapan, aktivitas terdekat.
            ...$ringkasan->dataContoh($tabAktif),
        ]);
    }

    // Halaman Penanaman
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

        $tanggalTanam = \Carbon\Carbon::create(
            2026, 11, 4, 0, 0, 0, 'Asia/Jakarta'
        );

        // Data contoh penanaman
        $penanaman = [
            'blok'         => 'Blok A',
            'siklus'       => 'MH-2026-A',
            'tanggal'      => $tanggalTanam,
            'luas'         => '1,2',
            'varietas'     => 'Inpari 32',
            'metode'       => 'Jajar legowo 2:1',
            'jumlah_bibit' => 300000,
            'jarak_tanam'  => '25 × 12,5 × 50 cm',
            'umur_bibit'   => 20,
            'catatan'      => 'Penanaman merata, 2–3 bibit per lubang. Tinggi air 2 cm.',
            'hst'          => (int) $tanggalTanam->diffInDays(now('Asia/Jakarta')),
        ];

        // Ambil data tab dari service yang sudah ada
        $dataContoh = $ringkasan->dataContoh();
        $tab = $dataContoh['tab'];

        // Atur tab Penanaman agar aktif
        foreach ($tab as &$item) {
            if ($item['label'] === 'Penanaman') {
                $item['url'] = route('fase-tanam.penanaman');
                $item['aktif'] = true;
            } else {
                $item['aktif'] = false;
            }
        }
        unset($item);

        // Masukkan tab yang sudah disesuaikan kembali ke data
        $dataContoh['tab'] = $tab;

        return view('pages.penanaman', [
            'title'        => 'Penanaman',
            'sekarang'     => now('Asia/Jakarta')->locale('id'),
            'daftarLahan'  => $daftarLahan,
            'lahan'        => $lahan,
            'phase'        => $lahan ? $dashboard->phase($lahan) : null,
            'waktu'        => now()->locale('id')
                ->translatedFormat('j M Y · H.i').' WIB',

            'penanaman'    => $penanaman,
            'blokB'        => [
                'nama'   => 'Blok B',
                'status' => 'Persemaian sedang berjalan. Target pindah tanam 21 Nov 2026.',
            ],
            'varietas'     => ['Inpari 32', 'Inpari 42', 'Ciherang', 'IR64'],
            'metode'       => ['Jajar legowo 2:1', 'Jajar legowo 4:1', 'Tegel'],

            // Data tab dari service, dengan Penanaman aktif
            ...$dataContoh,
        ]);
    }
}
