<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PatchZiggyCache extends Command
{
    protected $signature = 'ziggy:patch-cache';
    protected $description = 'Apply file-based route cache to Ziggy for cross-request performance';

    private const MARKER = 'el archivo ziggy se guarda en cache';

    public function handle(): int
    {
        $path = base_path('vendor/tightenco/ziggy/src/Ziggy.php');

        if (! file_exists($path)) {
            $this->error("Ziggy source not found at: $path");
            return self::FAILURE;
        }

        $content = file_get_contents($path);

        if (str_contains($content, self::MARKER)) {
            $this->info('[Ziggy] File-based route cache already applied.');
            return self::SUCCESS;
        }

        $patched = preg_replace(
            '/(if\s*\(\s*!\s*static\s*::\s*\$cache\s*\)\s*\{)\s*static\s*::\s*\$cache\s*=\s*\$this->nameKeyedRoutes\s*\(\s*\)\s*;\s*(\})/s',
            <<<'PHP'
$1
            // el archivo ziggy se guarda en cache, aquí se comprueba si debe reconstruirse
            $cache_routes = base_path("bootstrap/cache/routes-v7.php");
            $cache_ziggy = base_path("bootstrap/cache/ziggy2.json");
            if (
                !file_exists($cache_ziggy) ||
                !file_exists($cache_routes) ||
                filemtime($cache_routes) > filemtime($cache_ziggy)
            ) {
                static::$cache = $this->nameKeyedRoutes();
                file_put_contents($cache_ziggy, static::$cache->toJson());
            } else {
                try {
                    $ziggy_content = file_get_contents($cache_ziggy);
                    static::$cache = collect(json_decode($ziggy_content, true));
                } catch (\Exception $e) {
                    static::$cache = $this->nameKeyedRoutes(); // por si hubiera algun error
                }
            }
$2
PHP,
            $content,
            -1,
            $count
        );

        if ($count !== 1) {
            $this->error("[Ziggy] Could not identify the constructor cache block. Expected 1 match, found {$count}.");
            return self::FAILURE;
        }

        file_put_contents($path, $patched);
        $this->info('[Ziggy] File-based route cache applied successfully.');
        return self::SUCCESS;
    }
}
