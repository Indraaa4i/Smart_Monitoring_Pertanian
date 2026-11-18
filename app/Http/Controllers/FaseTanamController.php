<?php

namespace App\Http\Controllers;

use App\Models\Lahan;
use App\Services\DashboardService;
use App\Services\FaseTanamRingkasanService;
use App\Services\FaseTanamService;
use Illuminate\Http\Request;

class FaseTanamController extends Controller
{
    public function __invoke(
        Request $request,
        FaseTanamService $service,
        DashboardService $dashboard,
        FaseTanamRingkasanService $ringkasan,
    ) {
        // Untuk topbar (pilihan lahan dan pil fase). Data siklus sendiri masih contoh dari config.
        $daftarLahan = Lahan::orderBy('nama_lahan')->get();
        $lahan = $daftarLahan->firstWhere('id', (int) $request->query('lahan')) ?? $daftarLahan->first();

        return view('pages.fase-tanam', [
            'daftarLahan' => $daftarLahan,
            'lahan' => $lahan,
            'phase' => $lahan ? $dashboard->phase($lahan) : null,
            'siklus' => $service->siklusAktif(),
            'contoh' => config('sawah_fase_tanam.contoh'),
            'waktu' => now()->locale('id')->translatedFormat('j M Y · H.i').' WIB',

            // Data contoh: tab, kartu ringkasan, progres tahapan, aktivitas terdekat.
            ...$ringkasan->dataContoh(),
        ]);
    }
}