<?php

namespace App\Pigmalion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use ZipArchive;
use Exception;

class DeployHelper
{

    // parte cliente (genera el zip y lo envia)


    public static function validateDirectoryExists(string $path): void
    {
        if (!File::exists($path)) {
            throw new Exception("Directorio no existe: $path");
        }
    }


    public static function createZipFile(
        string $sourcePath,
        string $zipPath,
        array $exclusions = [],
        string $basePathPrefix = ''
    ): bool {
        if (!File::isDirectory($sourcePath)) {
            throw new Exception("Directorio fuente no existe: $sourcePath");
        }

        File::delete($zipPath);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new Exception("Error al crear archivo ZIP: $zipPath");
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $sourcePath,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;

            // Obtener ruta relativa usando el iterador
            $relativePath = $iterator->getSubPathName();

            // Normalizar a rutas Unix
            $relativePath = str_replace('\\', '/', $relativePath);

            // Aplicar prefijo base si existe
            $zipPathEntry = $basePathPrefix
                ? $basePathPrefix . '/' . $relativePath
                : $relativePath;

            if (self::shouldExclude($file->getRealPath(), $exclusions)) continue;

            $zip->addFile($file->getRealPath(), $zipPathEntry);
        }

        return $zip->close();
    }


    private static function shouldExclude(string $path, array $exclusions)
    {
        $path = str_replace('\\', '/', $path);

        foreach ($exclusions as $pattern) {
            if (str_contains($path, $pattern)) return true;
            if (fnmatch($pattern, basename($path))) return true;
        }

        return false;
    }



    public static function sendZipFile(
        string $zipPath,
        string $endpoint,
        string $fileName,
        array $headers = []
    ): array {
        if (!File::exists($zipPath)) {
            throw new Exception("Archivo ZIP no encontrado: $zipPath");
        }

        $ch = curl_init();
        $postFields = [
            'file' => new \CURLFile($zipPath, 'application/zip', $fileName)
        ];

        $defaultHeaders = ['Content-Type: multipart/form-data'];
        $mergedHeaders = array_merge($defaultHeaders, $headers);

        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $mergedHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'success' => $httpCode == 200,
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error
        ];
    }


    public static function handleResponse(array $result, Command $command): void
    {
        if ($result['success']) {
            $command->info('Envío exitoso: ' . $result['response']);
        } else {
            $command->error("Error {$result['http_code']}: " . $result['response']);
            if ($result['error']) {
                $command->error("CURL Error: {$result['error']}");
            }
        }
    }


    // parte servidor (recibe el zip y lo extrae)


    public static function storeUploadedFile(Request $request, string $prefix): string
    {
        $zipPath = storage_path("app/temp/tmp_" . $prefix . "_" . now()->timestamp . '.zip');
        $request->file('file')->move(dirname($zipPath), basename($zipPath));

        if (!file_exists($zipPath)) {
            throw new Exception("Error al guardar el archivo ZIP");
        }

        return $zipPath;
    }
    public static function backupNodeModules(): string
    {
        $nodeModulesPath = base_path('node_modules');
        $backupPath = self::generateUniqueBackupPath();

        if (!File::isDirectory($nodeModulesPath)) {
            Log::channel('deploy')->info("No existe node_modules para hacer backup.");
            return '';
        }

        /* if (self::renameDirectoryUsingOS($nodeModulesPath, $backupPath)) {
            Log::channel('deploy')->info("Backup de node_modules creado en: {$backupPath}");
            return $backupPath;
        } */

        if (File::move($nodeModulesPath, $backupPath)) {
            Log::channel('deploy')->info("Backup de node_modules creado en: {$backupPath}");
            return $backupPath;
        }

        Log::channel('deploy')->warning("Renombrado falló, intentando copiar node_modules.");
        // self::copyDirectoryUsingOS($nodeModulesPath, $backupPath);

        if (File::copyDirectory($nodeModulesPath, $backupPath)) {
            Log::channel('deploy')->info("Backup de node_modules creado por copia en: {$backupPath}");
            return $backupPath;
        }

        /*if (File::isDirectory($backupPath)) {
            Log::channel('deploy')->info("Backup de node_modules creado por copia en: {$backupPath}");
            return $backupPath;
        }*/

        Log::channel('deploy')->error("No se pudo crear el backup de node_modules.");
        return '';
    }

    public static function cleanNodeModules(): void
    {
        $nodeModulesPath = base_path('node_modules');

        if (File::isDirectory($nodeModulesPath)) {
            File::deleteDirectory($nodeModulesPath);
            Log::channel('deploy')->info("Directorio node_modules eliminado.");
        } else {
            Log::channel('deploy')->info("No existe node_modules para limpiar.");
        }
    }


    private static function executeCommand(string $command): bool
    {
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            Log::channel('deploy')->error("Error ejecutando comando: {$command}");
            Log::channel('deploy')->error("Output: " . implode("\n", $output));
            return false;
        }

        return true;
    }
    public static function renameDirectoryUsingOS(string $oldPath, string $newPath): bool
    {
        $oldPath = escapeshellarg($oldPath);
        $newPath = escapeshellarg($newPath);

        $command = PHP_OS_FAMILY === 'Windows'
            ? "rename {$oldPath} {$newPath}"
            : "mv -f {$oldPath} {$newPath}";

        return self::executeCommand($command) && File::isDirectory($newPath);
    }

    public static function copyDirectoryUsingOS(string $source, string $destination): void
    {
        $source = escapeshellarg($source);
        $destination = escapeshellarg($destination);

        $command = PHP_OS_FAMILY === 'Windows'
            ? "xcopy /E /I /Q {$source} {$destination}"
            : "cp -R {$source} {$destination}";

        if (!self::executeCommand($command)) {
            throw new Exception("Error al copiar directorio: {$source} a {$destination}");
        }
    }



    public static function extractZip(string $zipPath, string $extractPath): void
    {
        set_time_limit(300); // 5 minutos para operaciones largas

        if (!file_exists($zipPath)) {
            throw new Exception("El archivo ZIP no existe: {$zipPath}");
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== TRUE) {
            throw new Exception("Error al abrir el archivo ZIP: {$zipPath}");
        }

        if (!$zip->extractTo($extractPath)) {
            throw new Exception("Error en la extracción del ZIP al directorio: {$extractPath}");
        }

        $zip->close();
    }


    public static function postInstallCleanup(): void
    {
        // Limpiar cache de Vite
        File::deleteDirectory(base_path('.vite'));

        // Reconstruir dependencias si es necesario
        /*if (config('app.node_modules_post_install')) {
            exec('npm rebuild 2>&1', $output, $exitCode);
            if ($exitCode !== 0) {
                throw new Exception("Error en npm rebuild: " . implode("\n", $output));
            }
        }*/
    }


    public static function cleanTempFiles($zipPath = null)
    {
        if ($zipPath && File::exists($zipPath)) {
            File::delete($zipPath);
        }

        array_map('unlink', glob(sys_get_temp_dir() . '/php*'));
    }


    public static function generateUniqueBackupPath(): string
    {
        $baseBackupPath = base_path('_node_modules_backup');
        $timestamp = now()->format('Ymd_His');
        $backupPath = "{$baseBackupPath}_{$timestamp}";

        // Si ya existe un backup con el mismo nombre, agregar sufijo incremental
        $counter = 1;
        while (File::exists($backupPath)) {
            $backupPath = "{$baseBackupPath}_{$timestamp}_{$counter}";
            $counter++;
        }

        return $backupPath;
    }

    public static function cleanOldBackups(int $maxBackups = 3): void
    {
        $backups = File::glob(base_path('_node_modules_backup_*'));
        usort($backups, fn($a, $b) => filemtime($b) <=> filemtime($a));

        foreach (array_slice($backups, $maxBackups) as $backup) {
            File::deleteDirectory($backup);
            Log::channel('deploy')->info("Backup antiguo eliminado: {$backup}");
        }
    }


    /**
     * Reemplaza algunas cadenas de texto en los archivos recibidos
     */
    public static function doReplacements(string $source, string $from, string $to)
    {
        $files_changed = [];

        // Buscar archivos usando wildcard opcional
        $files = self::resolveFilePaths($source);

        foreach ($files as $filePath) {
            if (!File::exists($filePath)) {
                throw new Exception("El archivo no existe en la ruta: {$filePath}");
            }

            $files_changed[] = $filePath;

            $content = File::get($filePath);

            // primero mira si existe la cadena buscada $from  en $content
            if(str_contains(    $content, $from)) {
                $updatedContent = str_replace($from, $to, $content);
                File::put($filePath, $updatedContent);
            }
        }

        return $files_changed;
    }

    private static function resolveFilePaths(string $sourcePattern): array
    {
        $fullPath = base_path($sourcePattern);

        // Si contiene wildcard (*)
        if (str_contains($sourcePattern, '*')) {
            $foundFiles = glob($fullPath, GLOB_BRACE);
            return array_filter($foundFiles, 'is_file');
        }

        // Si es ruta normal
        return [base_path($sourcePattern)];
    }
}
