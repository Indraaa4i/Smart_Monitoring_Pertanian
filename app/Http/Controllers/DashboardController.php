<?php

namespace App\Http\Controllers;

use App\Models\Lahan;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardService $dashboard)
    {
        // TODO setelah login ada: batasi ke lahan milik petani yang login (auth()->user()->lahan()).
        $daftarLahan = Lahan::orderBy('nama_lahan')->get();
        $lahan = $daftarLahan->firstWhere('id', (int) $request->query('lahan')) ?? $daftarLahan->first();

        abort_if($lahan === null, 404, 'Belum ada data lahan. Jalankan seeder atau tambahkan lahan.');

        return view('pages.beranda', ['daftarLahan' => $daftarLahan, 'lahan' => $lahan] + $dashboard->forLahan($lahan));
    }
}
