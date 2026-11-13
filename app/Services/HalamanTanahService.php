<?php

namespace App\Services;

use App\Models\Lahan;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

/**
 * Data bersama halaman Kondisi Tanah dan Rekomendasi.
 * Label, ikon, skala, dan batas aman dibaca dari config('sawah.sensors').
 * Tabel sensor belum aktif (T4), jadi halaman menampilkan keadaan "sensor belum terhubung".
 * Saat T4/T5 selesai, tambahkan pembacaan nyata di sini (T16).
 */
class HalamanTanahService
{
    public function daftarLahan(): Collection
    {
        return Lahan::orderBy('nama_lahan')->get();
    }

    public function lahanTerpilih(int $id): ?Lahan
    {
        return ($id > 0 ? Lahan::find($id) : null) ?? Lahan::orderBy('id')->first();
    }

    /** @return array<string, array<string, mixed>> */
    public function parameterOffline(): array
    {
        $hasil = [];

        foreach (config('sawah.sensors') as $kunci => $s) {
            $min = $s['scale']['min'];
            $maks = $s['scale']['max'];
            $amanMin = $s['safe']['min'];
            $amanMaks = $s['safe']['max'];
            $rentang = $maks - $min;

            $hasil[$kunci] = [
                'kunci'      => $kunci,
                'label'      => $s['label'],
                'ikon'       => $s['icon'],
                'sensor'     => config("sawah.halaman.sensors.$kunci.sensor", 'Sensor '.$s['short']),
                'chip'       => config("sawah.halaman.sensors.$kunci.chip", $s['short']),
                'batas'      => 'Batas aman: '.$this->angka($amanMin, $s['decimals']).'–'
                                .$this->angka($amanMaks, $s['decimals']).$s['suffix'],
                'skala'      => [$this->angkaBulat($min).$s['suffix'], $this->angkaBulat($maks).$s['suffix']],
                'zona_awal'  => round(($amanMin - $min) / $rentang * 100, 1),
                'zona_lebar' => round(($amanMaks - $amanMin) / $rentang * 100, 1),
            ];
        }

        return $hasil;
    }

    public function phase(Lahan $lahan): ?array
    {
        if ($lahan->tanggal_tanam === null) {
            return null;
        }

        $day = max(1, (int) $lahan->tanggal_tanam->copy()->startOfDay()->diffInDays(today()) + 1);
        $phases = config('sawah.phases');
        $start = 1;

        foreach ($phases as $p) {
            $end = $start + $p['days'] - 1;
            if ($day <= $end) {
                return ['label' => $p['label'], 'tip' => $p['tip'], 'day' => $day - $start + 1, 'total' => $p['days']];
            }
            $start = $end + 1;
        }

        $last = end($phases);

        return ['label' => $last['label'], 'tip' => $last['tip'], 'day' => $last['days'], 'total' => $last['days']];
    }

    public function tanggalPanjang(): string
    {
        return CarbonImmutable::now()->locale('id')->translatedFormat('l, j F Y');
    }

    private function angka(float|int $nilai, int $desimal): string
    {
        return number_format($nilai, $desimal, ',', '.');
    }

    /** Angka skala tanpa nol di belakang koma (0, 14, 100). */
    private function angkaBulat(float|int $nilai): string
    {
        return rtrim(rtrim(number_format($nilai, 1, ',', '.'), '0'), ',');
    }
}