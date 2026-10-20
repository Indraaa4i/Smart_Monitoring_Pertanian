<?php

namespace App\Enums;

enum SensorStatus: string
{
    case Aman = 'aman';
    case Waspada = 'waspada';
    case Offline = 'offline';

    public function label(): string
    {
        return match ($this) {
            self::Aman => 'Aman',
            self::Waspada => 'Perlu dicek',
            self::Offline => 'Offline',
        };
    }

    /** Dipakai untuk kelas CSS: ok | warn | off */
    public function tone(): string
    {
        return match ($this) {
            self::Aman => 'ok',
            self::Waspada => 'warn',
            self::Offline => 'off',
        };
    }

    public function plotLabel(): string
    {
        return match ($this) {
            self::Aman => 'Status sawah aman',
            self::Waspada => 'Ada yang perlu dicek',
            self::Offline => 'Sensor tidak terhubung',
        };
    }

    private function severity(): int
    {
        return match ($this) {
            self::Aman => 0,
            self::Offline => 1,
            self::Waspada => 2,
        };
    }

    /** Status terburuk dari sekumpulan status; kosong dianggap Offline. */
    public static function worst(iterable $statuses): self
    {
        return collect($statuses)->sortByDesc(fn (self $s) => $s->severity())->first() ?? self::Offline;
    }
}
