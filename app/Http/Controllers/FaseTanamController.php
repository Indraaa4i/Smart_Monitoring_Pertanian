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

        $tanggalTanam = Carbon::create(
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

    // Halaman Persemaian Bibit
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

        // Data contoh persemaian (gabung dengan data tersimpan di session jika ada)
        $data = session('persemaian_data', $ringkasan->persemaian());

        // Menghitung umur bibit
        $tanggalSemai = Carbon::parse($data['tanggal_semai'], 'Asia/Jakarta');
        $tanggalPeriksa = Carbon::parse($data['tanggal_periksa'], 'Asia/Jakarta');
        $umurBibit = (int) $tanggalSemai->diffInDays($tanggalPeriksa);

        // Mengambil tab dari service
        $dataContoh = $ringkasan->dataContoh();
        $tab = $dataContoh['tab'];

        // Mengatur tab Persemaian Bibit agar aktif
        foreach ($tab as &$item) {
            if ($item['label'] === 'Persemaian bibit') {
                $item['url'] = route('fase-tanam.persemaian');
                $item['aktif'] = true;
            } else {
                $item['aktif'] = false;
            }
        }
        unset($item);

        $dataContoh['tab'] = $tab;

        return view('pages.persemaian-bibit', [
            'title'        => 'Persemaian Bibit',
            'sekarang'     => now('Asia/Jakarta')->locale('id'),
            'daftarLahan'  => $daftarLahan,
            'lahan'        => $lahan,
            'phase'        => $lahan ? $dashboard->phase($lahan) : null,
            'contoh'       => config('sawah_fase_tanam.contoh'),
            'waktu'        => now()->locale('id')
                ->translatedFormat('j M Y · H.i').' WIB',

            'data'         => $data,
            'umurBibit'    => $umurBibit,

            // Data tab dengan Persemaian Bibit aktif
            ...$dataContoh,
        ]);
    }

    // Simpan Pemeriksaan Persemaian Bibit
    public function storePersemaian(
        Request $request,
        FaseTanamRingkasanService $ringkasan,
    ) {
        $validated = $request->validate([
            'lahan_siklus'    => ['required', 'string'],
            'varietas'        => ['required', 'string'],
            'tanggal_semai'   => ['required', 'date'],
            'jumlah_benih'    => ['required', 'numeric', 'min:0'],
            'tanggal_periksa' => ['required', 'date', 'after_or_equal:tanggal_semai'],
            'kondisi'         => ['required', 'string'],
            'daya_tumbuh'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'target_pindah'   => ['nullable', 'date'],
            'catatan'         => ['nullable', 'string'],
        ]);

        $data = session('persemaian_data', $ringkasan->persemaian());

        // Pisahkan string "Blok B · MH-2026-B" jika diubah user
        $bagianLahan = explode(' · ', $validated['lahan_siklus']);
        $data['blok'] = $bagianLahan[0] ?? $data['blok'];
        $data['siklus'] = $bagianLahan[1] ?? $data['siklus'];

        $data['varietas'] = $validated['varietas'];
        $data['tanggal_semai'] = $validated['tanggal_semai'];
        $data['jumlah_benih'] = $validated['jumlah_benih'];
        $data['tanggal_periksa'] = $validated['tanggal_periksa'];
        $data['kondisi'] = $validated['kondisi'];
        $data['daya_tumbuh'] = $validated['daya_tumbuh'] ?? $data['daya_tumbuh'];
        $data['target_pindah'] = $validated['target_pindah'] ?? $data['target_pindah'];
        $data['catatan'] = $validated['catatan'] ?? '';

        // Hitung umur untuk dimasukkan ke baris terbaru tabel riwayat
        $tglSemai = Carbon::parse($validated['tanggal_semai'], 'Asia/Jakarta');
        $tglPeriksa = Carbon::parse($validated['tanggal_periksa'], 'Asia/Jakarta');
        $umurBaru = (int) $tglSemai->diffInDays($tglPeriksa);

        $riwayatBaru = [
            'tanggal'     => $tglPeriksa->locale('id')->translatedFormat('j M Y'),
            'umur'        => $umurBaru,
            'kondisi'     => explode(' · ', $validated['kondisi'])[0],
            'tinggi'      => $validated['catatan'] ?: 'Pertumbuhan normal',
            'daya_tumbuh' => ($validated['daya_tumbuh'] ?? $data['daya_tumbuh']).'%',
            'petugas'     => 'Bu Sari',
        ];

        array_unshift($data['riwayat'], $riwayatBaru);

        session(['persemaian_data' => $data]);

        return redirect()
            ->route('fase-tanam.persemaian')
            ->with('success', 'Data pemeriksaan persemaian bibit berhasil disimpan.');
    }
}