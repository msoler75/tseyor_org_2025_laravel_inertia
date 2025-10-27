<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PWALogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pwa:logs {--clear : Limpiar los logs} {--tail : Mostrar solo los últimos logs} {--lines=20 : Número de líneas a mostrar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostrar o gestionar logs de PWA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logFile = storage_path('logs/pwa-debug.log');

        if ($this->option('clear')) {
            if (file_exists($logFile)) {
                unlink($logFile);
                $this->info('✅ Logs de PWA limpiados');
            } else {
                $this->info('No hay logs para limpiar');
            }
            return;
        }

        if (!file_exists($logFile)) {
            $this->warn('No existe el archivo de logs de PWA');
            $this->info('Ruta: ' . $logFile);
            return;
        }

        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($lines)) {
            $this->info('No hay logs en el archivo');
            return;
        }

        $this->info('📄 Logs de PWA (' . count($lines) . ' entradas)');
        $this->info('📁 Archivo: ' . $logFile);
        $this->info('📏 Tamaño: ' . $this->formatBytes(filesize($logFile)));
        $this->info('');

        // Mostrar logs
        $logsToShow = $lines;

        if ($this->option('tail')) {
            $numLines = (int) $this->option('lines');
            $logsToShow = array_slice($lines, -$numLines);
            $this->info("Mostrando los últimos {$numLines} logs:");
        }

        foreach ($logsToShow as $line) {
            // Parsear la línea del log
            if (preg_match('/\[([^\]]+)\] \[([^\]]+)\] (.+) - URL: ([^ ]+) - PWA: ([^ ]+) - Data: (.+)/', $line, $matches)) {
                $timestamp = $matches[1];
                $level = $matches[2];
                $message = $matches[3];
                $url = $matches[4];
                $isPwa = $matches[5];
                $data = $matches[6];

                // Color del nivel
                $color = match($level) {
                    'ERROR' => 'red',
                    'WARN' => 'yellow',
                    'SUCCESS' => 'green',
                    default => 'blue'
                };

                $this->line("<fg={$color}>[{$timestamp}] [{$level}]</> {$message}");

                if ($url !== 'unknown') {
                    $this->line("  <fg=gray>URL:</> {$url}");
                }

                if ($isPwa === 'YES') {
                    $this->line("  <fg=magenta>PWA: Sí</>");
                }

                if ($data !== 'null') {
                    $this->line("  <fg=gray>Data:</> {$data}");
                }

                $this->line('');
            } else {
                $this->line($line);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Formatear bytes a formato legible
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
