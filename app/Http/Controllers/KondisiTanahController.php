<?php

namespace App\Http\Controllers;

use App\Services\HalamanTanahService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KondisiTanahController extends Controller
{
    public function __invoke(Request $request, HalamanTanahService $layanan): View
    {
        $lahan = $layanan->lahanTerpilih($request->integer('lahan'));

        return view('pages.kondisi-tanah', [
            // Variabel untuk topbar. `phase` samakan dengan yang diisi DashboardController.
            'lahan'       => $lahan,
            'daftarLahan' => $layanan->daftarLahan(),
            'phase'       => $layanan->phase($lahan),

            'parameter'   => $layanan->parameterOffline(),
            'tanggal'     => $layanan->tanggalPanjang(),
        ]);
    }
}