<?php

namespace App\Services;

use App\Models\Lahan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/** Prakiraan cuaca dari API publik BMKG, di-cache agar tidak melewati batas request. */
class WeatherService
{
    public function forLahan(Lahan $lahan): ?array
    {
        if (blank($lahan->kode_wilayah)) {
            return null;
        }

        try {
            $json = $this->fetch($lahan->kode_wilayah);

            return $json ? $this->summarize($json) : null;
        } catch (Throwable $e) {
            Log::warning('Ringkasan cuaca BMKG gagal: '.$e->getMessage(), ['adm4' => $lahan->kode_wilayah]);

            return null;
        }
    }

    private function fetch(string $adm4): ?array
    {
        $key = "bmkg:forecast:{$adm4}";

        if ($cached = Cache::get($key)) {
            return $cached;
        }

        // Baru saja gagal? Jangan menembak API terus-menerus; pakai data lama bila ada.
        if (Cache::has("{$key}:failed")) {
            return Cache::get("{$key}:stale");
        }

        try {
            $json = Http::baseUrl(config('sawah.weather.base_url'))
                ->timeout(config('sawah.weather.timeout'))
                ->acceptJson()
                ->retry(1, 300, throw: false)
                ->get('/prakiraan-cuaca', ['adm4' => $adm4])
                ->throw()
                ->json();

            if (! isset($json['data'][0]['cuaca'])) {
                throw new \UnexpectedValueException('Struktur data BMKG tidak dikenali.');
            }

            Cache::put($key, $json, now()->addMinutes(config('sawah.weather.cache_minutes')));
            Cache::put("{$key}:stale", $json, now()->addDay());

            return $json;
        } catch (Throwable $e) {
            Log::warning('Ambil data BMKG gagal: '.$e->getMessage(), ['adm4' => $adm4]);
            Cache::put("{$key}:failed", true, now()->addMinutes(2));

            return Cache::get("{$key}:stale");
        }
    }

    private function summarize(array $json): ?array
    {
        $tz = $json['lokasi']['timezone'] ?? null;
        $tz = in_array($tz, timezone_identifiers_list(), true) ? $tz : config('app.timezone');
        $now = now($tz);

        $slots = collect($json['data'][0]['cuaca'])
            ->collapse()                                  // hari → daftar slot 3-jam
            ->filter(fn ($s) => isset($s['local_datetime'], $s['weather_desc'], $s['t']))
            ->map(fn ($s) => $s + ['at' => Carbon::parse($s['local_datetime'], $tz)])
            ->sortBy('at')
            ->values();

        if ($slots->isEmpty()) {
            return null;
        }

        $current = $slots->last(fn ($s) => $s['at']->lte($now)) ?? $slots->first();

        // Slot hujan pertama dari sekarang sampai akhir hari ini.
        $rain = $slots
            ->filter(fn ($s) => $s['at']->gte($current['at']) && $s['at']->isSameDay($now))
            ->first(fn ($s) => $this->isRain($s['weather_desc']));

        return [
            'temp' => round((float) $current['t']).'°C',
            'condition' => $current['weather_desc'],
            'icon' => $this->icon($current['weather_desc']),
            'rain' => $rain
                ? $rain['weather_desc'].' · '.$rain['at']->format('H.i')
                : 'Tidak ada hujan diperkirakan hari ini',
            'note' => $rain
                ? 'Pertimbangkan hujan sekitar pukul '.$rain['at']->format('H.i').'. Tetap periksa kelembapan tanah sebelum memutuskan pengairan.'
                : 'Belum ada hujan diperkirakan hari ini. Tetap periksa kelembapan tanah sebelum memutuskan pengairan.',
        ];
    }

    private function isRain(string $desc): bool
    {
        return stripos($desc, 'hujan') !== false || stripos($desc, 'petir') !== false;
    }

    private function icon(string $desc): string
    {
        return match (true) {
            $this->isRain($desc) => 'cloud-rain',
            stripos($desc, 'cerah berawan') !== false => 'cloud-sun',
            stripos($desc, 'cerah') !== false => 'sun',
            default => 'cloud',
        };
    }
}
