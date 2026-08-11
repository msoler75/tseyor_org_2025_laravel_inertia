<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PatchZiggyCache extends Command
{
    protected $signature = 'ziggy:patch-cache';

    protected $description = 'Apply file-based route cache to Ziggy for cross-request performance';

    private const MARKER = 'el archivo ziggy se guarda en cache';

    private const CACHE_BLOCK = <<<'PHP'
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
PHP;

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

        // Try Ziggy v1 pattern: if (! static::$cache) { static::$cache = $this->nameKeyedRoutes(); }
        $v1pattern = '/(if\s*\(\s*!\s*static\s*::\s*\$cache\s*\)\s*\{)\s*static\s*::\s*\$cache\s*=\s*\$this->nameKeyedRoutes\s*\(\s*\)\s*;\s*(\})/s';
        $v1replacement = '$1'."\n".self::CACHE_BLOCK."\n".'$2';

        $patched = preg_replace($v1pattern, $v1replacement, $content, -1, $count);

        if ($count === 1) {
            file_put_contents($path, $patched);
            $this->info('[Ziggy v1] File-based route cache applied successfully.');

            return self::SUCCESS;
        }

        // Try Ziggy v2 pattern: $this->routes = static::$cache ??= $this->nameKeyedRoutes();
        $v2pattern = '/(\$this->routes\s*=\s*static\s*::\s*\$cache\s*\?\?=\s*\$this->nameKeyedRoutes\s*\(\s*\)\s*;)/';
        $v2replacement = <<<'PHP'
if (!static::$cache) {
CACHE_BLOCK
        }
        $this->routes = static::$cache;
PHP;
        $v2replacement = str_replace('CACHE_BLOCK', self::CACHE_BLOCK, $v2replacement);

        $patched = preg_replace($v2pattern, $v2replacement, $content, -1, $count);

        if ($count === 1) {
            file_put_contents($path, $patched);
            $this->info('[Ziggy v2] File-based route cache applied successfully.');

            return self::SUCCESS;
        }

        $this->error('[Ziggy] Could not identify the constructor cache block. Ziggy version may be unsupported.');

        return self::FAILURE;
    }
}
