<?php

namespace App\Services;

use Carbon\Carbon;

class FaseTanamSiklusService
{
    public function data(): array
    {
        $cycles = config('sawah_fase_tanam.daftar_siklus', []);

        $aktif = count(array_filter(
            $cycles,
            fn ($item) => $item['status'] === 'Aktif'
        ));

        $rencana = count(array_filter(
            $cycles,
            fn ($item) => $item['status'] === 'Rencana'
        ));

        return [
            'siklus' => $cycles,
            'summary' => [
                'total' => count($cycles),
                'aktif' => $aktif,
                'rencana' => $rencana,
            ],
        ];
    }

    public function formatTanggal(string $tanggal): string
    {
        return Carbon::parse($tanggal)
            ->locale('id')
            ->translatedFormat('j M Y');
    }
}
