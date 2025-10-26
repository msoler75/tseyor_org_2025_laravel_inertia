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

    public static function checkAllowedIP(): void
    {
        $clientIP = \Illuminate\Support\Facades\Request::ip();
        $allowedIPs = self::getAllowedIPs();
        if (!in_array($clientIP, $allowedIPs)) {
            Log::channel('deploy')->warning("IP no autorizada para deploy", ['ip' => $clientIP, 'allowed' => $allowedIPs]);
            throw new Exception("IP no autorizada para deploy: {$clientIP}");
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
        array $headers = [],
        array $extraPost = []
    ): array {
        if (!File::exists($zipPath)) {
            throw new Exception("Archivo ZIP no encontrado: $zipPath");
        }

        $ch = curl_init();

        // Construir campos POST, permitiendo añadir valores extra (por ejemplo 'prepare')
        $postFields = array_merge([
            'file' => new \CURLFile($zipPath, 'application/zip', $fileName)
        ], $extraPost);

        $defaultHeaders = ['Content-Type: multipart/form-data'];

        // Añadir token de seguridad a la cabecera
        $deployToken = config('deploy.deploy_token');
        if ($deployToken) {
            $defaultHeaders[] = 'X-Deploy-Token: ' . $deployToken;
        }
        $mergedHeaders = array_merge($defaultHeaders, $headers);

        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $mergedHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5
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

    /**
     * Envía una solicitud de rollback al endpoint especificado
     */
    public static function sendRollbackRequest(string $endpoint): array
    {
        $ch = curl_init();

        $postFields = [
            'rollback' => 'true'
        ];

        $defaultHeaders = ['Content-Type: application/x-www-form-urlencoded'];

        // Añadir token de seguridad a la cabecera
        $deployToken = config('deploy.deploy_token');
        if ($deployToken) {
            $defaultHeaders[] = 'X-Deploy-Token: ' . $deployToken;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postFields),
            CURLOPT_HTTPHEADER => $defaultHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5
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

    /**
     * Mueve un archivo ZIP guardado a la carpeta storage/install creando la carpeta si es necesario.
     * Devuelve la ruta destino completa.
     */
    public static function moveZipToInstall(string $zipPath): string
    {
        $installDir = storage_path('install');
        if (!File::isDirectory($installDir)) {
            File::makeDirectory($installDir, 0755, true, true);
        }

        $dest = $installDir . DIRECTORY_SEPARATOR . basename($zipPath);

        // Si ya existe, sobrescribir
        if (File::exists($dest)) {
            File::delete($dest);
        }

        // Intentar mover el archivo. Si falla, lanzar excepción para que el controlador la maneje.
        if (!File::move($zipPath, $dest)) {
            throw new Exception("No se pudo mover el ZIP a: {$dest}");
        }

        Log::channel('deploy')->info("Archivo preparado movido a: {$dest}");
        return $dest;
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

    /**
     * Valida el contenido del ZIP antes de extraerlo para prevenir path traversal y archivos no permitidos.
     * @param string $zipPath Ruta al archivo ZIP
     * @param string $type Tipo de despliegue: 'public_build', 'ssr', 'node_modules'
     * @throws Exception Si el contenido no es válido
     */
    public static function validateZipContent(string $zipPath, string $type): void
    {
        if (!file_exists($zipPath)) {
            throw new Exception("El archivo ZIP no existe: {$zipPath}");
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== TRUE) {
            throw new Exception("Error al abrir el archivo ZIP para validación: {$zipPath}");
        }

        // Definir reglas por tipo
        $rules = self::getValidationRules($type);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);

            // Verificar path traversal
            if (strpos($entry, '..') !== false || strpos($entry, '../') !== false) {
                $zip->close();
                throw new Exception("Path traversal detectado en entrada ZIP: {$entry}");
            }

            // Verificar si la entrada está en whitelist de paths
            $allowed = false;
            foreach ($rules['allowedPaths'] as $path) {
                if (strpos($entry, $path) === 0) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed) {
                $zip->close();
                throw new Exception("Path no permitido en ZIP: {$entry} (tipo: {$type})");
            }

            // Verificar extensión si no es directorio
            if (!str_ends_with($entry, '/')) {
                $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                if (!in_array($extension, $rules['allowedExtensions'])) {
                    $zip->close();
                    throw new Exception("Extensión no permitida en ZIP: {$extension} para {$entry} (tipo: {$type})");
                }
            }
        }

        $zip->close();
    }

    /**
     * Devuelve las reglas de validación para cada tipo de despliegue.
     */
    private static function getValidationRules(string $type): array
    {
        switch ($type) {
            case 'public_build':
                return [
                    'allowedPaths' => ['assets/', 'images/', 'favicon.ico', 'manifest.json', 'robots.txt', 'sitemap.xml'],
                    'allowedExtensions' => ['js', 'css', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'json', 'txt', 'xml', 'map']
                ];
            case 'ssr':
                return [
                    'allowedPaths' => ['ssr.js', 'ssr-manifest.json'],
                    'allowedExtensions' => ['js', 'json']
                ];
            case 'node_modules':
                return [
                    'allowedPaths' => ['node_modules/'],
                    'allowedExtensions' => ['js', 'json', 'map', 'css', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'md', 'txt', 'lock', 'ts', 'd.ts']
                ];
            default:
                throw new Exception("Tipo de despliegue no reconocido: {$type}");
        }
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

    public static function cleanOldNodeModulesBackups(int $maxBackups = 3): void
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


// INSTALACIONES



     /**
     * Instala un ZIP de public/build: extrae en build_temp, aplica reemplazos,
     * mueve build a build_old y build_temp a build.
     * Lanza excepciones en caso de error.
     * @param string $zipPath
     * @return void
     * @throws \Exception
     */
    public static function installPublicBuildFromZip(string $zipPath): void
    {
        // Validar contenido del ZIP antes de cualquier operación
        self::validateZipContent($zipPath, 'public_build');

        $buildPath = public_path('build');
        $buildTempPath = public_path('build_temp');
        $buildOldPath = public_path('build_old');

        Log::channel('deploy')->info("Paths: ", ['buildPath' => $buildPath, 'buildTempPath' => $buildTempPath, 'buildOldPath' => $buildOldPath]);

        if (File::isDirectory($buildTempPath)) {
            File::deleteDirectory($buildTempPath);
        }

        self::extractZip($zipPath, $buildTempPath);

        // obtener host de la llamada
        $host = request()->getSchemeAndHttpHost();

        $files = self::doReplacements('public/build_temp/assets/*.js', 'http://localhost', $host);
        if (count($files)) {
            Log::channel('deploy')->info('Archivos actualizados: ', $files);
        }

        if (File::isDirectory($buildOldPath)) {
            Log::channel('deploy')->info('Borramos carpeta ' . $buildOldPath);
            File::deleteDirectory($buildOldPath);
        }

        if (File::isDirectory($buildPath)) {
            Log::channel('deploy')->info('Renombramos ' . $buildPath . ' a ' . $buildOldPath);
            File::move($buildPath, $buildOldPath);
        }

        Log::channel('deploy')->info('Renombramos ' . $buildTempPath . ' a ' . $buildPath);
        File::move($buildTempPath, $buildPath);
    }

    /**
     * Rollback del build público: renombra build a build_new y build_old a build
     */
    public static function rollbackPublicBuild(): void
    {
        $buildPath = public_path('build');
        $buildOldPath = public_path('build_old');
        $buildNewPath = public_path('build_new');

        Log::channel('deploy')->info("Rollback paths: ", ['buildPath' => $buildPath, 'buildOldPath' => $buildOldPath, 'buildNewPath' => $buildNewPath]);

        if (File::isDirectory($buildNewPath)) {
            Log::channel('deploy')->info('Borramos carpeta ' . $buildNewPath);
            File::deleteDirectory($buildNewPath);
        }

        if (File::isDirectory($buildPath)) {
            Log::channel('deploy')->info('Renombramos ' . $buildPath . ' a ' . $buildNewPath);
            File::move($buildPath, $buildNewPath);
        }

        if (File::isDirectory($buildOldPath)) {
            Log::channel('deploy')->info('Renombramos ' . $buildOldPath . ' a ' . $buildPath);
            File::move($buildOldPath, $buildPath);
        } else {
            throw new Exception('No hay build_old para rollback');
        }
    }


    /**
     * Instala un ZIP de SSR: hace backup de ssr.js, extrae y aplica reemplazos.
     * @param string $zipPath
     * @return void
     * @throws \Exception
     */
    public static function installSSRFromZip(string $zipPath): void
    {
        // Validar contenido del ZIP antes de cualquier operación
        self::validateZipContent($zipPath, 'ssr');

        // Realizar backup solo del archivo ssr.js si existe
        $ssrFile = base_path('bootstrap/ssr/ssr.js');
        $ssrBackupFile = base_path('bootstrap/ssr/ssr.js.bak');
        if (File::exists($ssrBackupFile)) {
            File::delete($ssrBackupFile);
        }
        if (File::exists($ssrFile)) {
            Log::channel('deploy')->info('Backup de ' . $ssrFile . ' a ' . $ssrBackupFile);
            File::copy($ssrFile, $ssrBackupFile);
        }

        $extractPath = base_path('bootstrap/ssr');

        // recrea la carpeta bootstrap/ssr
        File::deleteDirectory($extractPath);
        File::makeDirectory($extractPath, 0755, true, true);

        self::extractZip($zipPath, $extractPath);

        // obtener host
        $host = request()->getSchemeAndHttpHost();

        $files = self::doReplacements('bootstrap/ssr/ssr.js', 'http://localhost', $host);
        if (count($files)) {
            Log::channel('deploy')->info('Archivos actualizados: ', $files);
        }

        if (!(File::exists($extractPath . '/ssr.js') && File::exists($extractPath . '/ssr-manifest.json'))) {
            throw new \Exception('El archivo .zip no contiene los archivos SSR necesarios');
        }
    }


    /**
     * Instala node_modules desde un ZIP: hace backup, limpia node_modules, extrae y limpia.
     * @param string $zipPath
     * @return void
     * @throws \Exception
     */
    public static function installNodeModulesFromZip(string $zipPath): void
    {
        // Validar contenido del ZIP antes de cualquier operación
        self::validateZipContent($zipPath, 'node_modules');

        // 1. Crear backup de node_modules existente
        $backupPath = self::backupNodeModules();

        // 2. Limpiar node_modules existente
        self::cleanNodeModules();

        // 3. Descomprimir
        self::extractZip($zipPath, base_path());

        // 4. Limpieza post-instalación
        self::postInstallCleanup();

        // 5. Limpieza de backups antiguos
        self::cleanOldNodeModulesBackups();
    }


    // instalación de todo desde los archivos en la carpeta storage/install

    /**
     * Busca en storage/install los zips más recientes para public_build, ssr y node_modules
     * y ejecuta las instalaciones llamando a los métodos correspondientes.
     * Devuelve un array con el resultado por cada elemento.
     *
     * @return array
     */
    public static function installAllFromStorageInstall(): array
    {
        $installDir = storage_path('install');
        if (!File::isDirectory($installDir)) {
            throw new Exception("Directorio de instalación no encontrado: {$installDir}");
        }

        $patterns = [
            'node_modules' => '*node_modules*.zip',
            'public_build' => '*public_build*.zip',
            'ssr' => '*ssr*.zip',
        ];

        $results = [];

        // Primera pasada: encontrar el archivo más reciente para cada patrón
        $latestFiles = [];
        foreach ($patterns as $key => $pattern) {
            $files = File::glob($installDir . DIRECTORY_SEPARATOR . $pattern) ?: [];
            if (empty($files)) {
                $latestFiles[$key] = null;
            } else {
                usort($files, function ($a, $b) {
                    return filemtime($b) <=> filemtime($a);
                });
                $latestFiles[$key] = $files[0];
            }
        }

    // Si falta alguno, no ejecutar ninguna instalación
    $missing = array_filter($latestFiles, fn($v) => $v === null);
        if (!empty($missing)) {
            foreach ($latestFiles as $k => $v) {
                if ($v === null) {
                    Log::channel('deploy')->warning("No se encontró zip para {$k} en {$installDir}");
                    $results[$k] = ['found' => false, 'file' => null, 'status' => 'skipped', 'message' => 'Missing file - all installations skipped'];
                } else {
                    $results[$k] = ['found' => true, 'file' => $v, 'status' => 'skipped', 'message' => 'Present but skipped because another is missing'];
                }
            }

            Log::channel('deploy')->warning('Faltan uno o más zips en storage/install; no se ejecutará ninguna instalación.');
            return $results;
        }

        // Comprobar que los archivos encontrados sean recientes (últimas 24 horas)
        foreach ($latestFiles as $k => $filePath) {
            if (!file_exists($filePath)) {
                Log::channel('deploy')->error("Archivo no existe al comprobar antigüedad: {$filePath}");
                throw new Exception("Archivo no existe al comprobar antigüedad: {$filePath}");
            }

            $mtime = filemtime($filePath);
            if ($mtime === false) {
                Log::channel('deploy')->error("No se pudo obtener mtime de: {$filePath}");
                throw new Exception("No se pudo obtener mtime de: {$filePath}");
            }

            // Comprobar contra el umbral configurable en config/deploy.php
            $maxAge = config('deploy.max_age_seconds', 86400);
            if ((time() - $mtime) > $maxAge) {
                Log::channel('deploy')->error("El ZIP para {$k} supera el umbral de edad ({$maxAge}s): {$filePath}");
                throw new Exception("El ZIP para {$k} supera el umbral de edad ({$maxAge}s): {$filePath}");
            }
        }
        // Intentar adquirir lock para evitar concurrencia (archivo simple con timestamp)
        $lockFile = $installDir . DIRECTORY_SEPARATOR . '.install_lock';
        $now = time();
        $lockAcquired = false;

        if (File::exists($lockFile)) {
            $lockTs = (int) File::get($lockFile);
            // si el lock es antiguo (>5 minutos) lo consideramos expirado
            if ($now - $lockTs > 300) {
                File::delete($lockFile);
            } else {
                throw new Exception('Otra instalación está en curso. Intenta más tarde.');
            }
        }

        // crear lock
        File::put($lockFile, (string) $now);
        $lockAcquired = true;

        // Todos los archivos existen: ejecutar instalaciones en orden
        $processedFiles = [];
        foreach ($latestFiles as $key => $latest) {
            $results[$key] = ['found' => true, 'file' => $latest, 'status' => 'pending'];
            try {
                switch ($key) {
                    case 'public_build':
                        self::installPublicBuildFromZip($latest);
                        break;
                    case 'ssr':
                        self::installSSRFromZip($latest);
                        break;
                    case 'node_modules':
                        self::installNodeModulesFromZip($latest);
                        break;
                }
                $results[$key]['status'] = 'ok';
                $results[$key]['message'] = 'Installed successfully';
                Log::channel('deploy')->info("Instalación exitosa para {$key} desde {$latest}");

                // almacenar lista de archivos que se instalaron correctamente
                $processedFiles[] = $latest;
            } catch (Exception $e) {
                $results[$key]['status'] = 'error';
                $results[$key]['message'] = $e->getMessage();
                Log::channel('deploy')->error("Error instalando {$key} desde {$latest}: " . $e->getMessage());
            }
        }

        // Si todas las instalaciones fueron exitosas, renombrar los zips procesados a .installed
        $hadErrors = false;
        foreach ($results as $r) {
            if (isset($r['status']) && $r['status'] === 'error') {
                $hadErrors = true;
                break;
            }
        }
        if (!$hadErrors) {
            foreach ($processedFiles as $file) {
                try {
                    $installedName = $file . '.installed';
                    if (File::exists($file)) {
                        File::move($file, $installedName);
                        Log::channel('deploy')->info("Archivo procesado renombrado a: {$installedName}");
                    }
                } catch (Exception $e) {
                    Log::channel('deploy')->error("Error renombrando archivo procesado {$file}: " . $e->getMessage());
                    // No change to results statuses — el instalador ya terminó OK;
                    // solo informamos del fallo del renombrado.
                }
            }
        } else {
            Log::channel('deploy')->warning('No se renombraron los zips a .installed porque hubo errores en la instalación.');
        }

        // liberar lock
        if ($lockAcquired && File::exists($lockFile)) {
            File::delete($lockFile);
        }

        return $results;
    }

    public static function getAllowedIPs(): array
    {
        $filePath = storage_path('admin/allowed_ips.json');

        if (!file_exists($filePath)) {
            return [];
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        return $data['allowed_ips'] ?? [];
    }

    public static function addAllowedIP(string $ip, string $updatedBy = 'DeployHelper'): void
    {
        $filePath = storage_path('admin/allowed_ips.json');

        // Asegurar que el directorio existe
        if (!is_dir(dirname($filePath))) {
            @mkdir(dirname($filePath), 0755, true);
        }

        $data = [
            'allowed_ips' => [],
            'last_updated' => now()->toISOString(),
            'updated_by' => $updatedBy
        ];

        if (file_exists($filePath)) {
            $json = file_get_contents($filePath);
            $temp = json_decode($json, true);
            if (is_array($temp)) {
                $data = array_merge($data, $temp);
            }
        }

        if (!in_array($ip, $data['allowed_ips'])) {
            $data['allowed_ips'][] = $ip;
            $data['last_updated'] = now()->toISOString();
            $data['updated_by'] = $updatedBy;

            $saved = file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
            if ($saved === false) {
                Log::error('Failed to save allowed_ips.json from addAllowedIP', ['file' => $filePath, 'ip' => $ip]);
            }
        }
    }
}
