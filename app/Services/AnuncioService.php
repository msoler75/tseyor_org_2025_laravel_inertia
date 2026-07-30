<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AnuncioService
{
    const CACHE_KEY = 'anuncio_banner_cache';
    const CACHE_TTL = 3600;

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function getBannerData(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $anuncio = Setting::where('name', 'anuncio')->value('value');

            $setting = Setting::where('name', 'aviso_mantenimiento')->first();
            $mantenimiento = $this->computeMantenimiento($setting);

            return [
                'anuncio' => $anuncio,
                'mantenimiento' => $mantenimiento,
            ];
        });
    }

    private function computeMantenimiento(?Setting $setting): ?array
    {
        if (!$setting || !$setting->value) {
            return null;
        }

        $data = json_decode($setting->value, true);

        if (!$data || empty($data['inicio'])) {
            return null;
        }

        $ahora = Carbon::now('UTC');
        $inicio = Carbon::parse($data['inicio']);
        $fin = isset($data['fin']) ? Carbon::parse($data['fin']) : null;

        if ($fin && $ahora > $fin) {
            $setting->value = '{}';
            $setting->save();
            return null;
        }

        $data['esta_vigente'] = $inicio <= $ahora;

        return $data;
    }
}
