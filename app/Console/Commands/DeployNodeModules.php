<?php

namespace App\Console\Commands;

define('NODE_MODULES_ENDPOINT', 'https://dev.tseyor.org/_sendnodemodules');

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Pigmalion\DeployHelper as Deploy;
use Exception;

class DeployNodeModules extends Command
{
    protected $signature = 'deploy:nodemodules';
    protected $description = 'Comprime node_modules para que funcione SSR y lo envía al servidor';

    private const SOURCE_DIR = 'node_modules';
    private const ZIP_NAME = 'nodemodules.zip';

    // carpetas no necesarias:
    private const EXCLUSIONS = [
        '/.cache/',
        '/.bin/',
        '/.yarn-integrity',
        '/esbuild/',
        '*.ts',
        '*.md',
        '*.log',
        '/darwin-',
        '/win32-',
        '/vite',
        '/.vite',
        '/.vite-temp',
        '/jiti',
        '/@splidejs',
        '/@lezer',
        '/prosemirror',
        '/unplugin',
        '/dropzone',
        '/@popperjs',
        '/tippy.js',
        '/terser',
        '/daisyui',
        '/@tiptap',
        '/@codemirror',
        '/@rollup',
        '/md-editor-v3',
        '/tailwindcss',
        '/typescript',
        '/caniuse-lite',
        '/@swc',
        '/autoprefixer',
        '/@types',
        '/sucrase',
        '/culori',
        '/tldts-core',
        '/@jridgewell',
        '/@nodelib',
        '/postcss',
    ];

    public function handle()
    {
        try {
            $sourcePath = base_path(self::SOURCE_DIR);
            $zipPath = storage_path('app/' . self::ZIP_NAME);

            Deploy::validateDirectoryExists($sourcePath);

            $this->info('Creando zip...');

            if (Deploy::createZipFile(
                $sourcePath,
                $zipPath,
                self::EXCLUSIONS,
                'node_modules'
            )) {
                $this->info('ZIP creado: ' . basename($zipPath));

                $this->info('Enviando...');
                $result = Deploy::sendZipFile(
                    $zipPath,
                    NODE_MODULES_ENDPOINT,
                    self::ZIP_NAME,
                );

                Deploy::handleResponse($result, $this);

                File::delete($zipPath);
            } else {
                $this->error('Error al crear el ZIP');
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
