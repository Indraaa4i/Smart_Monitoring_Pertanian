<?php

namespace App\Services;

use App\Enums\SensorStatus;
use App\Models\DataSensor;
use App\Models\Lahan;
use Illuminate\Support\Facades\Schema;

/** Menyiapkan semua data Beranda. Blade hanya menampilkan. */
class DashboardService
{
    public function __construct(private WeatherService $weather)
    {
    }

    public function forLahan(Lahan $lahan): array
    {
        $params = config('sawah.sensors');   // kunci = nama kolom di tabel data_sensor
        // Sementara: tabel data_sensor belum dibuat (menyusul). Hapus pengecekan ini setelah migrasinya dijalankan.
        $adaData = Schema::hasTable('data_sensor');
        $latest = $adaData ? $lahan->dataSensor()->latest('waktu')->first() : null;

        $statuses = collect($params)->map(fn ($cfg, $key) => $this->statusOf($latest, $key, $cfg));
        $overall = SensorStatus::worst($statuses);

        return [
            'phase' => $this->phase($lahan),
            'cards' => collect($params)->map(fn ($cfg, $key) => $this->card($latest, $key, $cfg, $statuses[$key]))->values()->all(),
            'status' => $overall,
            'actions' => config("sawah.actions.{$overall->value}"),
            'history' => $this->history($lahan, $adaData),
            'weather' => $this->weather->forLahan($lahan),
        ];
    }

    private function level(float $value, array $cfg): string
    {
        return $value < $cfg['safe']['min'] ? 'low' : ($value > $cfg['safe']['max'] ? 'high' : 'ok');
    }

    private function fmt(float|int $n, array $cfg): string
    {
        return number_format($n, $cfg['decimals'], ',', '');
    }

    private function statusOf(?DataSensor $latest, string $key, array $cfg): SensorStatus
    {
        $value = $latest?->{$key};

        if ($value === null || $latest->waktu->lt(now()->subMinutes(config('sawah.offline_after_minutes')))) {
            return SensorStatus::Offline;
        }

        return $this->level($value, $cfg) === 'ok' ? SensorStatus::Aman : SensorStatus::Waspada;
    }

    private function card(?DataSensor $latest, string $key, array $cfg, SensorStatus $status): array
    {
        $offline = $status === SensorStatus::Offline;
        $sfx = $cfg['suffix'];
        $limit = $this->fmt($cfg['safe']['min'], $cfg).'–'.$this->fmt($cfg['safe']['max'], $cfg).$sfx;
        $limitLabel = config('sawah.limit_label');

        if ($offline) {
            $text = config('sawah.offline');
            $value = '–';
            $current = null;
        } else {
            $current = $latest->{$key};
            $level = $this->level($current, $cfg);
            $value = $this->fmt($current, $cfg);
            $text = $cfg['messages'][$level];
            $text['description'] = $level === 'ok'
                ? "Nilai {$value}{$sfx} berada dalam {$limitLabel} {$limit}."
                : "Nilai {$value}{$sfx} di luar {$limitLabel} {$limit}.";
        }

        return [
            'icon' => $cfg['icon'],
            'title' => $cfg['label'],
            'status' => $status->label(),
            'tone' => $status->tone(),
            'value' => $value,
            'unit' => $offline ? '' : $cfg['unit'],
            'headline' => $text['headline'],
            'description' => $text['description'],
            'hint' => $text['hint'],
            'min' => $cfg['scale']['min'],
            'max' => $cfg['scale']['max'],
            'safeMin' => $cfg['safe']['min'],
            'safeMax' => $cfg['safe']['max'],
            'current' => $current,
            'minLabel' => $this->fmt($cfg['scale']['min'], ['decimals' => 0]).$sfx,
            'maxLabel' => $this->fmt($cfg['scale']['max'], ['decimals' => 0]).$sfx,
            'rangeLabel' => ucfirst($limitLabel).': '.$limit,
        ];
    }

    private function phase(Lahan $lahan): ?array
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

        $last = end($phases);   // melewati semua fase yang dikonfigurasi

        return ['label' => $last['label'], 'tip' => $last['tip'], 'day' => $last['days'], 'total' => $last['days']];
    }

    private function history(Lahan $lahan, bool $adaData): array
    {
        $params = config('sawah.sensors');

        $rows = ($adaData ? $lahan->dataSensor()->latest('waktu')->limit(config('sawah.history_rows'))->get() : collect())
            ->map(function (DataSensor $r) use ($params) {
                $values = [];
                $statuses = [];

                foreach ($params as $key => $cfg) {
                    $v = $r->{$key};
                    $values[] = $v === null ? '–' : $this->fmt($v, $cfg).$cfg['suffix'];
                    if ($v !== null) {
                        $statuses[] = $this->level($v, $cfg) === 'ok' ? SensorStatus::Aman : SensorStatus::Waspada;
                    }
                }

                $status = SensorStatus::worst($statuses);

                return [
                    'time' => $r->waktu->format('H.i'),
                    'values' => $values,
                    'status' => $status->label(),
                    'tone' => $status->tone(),
                ];
            })
            ->all();

        return [
            'columns' => collect($params)->pluck('short')->values()->all(),
            'rows' => $rows,
            'last' => $rows[0]['time'] ?? null,
        ];
    }
}
