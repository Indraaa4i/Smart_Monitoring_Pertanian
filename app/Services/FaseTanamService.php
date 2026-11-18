<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

/** Menyusun data halaman Fase Tanam. Saat ini dari config (data contoh), belum dari database. */
class FaseTanamService
{
    public function siklusAktif(): array
    {
        $c = config('sawah_fase_tanam.siklus_aktif');

        $tanam = Carbon::parse($c['tanggal_tanam'])->startOfDay();
        $hst = max(0, (int) $tanam->diffInDays(today()));

        $tahap = collect($c['tahap'])
            ->map(fn (array $t) => $t + ['url' => $this->url($t['rute'] ?? null)])
            ->all();

        $selesai = count(array_filter($tahap, fn (array $t) => $t['status'] === 'selesai'));
        $total = count($tahap);
        $persen = $total > 0 ? (int) round($selesai / $total * 100) : 0;
        $berjalan = collect($tahap)->firstWhere('status', 'berlangsung');

        $luas = $this->luas($c['luas_ha']);

        return [
            'kode' => $c['kode'],
            'judul' => $c['musim'].' · '.$c['lahan'],
            'ringkas' => $c['varietas'].' · '.$luas.' · Tanam '.$this->tanggal($c['tanggal_tanam'])
                .' · Perkiraan panen '.$this->tanggal($c['perkiraan_panen']),
            'lencana' => $berjalan ? $berjalan['nama'].' · '.$hst.' HST' : $c['status'],
            'progres' => [
                'selesai' => $selesai,
                'total' => $total,
                'persen' => $persen,
                'teks' => $selesai.' dari '.$total.' tahap selesai · '.$persen.'% tahapan',
            ],
            'tahap' => $tahap,
            'aksi' => ['catat_pemeliharaan' => $this->url($c['rute_catat_pemeliharaan'] ?? null)],
            'detail' => [
                'judul' => 'Detail siklus · '.$c['kode'],
                'subjudul' => $c['musim'].' · '.$c['status'].($berjalan ? ' · '.$berjalan['nama'].', '.$hst.' HST' : ''),
                'kolom' => [
                    ['label' => 'Lahan', 'nilai' => $c['lahan'].' · '.$luas],
                    ['label' => 'Varietas padi', 'nilai' => $c['varietas']],
                    ['label' => 'Mulai persiapan', 'nilai' => $this->tanggal($c['mulai_persiapan'])],
                    ['label' => 'Tanggal tanam', 'nilai' => $this->tanggal($c['tanggal_tanam'])],
                    ['label' => 'Perkiraan panen', 'nilai' => $this->tanggal($c['perkiraan_panen'])],
                    ['label' => 'Penanggung jawab', 'nilai' => $c['penanggung_jawab']],
                ],
                'catatan' => $c['catatan'] ?? null,
                'tahap' => array_values(array_filter($tahap, fn (array $t) => $t['tombol_modal'] ?? false)),
            ],
        ];
    }

    /** URL route bila route-nya sudah ada; null bila belum (tombol ditampilkan nonaktif). */
    private function url(?string $rute): ?string
    {
        return $rute && Route::has($rute) ? route($rute) : null;
    }

    private function tanggal(string $tanggal): string
    {
        return Carbon::parse($tanggal)->locale('id')->translatedFormat('j M Y');
    }

    /** 1.2 → "1,2 ha"; 2.0 → "2 ha" */
    private function luas(float $ha): string
    {
        return rtrim(rtrim(number_format($ha, 2, ',', ''), '0'), ',').' ha';
    }
}