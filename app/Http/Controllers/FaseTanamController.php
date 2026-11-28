<?php

namespace App\Http\Controllers;

use App\Models\Lahan;
use App\Services\DashboardService;
use App\Services\FaseTanamJadwalService;
use App\Services\FaseTanamPersiapanService;
use App\Services\FaseTanamRingkasanService;
use App\Services\FaseTanamRiwayatService;
use App\Services\FaseTanamService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FaseTanamController extends Controller
{
    // Tanggal tanam contoh (Blok A, MH-2026-A). Dipakai untuk menghitung HST.
    private const TANGGAL_TANAM = '2026-11-04';

    // ==================================================
    // RINGKASAN, SIKLUS, RIWAYAT, JADWAL, PERSIAPAN
    // ==================================================
    public function __invoke(
        Request $request,
        FaseTanamService $service,
        DashboardService $dashboard,
        FaseTanamRingkasanService $ringkasan,
        FaseTanamRiwayatService $riwayatService,
        FaseTanamJadwalService $jadwalService,
        FaseTanamPersiapanService $persiapanService,
    ) {
        $daftarLahan = Lahan::orderBy('nama_lahan')->get();

        $lahan = $daftarLahan->firstWhere(
            'id',
            (int) $request->query('lahan')
        ) ?? $daftarLahan->first();

        $tabDiminta = (string) $request->query('tab', 'ringkasan');
        $tabAktif = in_array(
            $tabDiminta,
            ['ringkasan', 'siklus', 'riwayat', 'jadwal', 'persiapan'],
            true
        ) ? $tabDiminta : 'ringkasan';

        $dataContoh = $ringkasan->dataContoh($tabAktif);

        // Data riwayat hanya dihitung ketika tab Riwayat Tanam dibuka.
        $dataRiwayat = null;

        if ($tabAktif === 'riwayat') {
            $filterRiwayat = $request->validate([
                'q' => ['nullable', 'string', 'max:100'],
                'tahun' => ['nullable', 'regex:/^(semua|\d{4})$/'],
                'blok' => ['nullable', 'string', 'max:100'],
            ]);

            $dataRiwayat = $riwayatService->data($filterRiwayat);
        }

        // Data jadwal hanya dihitung ketika tab Jadwal & Aktivitas dibuka.
        $dataJadwal = null;

        if ($tabAktif === 'jadwal') {
            $filterJadwal = $request->validate([
                'bulan' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
                'tanggal' => ['nullable', 'date_format:Y-m-d'],
                'lahan_id' => ['nullable', 'integer'],
                'status' => ['nullable', 'in:semua,menunggu,selesai'],
            ]);

            $dataJadwal = $jadwalService->data($filterJadwal, $daftarLahan);
        }

        // Data Persiapan Lahan hanya disusun ketika tab tersebut dibuka.
        $dataPersiapan = null;

        if ($tabAktif === 'persiapan') {
            $dataPersiapan = $persiapanService->data($lahan, $daftarLahan);
        }

        // ==================================================
        // DATA SIKLUS TANAM (data contoh yang sudah ada)
        // ==================================================
        $dataSiklus = [
            'summary' => [
                'total' => 2,
                'aktif' => 1,
                'rencana' => 1,
            ],

            'siklus' => [
                [
                    'nama' => 'Siklus Tanam 1',
                    'lahan' => 'Blok A',
                    'musim' => 'MH-2026-A',
                    'varietas' => 'Inpari 32',
                    'luas_ha' => 1.2,
                    'mulai' => '2026-11-04',
                    'akhir' => '2027-02-22',
                    'tahap' => 'Pemeliharaan',
                    'status' => 'Aktif',
                    'catatan' => 'Siklus tanam aktif pada Blok A.',
                ],
                [
                    'nama' => 'Siklus Tanam 2',
                    'lahan' => 'Blok B',
                    'musim' => 'MH-2026-B',
                    'varietas' => 'Ciherang',
                    'luas_ha' => 1.3,
                    'mulai' => '2026-11-21',
                    'akhir' => '2027-03-10',
                    'tahap' => 'Persemaian',
                    'status' => 'Rencana',
                    'catatan' => 'Bibit sedang disiapkan untuk rencana penanaman Blok B.',
                ],
            ],
        ];

        return view('pages.fase-tanam', [
            'title' => 'Fase Tanam',
            'tabAktif' => $tabAktif,

            'daftarLahan' => $daftarLahan,
            'lahan' => $lahan,

            'phase' => $lahan
                ? $dashboard->phase($lahan)
                : null,

            'siklus' => $this->siapkanSiklusAktif($service, $ringkasan),
            'dataSiklus' => $dataSiklus,
            'dataRiwayat' => $dataRiwayat,
            'dataJadwal' => $dataJadwal,
            'dataPersiapan' => $dataPersiapan,

            'contoh' => config('sawah_fase_tanam.contoh'),

            'waktu' => now('Asia/Jakarta')
                ->locale('id')
                ->translatedFormat('j M Y · H.i').' WIB',

            ...$dataContoh,
        ]);
    }

    // ==================================================
    // SIMPAN CATATAN PERSIAPAN LAHAN (T20)
    // ==================================================
    public function storePersiapanLahan(
        Request $request,
        FaseTanamPersiapanService $persiapanService,
    ): RedirectResponse {
        $opsiKondisi = config('sawah_persiapan_lahan.kondisi', []);
        $opsiMetode = config('sawah_persiapan_lahan.metode', []);

        $validated = $request->validate([
            'lahan_id' => [
                'required',
                'integer',
                Rule::exists('lahan', 'id')->whereNull('deleted_at'),
            ],
            'sumber' => ['required', 'in:manual'],
            'tanggal_pemeriksaan' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'kondisi_tanah' => ['required', Rule::in($opsiKondisi)],
            'metode_pengolahan' => ['required', Rule::in($opsiMetode)],
            'ph' => ['required', 'numeric', 'between:0,14'],
            'kelembapan' => ['required', 'numeric', 'between:0,100'],
            'ketinggian_air' => ['required', 'numeric', 'min:0', 'max:1000'],
            'penanggung_jawab' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'array', 'max:5'],
            'foto.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'foto.max' => 'Maksimal 5 foto untuk satu catatan.',
            'foto.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
            'foto.*.mimes' => 'Foto harus berformat JPG atau PNG.',
            'ph.between' => 'Nilai pH harus berada pada rentang 0 sampai 14.',
            'kelembapan.between' => 'Kelembapan harus berada pada rentang 0 sampai 100%.',
        ]);

        $lahan = Lahan::findOrFail($validated['lahan_id']);

        $fotoPaths = [];
        foreach ($request->file('foto', []) as $foto) {
            $path = $foto->store('persiapan-lahan', 'public');

            if ($path !== false) {
                $fotoPaths[] = $path;
            }
        }

        $persiapanService->simpan($lahan, $validated, $fotoPaths);

        return redirect()
            ->route('fase-tanam', [
                'tab' => 'persiapan',
                'lahan' => $lahan->id,
            ])
            ->with('success', 'Catatan persiapan lahan berhasil disimpan.');
    }

    // ==================================================
    // SIMPAN AKTIVITAS JADWAL
    // ==================================================
    public function storeAktivitas(
        Request $request,
        FaseTanamJadwalService $jadwalService,
    ): RedirectResponse {
        $validated = $request->validate([
            'kegiatan' => ['required', 'string', 'max:120'],
            'lahan_id' => [
                'required',
                'integer',
                Rule::exists('lahan', 'id')->whereNull('deleted_at'),
            ],
            'tanggal' => ['required', 'date_format:Y-m-d'],
            'waktu' => ['required', 'date_format:H:i'],
            'prioritas' => ['required', 'in:normal,tinggi'],
            'pengingat' => ['required', 'in:0,30,60'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'bulan' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
            'filter_lahan' => ['nullable', 'integer'],
            'filter_status' => ['nullable', 'in:semua,menunggu,selesai'],
        ]);

        $jadwalService->tambah($validated);

        return $this->redirectKeJadwal($request, $validated['tanggal'])
            ->with('success', 'Aktivitas berhasil ditambahkan.');
    }

    // ==================================================
    // TANDAI AKTIVITAS SELESAI
    // ==================================================
    public function selesaikanAktivitas(
        Request $request,
        FaseTanamJadwalService $jadwalService,
        string $aktivitas,
    ): RedirectResponse {
        $berhasil = $jadwalService->tandaiSelesai($aktivitas);

        abort_unless($berhasil, 404);

        $tanggalKembali = $request->validate([
            'bulan' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
            'lahan_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:semua,menunggu,selesai'],
        ]);

        $tanggal = $tanggalKembali['tanggal']
            ?? now('Asia/Jakarta')->format('Y-m-d');

        return $this->redirectKeJadwal($request, $tanggal)
            ->with('success', 'Aktivitas berhasil ditandai selesai.');
    }

    /**
     * Mengembalikan pengguna ke tab Jadwal dengan filter yang dipakai.
     */
    private function redirectKeJadwal(Request $request, string $tanggal): RedirectResponse
    {
        $bulan = substr($tanggal, 0, 7);
        $lahanId = $request->input('lahan_id')
            ?? $request->input('filter_lahan');

        $parameter = [
            'tab' => 'jadwal',
            'bulan' => $bulan,
            'tanggal' => $tanggal,
        ];

        if (is_numeric($lahanId) && (int) $lahanId > 0) {
            $parameter['lahan_id'] = (int) $lahanId;
        }

        return redirect()->route('fase-tanam', $parameter);
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

        $umurBibit = Carbon::parse(
            $persemaian['tanggal_semai'],
            'Asia/Jakarta'
        )->diffInDays(now('Asia/Jakarta'));

        $contoh = config('sawah_fase_tanam.contoh');

        $dataContoh = $ringkasan->dataContoh('persemaian');
        $tab = $dataContoh['tab'];

        foreach ($tab as &$item) {
            $item['aktif'] = $item['label'] === 'Persemaian bibit';
        }

        unset($item);
        $dataContoh['tab'] = $tab;

        return view('pages.persemaian-bibit', [
            'title' => 'Persemaian Bibit',
            'tabAktif' => 'persemaian',
            'daftarLahan' => $daftarLahan,
            'lahan' => $lahan,
            'phase' => $lahan ? $dashboard->phase($lahan) : null,
            'waktu' => now('Asia/Jakarta')
                ->locale('id')
                ->translatedFormat('j M Y · H.i').' WIB',
            'persemaian' => $persemaian,
            'data' => $data,
            'umurBibit' => $umurBibit,
            'contoh' => $contoh,
            ...$persemaian,
            ...$dataContoh,
        ]);
    }

    // ==================================================
    // SIMPAN PEMERIKSAAN PERSEMAIAN
    // ==================================================
    public function storePersemaian(
        Request $request,
        FaseTanamRingkasanService $ringkasan,
    ): RedirectResponse {
        $validated = $request->validate([
            'lahan_siklus' => ['required', 'string'],
            'varietas' => ['required', 'string'],
            'tanggal_semai' => ['required', 'date'],
            'jumlah_benih' => ['required', 'numeric', 'min:0'],
            'tanggal_periksa' => ['required', 'date', 'after_or_equal:tanggal_semai'],
            'kondisi' => ['required', 'string'],
            'daya_tumbuh' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'target_pindah' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string'],
        ]);

        $data = session('persemaian_data', $this->dataPersemaianDefault());

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

        $tglSemai = Carbon::parse($validated['tanggal_semai'], 'Asia/Jakarta');
        $tglPeriksa = Carbon::parse($validated['tanggal_periksa'], 'Asia/Jakarta');
        $umurBaru = (int) $tglSemai->diffInDays($tglPeriksa);

        $riwayatBaru = [
            'tanggal' => $tglPeriksa->locale('id')->translatedFormat('j M Y'),
            'umur' => $umurBaru,
            'kondisi' => explode(' · ', $validated['kondisi'])[0],
            'tinggi' => $validated['catatan'] ?: 'Pertumbuhan normal',
            'daya_tumbuh' => ($validated['daya_tumbuh'] ?? $data['daya_tumbuh']).'%',
            'petugas' => 'Bu Sari',
        ];

        array_unshift($data['riwayat'], $riwayatBaru);
        session(['persemaian_data' => $data]);

        return redirect()
            ->route('fase-tanam.persemaian')
            ->with('success', 'Data pemeriksaan persemaian bibit berhasil disimpan.');
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

        $tanggalTanam = Carbon::create(2026, 11, 4, 0, 0, 0, 'Asia/Jakarta');

        $penanaman = [
            'blok' => 'Blok A',
            'siklus' => 'MH-2026-A',
            'tanggal' => $tanggalTanam,
            'luas' => '1,2',
            'varietas' => 'Inpari 32',
            'metode' => 'Jajar legowo 2:1',
            'jumlah_bibit' => 300000,
            'jarak_tanam' => '25 × 12,5 × 50 cm',
            'umur_bibit' => 20,
            'catatan' => 'Penanaman merata, 2–3 bibit per lubang. Tinggi air 2 cm.',
            'hst' => (int) $tanggalTanam->diffInDays(now('Asia/Jakarta')),
        ];

        $dataContoh = $ringkasan->dataContoh('penanaman');
        $tab = $dataContoh['tab'];

        foreach ($tab as &$item) {
            $item['aktif'] = $item['label'] === 'Penanaman';
        }

        unset($item);
        $dataContoh['tab'] = $tab;

        return view('pages.penanaman', [
            'title' => 'Penanaman',
            'tabAktif' => 'penanaman',
            'daftarLahan' => $daftarLahan,
            'lahan' => $lahan,
            'phase' => $lahan ? $dashboard->phase($lahan) : null,
            'waktu' => now('Asia/Jakarta')
                ->locale('id')
                ->translatedFormat('j M Y · H.i').' WIB',
            'penanaman' => $penanaman,
            'blokB' => [
                'nama' => 'Blok B',
                'status' => 'Persemaian sedang berjalan. Target pindah tanam 21 Nov 2026.',
            ],
            'varietas' => ['Inpari 32', 'Inpari 42', 'Ciherang', 'IR64'],
            'metode' => ['Jajar legowo 2:1', 'Jajar legowo 4:1', 'Tegel'],
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
            'siklus' => 'MH-2026-A',
            'blok' => 'Blok A',
            // HST dihitung otomatis dari tanggal tanam (sebelumnya hardcode 6).
            'hst' => $this->hitungHst(),
            'pemupukan' => '11 Nov',
            'tinggi_air' => '3 cm',
            'jam_hama' => '15.00',
            'petugas' => 'Pak Budi',
        ];

        $dataContoh = $ringkasan->dataContoh('pemeliharaan');
        $tab = $dataContoh['tab'];

        foreach ($tab as &$item) {
            $item['aktif'] = $item['label'] === 'Pemeliharaan';
        }

        unset($item);
        $dataContoh['tab'] = $tab;

        return view('pages.pemeliharaan', [
            'title' => 'Pemeliharaan',
            'tabAktif' => 'pemeliharaan',
            'daftarLahan' => $daftarLahan,
            'lahan' => $lahan,
            'phase' => $lahan ? $dashboard->phase($lahan) : null,
            'waktu' => $sekarang->translatedFormat('j M Y · H.i').' WIB',
            'sekarang' => $sekarang,
            'pemeliharaan' => $pemeliharaan,
            'logPemeliharaan' => session('pemeliharaan_log', []),
            ...$dataContoh,
        ]);
    }

    // ==================================================
    // SIMPAN CATATAN PEMELIHARAAN
    // ==================================================
    public function storePemeliharaan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'jenis' => ['required', 'in:pemupukan,penyiangan,pengairan,pestisida,lainnya'],
            'bahan' => ['nullable', 'string', 'max:100'],
            'dosis' => ['nullable', 'string', 'max:100'],
            'tinggi_air' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'tanggal.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
            'jenis.in' => 'Jenis pemeliharaan tidak valid.',
            'tinggi_air.max' => 'Tinggi air maksimal 100 cm.',
        ]);

        $log = session('pemeliharaan_log', []);

        array_unshift($log, [
            ...$validated,
            'siklus' => 'MH-2026-A',
            'blok' => 'Blok A',
            'dicatat' => now('Asia/Jakarta')->format('Y-m-d H:i:s'),
        ]);

        session(['pemeliharaan_log' => $log]);

        return redirect()
            ->to(route('fase-tanam.pemeliharaan').'#log-pemeliharaan')
            ->with('success', 'Catatan pemeliharaan berhasil disimpan.');
    }

    /**
     * Melengkapi data siklus aktif: tombol "Catat pemeliharaan" diarahkan ke
     * form pemeliharaan, dan progres tahapan disamakan dengan daftar tahapan.
     */
    private function siapkanSiklusAktif(
        FaseTanamService $service,
        FaseTanamRingkasanService $ringkasan,
    ) {
        $siklus = $service->siklusAktif();
        $progres = $ringkasan->tahapan()['progres'];

        $siklus['aksi'] ??= [];
        $siklus['aksi']['catat_pemeliharaan'] = route('fase-tanam.pemeliharaan').'#form-pemeliharaan';

        $siklus['progres'] = [
            'teks' => $progres['teks'],
            'persen' => $progres['persen'],
        ];

        return $siklus;
    }

    // ==================================================
    // PANEN & EVALUASI
    // ==================================================
    public function panen(
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

        $panen = [
            'siklus' => 'MH-2026-A',
            'blok' => 'Blok A',
            'varietas' => 'Inpari 32',
            'luas_ha' => 1.2,
            'hst' => $this->hitungHst(),
            'perkiraan_panen' => Carbon::create(2027, 2, 22, 0, 0, 0, 'Asia/Jakarta')
                ->locale('id')
                ->translatedFormat('j F Y'),
        ];

        $dataContoh = $ringkasan->dataContoh('panen');

        return view('pages.panen', [
            'title' => 'Panen & Evaluasi',
            'tabAktif' => 'panen',
            'daftarLahan' => $daftarLahan,
            'lahan' => $lahan,
            'phase' => $lahan ? $dashboard->phase($lahan) : null,
            'waktu' => $sekarang->translatedFormat('j M Y · H.i').' WIB',
            'sekarang' => $sekarang,
            'panen' => $panen,
            'logPanen' => session('panen_log', []),
            ...$dataContoh,
        ]);
    }

    // ==================================================
    // SIMPAN CATATAN PANEN & EVALUASI
    // ==================================================
    public function storePanen(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_panen' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'luas_dipanen' => ['required', 'numeric', 'min:0.01', 'max:1000'],
            'hasil_kg' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'kadar_air' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'harga_per_kg' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'evaluasi' => ['nullable', 'string', 'max:1000'],
        ], [
            'tanggal_panen.before_or_equal' => 'Tanggal panen tidak boleh lebih dari hari ini.',
            'luas_dipanen.min' => 'Luas dipanen harus lebih dari 0 ha.',
            'kadar_air.max' => 'Kadar air harus berada pada rentang 0 sampai 100%.',
        ]);

        $log = session('panen_log', []);

        array_unshift($log, [
            ...$validated,
            'siklus' => 'MH-2026-A',
            'blok' => 'Blok A',
            // Produktivitas dalam ton per hektar.
            'produktivitas' => round(
                ((float) $validated['hasil_kg'] / (float) $validated['luas_dipanen']) / 1000,
                2
            ),
            'dicatat' => now('Asia/Jakarta')->format('Y-m-d H:i:s'),
        ]);

        session(['panen_log' => $log]);

        return redirect()
            ->to(route('fase-tanam.panen').'#log-panen')
            ->with('success', 'Catatan panen dan evaluasi berhasil disimpan.');
    }

    private function hitungHst(): int
    {
        return max(0, (int) Carbon::parse(self::TANGGAL_TANAM, 'Asia/Jakarta')
            ->startOfDay()
            ->diffInDays(now('Asia/Jakarta')->startOfDay()));
    }

    // ==================================================
    // DATA CONTOH PERSEMAIAN
    // ==================================================
    private function dataPersemaianDefault(): array
    {
        return [
            'blok' => 'Blok B',
            'siklus' => 'MH-2026-B',
            'varietas' => 'Inpari 32',
            'tanggal_semai' => '2026-11-01',
            'jumlah_benih' => 25000,
            'tanggal_periksa' => '2026-11-19',
            'kondisi' => 'Baik · Pertumbuhan normal',
            'daya_tumbuh' => 90,
            'target_pindah' => '2026-11-21',
            'catatan' => 'Bibit tumbuh dengan baik.',
            'riwayat' => [],
        ];
    }
}
