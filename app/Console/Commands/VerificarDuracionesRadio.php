<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RadioItem;
use App\Pigmalion\StorageItem;
use Illuminate\Support\Facades\Log;

class VerificarDuracionesRadio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'radio:verificar-duraciones
                        {--fix : Corregir duraciones inválidas}
                        {--per-page=100 : Número de elementos por página}
                        {--page=1 : Página a procesar (empieza en 1)}
                        {--all : Actualizar todas las duraciones, no solo las problemáticas}
                        {--id= : Procesar solo el elemento con este ID específico}';    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar y opcionalmente corregir las duraciones de los elementos de radio con paginación';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fix = $this->option('fix');
        $perPage = (int) $this->option('per-page');
        $page = (int) $this->option('page');
        $all = $this->option('all');
        $idEspecifico = $this->option('id');

        // Validar parámetros de paginación
        if ($perPage <= 0) {
            $this->error("El parámetro 'per-page' debe ser mayor que 0");
            return;
        }

        if ($page <= 0) {
            $this->error("El parámetro 'page' debe ser mayor que 0");
            return;
        }

        // Si se especifica un ID, procesar solo ese elemento
        if ($idEspecifico) {
            $elemento = RadioItem::find($idEspecifico);
            if (!$elemento) {
                $this->error("No se encontró elemento con ID: {$idEspecifico}");
                return;
            }

            $this->info("Procesando elemento específico ID: {$idEspecifico}");
            $this->line("  Título: {$elemento->titulo}");
            $this->line("  Duración actual: {$elemento->duracion}");
            $this->line("  URL: {$elemento->url}");

            if ($fix) {
                $nuevaDuracion = $this->calcularDuracion($elemento->url);

                if ($nuevaDuracion > 0 && $nuevaDuracion != $elemento->duracion) {
                    $duracionAnterior = $elemento->duracion;
                    $elemento->update(['duracion' => $nuevaDuracion]);
                    $this->info("  ✓ Duración actualizada: {$duracionAnterior} → {$nuevaDuracion} segundos");
                } else if ($nuevaDuracion > 0) {
                    $this->line("  ✓ Duración correcta: {$nuevaDuracion} segundos");
                } else {
                    $this->error("  ✗ No se pudo calcular duración válida");
                }
            } else {
                $this->info("Modo verificación (sin --fix). No se realizaron cambios.");
            }
            return;
        }

        if ($all) {
            $this->info('Recalculando TODAS las duraciones de elementos de radio...');
        } else {
            $this->info('Verificando duraciones problemáticas de elementos de radio...');
        }

        // Construir query según las opciones
        if ($all) {
            // Obtener todos los elementos de radio
            $query = RadioItem::query();
        } else {
            // Obtener solo elementos con duración problemática
            $query = RadioItem::where(function($q) {
                $q->where('duracion', '<=', 0)
                  ->orWhere('duracion', '>', 86400)
                  ->orWhereNull('duracion');
            });
        }

        // Calcular total de elementos para mostrar información de paginación
        $totalElementos = $query->count();
        $totalPaginas = ceil($totalElementos / $perPage);

        if ($totalElementos == 0) {
            if ($all) {
                $this->info('No se encontraron elementos de radio.');
            } else {
                $this->info('No se encontraron elementos con duraciones problemáticas.');
            }
            return;
        }

        // Mostrar información de paginación
        $this->info("Total de elementos: {$totalElementos}");
        $this->info("Elementos por página: {$perPage}");
        $this->info("Total de páginas: {$totalPaginas}");
        $this->info("Procesando página {$page} de {$totalPaginas}");

        if ($page > $totalPaginas) {
            $this->error("La página {$page} no existe. Hay {$totalPaginas} páginas disponibles.");
            return;
        }

        // Aplicar paginación
        $offset = ($page - 1) * $perPage;
        $elementos = $query->offset($offset)->limit($perPage)->get();

        if ($all) {
            $this->info("Procesando {$elementos->count()} elementos de radio:");
        } else {
            $this->info("Encontrados {$elementos->count()} elementos con duraciones problemáticas:");
        }

        $corregidos = 0;
        $errores = 0;
        $sinCambios = 0;
        $omitidos = 0;

        foreach ($elementos as $elemento) {
            $this->line("ID: {$elemento->id} - {$elemento->titulo}");
            $this->line("  Duración actual: {$elemento->duracion}");
            $this->line("  URL: {$elemento->url}");

            if ($fix) {
                // Verificar si el archivo existe antes de intentar calcular duración
                $archivoExiste = $this->verificarExistenciaArchivo($elemento->url);

                if (!$archivoExiste) {
                    $this->line("  ⚠ Archivo no disponible localmente - omitiendo");
                    $omitidos++;
                } else {
                    try {
                        $nuevaDuracion = $this->calcularDuracion($elemento->url);

                        if ($nuevaDuracion > 0) {
                            $duracionAnterior = $elemento->duracion;
                            $elemento->duracion = $nuevaDuracion;
                            $elemento->save();

                            if ($duracionAnterior != $nuevaDuracion) {
                                $this->info("  ✓ Actualizado de {$duracionAnterior} a {$nuevaDuracion} segundos");
                                $corregidos++;
                            } else {
                                $this->line("  ○ Sin cambios: {$nuevaDuracion} segundos");
                                $sinCambios++;
                            }
                        } else {
                            $this->error("  ✗ No se pudo calcular duración válida");
                            $errores++;
                        }
                    } catch (\Exception $e) {
                        $this->error("  ✗ Error: " . $e->getMessage());
                        $errores++;
                    }
                }
            } else {
                $this->line("  (Usar --fix para corregir)");
            }

            $this->line('');
        }

        if ($fix) {
            $this->info("Proceso completado para página {$page}:");
            $this->info("  - Elementos actualizados: {$corregidos}");
            $this->info("  - Elementos sin cambios: {$sinCambios}");
            $this->info("  - Elementos omitidos (archivo no disponible): {$omitidos}");
            $this->info("  - Elementos con errores: {$errores}");

            if ($page < $totalPaginas) {
                $siguientePagina = $page + 1;
                $this->line("");
                $this->info("Para procesar la siguiente página, ejecuta:");
                $this->line("php artisan radio:verificar-duraciones --fix --page={$siguientePagina} --per-page={$perPage}" . ($all ? " --all" : ""));
            } else {
                $this->line("");
                $this->info("✅ Has completado todas las páginas disponibles.");
            }
        } else {
            if ($all) {
                $this->info("Para recalcular todas las duraciones, ejecuta el comando con --fix --all");
            } else {
                $this->info("Para corregir las duraciones, ejecuta el comando con --fix");
            }
        }
    }

    /**
     * Verificar si el archivo existe antes de intentar procesarlo
     */
    private function verificarExistenciaArchivo($url): bool
    {
        try {
            $mp3File = $this->resolverRutaArchivo($url);
            return $mp3File && file_exists($mp3File);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Calcular duración del audio usando métodos mejorados
     */
    private function calcularDuracion($url): int
    {
        try {
            // Manejar diferentes tipos de URLs
            $mp3File = $this->resolverRutaArchivo($url);

            if (!$mp3File || !file_exists($mp3File)) {
                throw new \Exception("Archivo no encontrado: {$mp3File}");
            }

            $this->line("    Analizando archivo: " . basename($mp3File));

            // Usar SOLO ffprobe (más preciso y confiable)
            $duracionFfprobe = $this->calcularDuracionConFfprobe($mp3File);
            if ($duracionFfprobe > 0) {
                $this->line("    ✓ Duración calculada con ffprobe: {$duracionFfprobe} segundos");
                return $duracionFfprobe;
            }

            // Si ffprobe falla, no hay alternativa confiable
            $this->line("    ⚠ ffprobe no pudo calcular la duración - manteniendo valor actual");

        } catch (\Exception $e) {
            $this->error("    ✗ Error general: " . $e->getMessage());
            Log::error("Error calculando duración", [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
        }

        return 0;
    }

    /**
     * Resolver la ruta física del archivo desde una URL
     */
    private function resolverRutaArchivo($url): ?string
    {
        try {
            // Si es una URL de tseyor.org, extraer solo la parte del path
            if (str_contains($url, 'tseyor.org')) {
                $path = parse_url($url, PHP_URL_PATH);
                if ($path) {
                    $rutaPublica = public_path($path);
                    if (file_exists($rutaPublica)) {
                        $this->line("    Encontrado archivo local: {$rutaPublica}");
                        return $rutaPublica;
                    }
                }
            }

            // Si es una URL completa, usar StorageItem
            if (str_starts_with($url, 'http')) {
                $loc = new StorageItem($url);
                return $loc->getPath();
            }

            // Si es una ruta relativa, construir ruta completa
            if (str_starts_with($url, '/almacen')) {
                $rutaPublica = public_path($url);
                if (file_exists($rutaPublica)) {
                    return $rutaPublica;
                }
            }

            // Intentar con StorageItem de todas formas
            $loc = new StorageItem($url);
            return $loc->getPath();

        } catch (\Exception $e) {
            $this->line("    ⚠ Error resolviendo ruta: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Calcular duración usando ffprobe instalado vía npm
     */
    private function calcularDuracionConFfprobe($mp3File): int
    {
        try {
            // Detectar la ruta de ffprobe según el sistema operativo
            $ffprobePath = $this->detectarRutaFfprobe();

            if (!$ffprobePath) {
                $this->line("    ⚠ ffprobe no encontrado en el sistema");
                return 0;
            }

            // Comando para obtener la duración
            $command = sprintf(
                '"%s" -v quiet -show_entries format=duration -of csv=p=0 "%s"',
                $ffprobePath,
                $mp3File
            );

            $this->line("    Ejecutando: " . $command);

            $output = shell_exec($command);

            if ($output && is_numeric(trim($output))) {
                $duracion = intval(floatval(trim($output)));

                if ($duracion > 0 && $duracion <= 86400) {
                    return $duracion;
                } else {
                    $this->line("    ⚠ Duración fuera de rango: {$duracion}");
                }
            } else {
                $this->line("    ⚠ Output de ffprobe inválido: " . ($output ?: 'vacío'));
            }

        } catch (\Exception $e) {
            $this->line("    ⚠ Error con ffprobe: " . $e->getMessage());
        }

        return 0;
    }

    /**
     * Detectar la ruta de ffprobe según el sistema operativo y entorno
     */
    private function detectarRutaFfprobe(): ?string
    {
        // Rutas candidatas para buscar ffprobe
        $rutasCandidatas = [];

        // Detectar sistema operativo
        $isWindows = PHP_OS_FAMILY === 'Windows';
        $isLinux = PHP_OS_FAMILY === 'Linux';
        $isMac = PHP_OS_FAMILY === 'Darwin';

        // 1. Buscar en node_modules (instalación via npm)
        if ($isWindows) {
            $rutasCandidatas[] = base_path('node_modules/@ffprobe-installer/win32-x64/ffprobe.exe');
            $rutasCandidatas[] = base_path('node_modules/@ffprobe-installer/win32-ia32/ffprobe.exe');
        } elseif ($isLinux) {
            $rutasCandidatas[] = base_path('node_modules/@ffprobe-installer/linux-x64/ffprobe');
            $rutasCandidatas[] = base_path('node_modules/@ffprobe-installer/linux-ia32/ffprobe');
            $rutasCandidatas[] = base_path('node_modules/@ffprobe-installer/linux-arm64/ffprobe');
            $rutasCandidatas[] = base_path('node_modules/@ffprobe-installer/linux-arm/ffprobe');
        } elseif ($isMac) {
            $rutasCandidatas[] = base_path('node_modules/@ffprobe-installer/darwin-x64/ffprobe');
            $rutasCandidatas[] = base_path('node_modules/@ffprobe-installer/darwin-arm64/ffprobe');
        }

        // 2. Buscar en rutas del sistema
        if ($isWindows) {
            // Intentar encontrar ffprobe en PATH o ubicaciones comunes
            $rutasCandidatas[] = 'ffprobe.exe';
            $rutasCandidatas[] = 'C:\ffmpeg\bin\ffprobe.exe';
            $rutasCandidatas[] = 'C:\Program Files\ffmpeg\bin\ffprobe.exe';
        } else {
            // Unix-like systems
            $rutasCandidatas[] = 'ffprobe';
            $rutasCandidatas[] = '/usr/bin/ffprobe';
            $rutasCandidatas[] = '/usr/local/bin/ffprobe';
            $rutasCandidatas[] = '/opt/ffmpeg/bin/ffprobe';
        }

        // Buscar en cada ruta candidata
        foreach ($rutasCandidatas as $ruta) {
            if ($this->verificarFfprobe($ruta)) {
                $this->line("    ✓ ffprobe encontrado en: {$ruta}");
                return $ruta;
            }
        }

        return null;
    }

    /**
     * Verificar si ffprobe está disponible en la ruta especificada
     */
    private function verificarFfprobe($ruta): bool
    {
        try {
            // Si es una ruta absoluta o relativa, verificar que el archivo existe
            if (str_contains($ruta, '/') || str_contains($ruta, '\\')) {
                if (!file_exists($ruta)) {
                    return false;
                }
            }

            // Probar ejecutar ffprobe con un comando simple
            $command = sprintf('"%s" -version 2>%s', $ruta, PHP_OS_FAMILY === 'Windows' ? 'nul' : '/dev/null');
            
            $output = shell_exec($command);
            
            // Si el comando se ejecuta y contiene información de ffprobe, es válido
            return $output && str_contains(strtolower($output), 'ffprobe');
            
        } catch (\Exception $e) {
            return false;
        }
    }
}
