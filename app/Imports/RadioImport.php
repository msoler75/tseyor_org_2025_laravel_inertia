<?php

namespace App\Imports;

use App\Models\Audio;
use App\Models\Comunicado;
use App\Models\RadioItem;
use App\Pigmalion\Markdown;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Pigmalion\StorageItem;

class RadioImport
{

    public static function importar()
    {
        // borra todas las entradas
        // RadioItem::whereRaw("1")->forceDelete();
        RadioItem::truncate();


        // comunicados activados en la radio:
        $activados = [
            0 => [ // GENERAL
                172=>[0],
                201=>[0],
                210=>[0],
                218=>[0],
                223=>[0],
                239=>[0],
                240=>[0],
                254=>[0],
                290=>[0],
                322=>[0],
                351=>[0],
                368=>[0],
                399=>[0],
                419=>[0],
                529=>[0],
                550=>[0],
                578=>[0],
                639=>[0],
                645=>[0],
                726=>[0],
                745 => [0],
                764 => [0],
                810 => [0],
                822 => [1],
                832 => [0],
                855 => [1],
                868 => [0, 1, 2],
                895 => [0],
                906 => [1],
                911 => [1],
                913 => [0],
                933 => [0, 1],
                985 => [0],
                991=> [0],
                996=> [0],
                1012=> [0],
                1056=> [0],
                1102=> [0],
                1114=> [0],
                1115 => [0],
                1139 => [0, 1],
                1184 => [0, 1],
                1239=> [0],
                1271=> [0]
            ],
            1 => [ // TAP
                96=> [0],
                131 => [0],
                177 => [0],
                149=> [0],
                150 => [0, 1],
                153=> [0],
                179 => [0],
                188=> [0]
            ]
        ];


        // Qué audios de comunicados están desactivados
        // numero de comunicado => [indice/s de audio]
        $desactivados=  [
            0 => [], // GENERAL
            1 => [],  // TAP,
            2 => ['01'=>[0,1,2], '02'=>[0,1,2], '03'=>[0,1,2], '04'=>[0,1,2], ] , // DDM
            3 => [] , // MUUL
        ];



        // JINGLES

        $carpetaJingles = "/almacen/medios/radio";

        $dir = new StorageItem($carpetaJingles);

        $dir_jingles = $dir->path;

        $mp3Files = glob($dir_jingles . "/*.mp3");

        foreach ($mp3Files as $mp3File) {

            $file = basename($mp3File);

            // if($file!="entrevista-a-orden-la-pm.html") continue;

            echo "Importando jingle $file\n";

            RadioItem::create(['titulo' => 'Radio Tseyor', 'categoria' => 'Jingles', 'url' => $carpetaJingles . "/" . $file, 'duracion' => self::duracion($mp3File)]);
        }

        // COMUNICADOS 1 al 1100

        // el primer audio de cada comunicado

        //$comunicados = Comunicado::where('numero', '<', 1287)->get();
        $comunicados = Comunicado::where(function($query) {
            $query->where('categoria', 0)->where('numero', '<', 1287)
                  ->orWhere(function($query) {
                      $query->where('categoria', 1)->where('numero', '<', 231);
                  });
        })->get();

        foreach ($comunicados as $comunicado) {
            if ($comunicado->audios) {
                foreach ($comunicado->audios as $index=>$audio) {
                    $mp3File = $audio;
                    $loc = new StorageItem($mp3File);
                    $mp3File = $loc->getPath();
                    if(!file_exists($mp3File)) continue;

                    $duracion = self::duracion($mp3File);
                    $url = $loc->url;
                    if ($duracion && $url) {
                        echo "Importando $mp3File - $comunicado->titulo [cat={$comunicado->categoria}][num={$comunicado->numero}][$index]\n";
                        $activado = !isset($desactivados[$comunicado->categoria][$comunicado->numero]) ||
                        array_search($index, $desactivados[$comunicado->categoria][$comunicado->numero])==-1;
                        $desactivado = $activado?'0':'1';
                        RadioItem::create(['titulo' => $comunicado->titulo, 'categoria' => 'Comunicados', 'url' => $url, 'duracion' => $duracion, 'desactivado' => $desactivado]);
                    }
                }
            }
        }


        // Talleres y meditaciones, cuentos
        // ....
        // falta que lo importe pero con proporcionalidad y variedad segun categoria

        // Categorías que queremos incluir
        $categoriasIgnorar = [
            'Canciones',
            'Música clásica',
        ];

        // Obtener todos los audios de las categorías incluidas
        $audios = Audio::whereNotIn('categoria', $categoriasIgnorar)->get();

        // Agrupar audios por categoría
        $audiosPorCategoria = $audios->groupBy('categoria');

        // Contar audios por categoría
        $conteoAudios = $audiosPorCategoria->map->count(); // retorna un array asociativo

        // Mostrar conteo de audios por categoría
        foreach ($conteoAudios as $categoria => $cantidad) {
            echo "Categoría: $categoria - Cantidad: $cantidad\n";
        }

        // Crear una lista de categorías con audios disponibles
        $categoriasDisponibles = $audiosPorCategoria->keys()->toArray();

        // Inicializar el índice de categoría
        $indiceCategoriaActual = 0;


        $totalAudios = $audios->count();
        $categoriasDisponibles = $audiosPorCategoria->keys()->toArray();
        $indiceCategoriaActual = 0;

        // Calcular cuántos "slots" debe tener cada categoría en la playlist
        $porcentajeCategoria = [];
        $acumuladoCategoria = [];
        foreach ($categoriasDisponibles as $categoria) {
            $porcentajeCategoria[$categoria] = $audiosPorCategoria[$categoria]->count() / $totalAudios;
            $acumuladoCategoria[$categoria] = 0;
        }
        // dd($categoriasDisponibles, $porcentajeCategoria, $acumuladoCategoria);


        $agregados = 0;
        while ($agregados < $totalAudios) {
            $categoriaActual = $categoriasDisponibles[$indiceCategoriaActual];
            // var_export($acumuladoCategoria);
            if ($acumuladoCategoria[$categoriaActual] < 1) {
                $acumuladoCategoria[$categoriaActual] += .1 * $porcentajeCategoria[$categoriaActual];
            } else {
                $acumuladoCategoria[$categoriaActual] -= 1.0;

                $audio = $audiosPorCategoria[$categoriaActual]->shift();

                if ($audio && $audio->audio) {
                    $mp3File = $audio->audio;
                    $loc = new StorageItem($mp3File);
                    $mp3File = $loc->getPath();
                    if(!file_exists($mp3File)) {
                        $agregados++;
                        continue;
                    }

                    $duracion = self::duracion($mp3File);
                    $url = $loc->url;

                    if ($duracion && $url) {
                        // echo "Importando audio $mp3File - $audio->titulo\n";
                        RadioItem::create([
                            'titulo' => $audio->titulo,
                            'categoria' => 'Meditaciones, cuentos y Talleres',
                            'url' => $url,
                            'duracion' => $duracion,
                            'desactivado' => false
                        ]);
                        $agregados++;
                    }
                }
                else
                $agregados++;
            }

            // Pasar a la siguiente categoría
            $indiceCategoriaActual = ($indiceCategoriaActual + 1) % count($categoriasDisponibles);
        }
    }


    private static function duracion($url)
    {
        try {
            $mp3File = null;

            // Manejar diferentes tipos de URLs
            if (preg_match('/^https?:\/\//', $url)) {
                $parsedUrl = parse_url($url);
                $path = $parsedUrl['path'] ?? '';

                if (preg_match('/^\/almacen\//', $path)) {
                    $localPath = str_replace('/almacen/', '', $path);
                    $mp3File = public_path('almacen/' . $localPath);
                    Log::info('RadioImport: URL remota convertida a local', [
                        'url' => $url,
                        'local_path' => $mp3File
                    ]);
                }
            } else {
                $mp3File = $url;
            }

            if (!$mp3File || !file_exists($mp3File)) {
                Log::warning('RadioImport: Archivo MP3 no encontrado', [
                    'url' => $url,
                    'resolved_path' => $mp3File
                ]);
                return 0;
            }

            Log::info('RadioImport: Calculando duración con ffprobe', [
                'file' => $mp3File
            ]);

            // Usar SOLO ffprobe (más preciso y confiable)
            $duracionFfprobe = self::calcularDuracionConFfprobe($mp3File);
            if ($duracionFfprobe > 0) {
                Log::info('RadioImport: Duración calculada exitosamente', [
                    'file' => basename($mp3File),
                    'duration' => $duracionFfprobe,
                    'method' => 'ffprobe'
                ]);
                return $duracionFfprobe;
            }

            // Si ffprobe falla, no hay alternativa confiable
            Log::warning('RadioImport: ffprobe no pudo calcular la duración', [
                'file' => basename($mp3File)
            ]);

        } catch (\Exception $e) {
            Log::error('RadioImport: Error calculando duración', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        Log::warning('RadioImport: No se pudo calcular duración', [
            'url' => $url
        ]);

        return 0;
    }

    /**
     * Calcular duración usando ffprobe
     */
    private static function calcularDuracionConFfprobe($mp3File): int
    {
        try {
            $ffprobePath = base_path('node_modules/.bin/ffprobe');

            if (!file_exists($ffprobePath)) {
                $ffprobePath = 'ffprobe';
            }

            $command = "\"{$ffprobePath}\" -v quiet -show_entries format=duration -of csv=p=0 \"" . addslashes($mp3File) . "\"";

            $output = shell_exec($command);

            if ($output && is_numeric(trim($output))) {
                $duracion = intval(floatval(trim($output)));

                if ($duracion > 0 && $duracion <= 86400) {
                    return $duracion;
                }
            }

        } catch (\Exception $e) {
            Log::error('RadioImport: Error con ffprobe', [
                'file' => $mp3File,
                'error' => $e->getMessage()
            ]);
        }

        return 0;
    }

    /**
     * Método alternativo para calcular duración usando ffprobe
     */
    private static function duracionAlternativa($mp3File)
    {
        try {
            // Intentar usar ffprobe si está disponible
            $command = "ffprobe -v quiet -show_entries format=duration -of csv=p=0 " . escapeshellarg($mp3File);
            $output = shell_exec($command);

            if ($output && is_numeric(trim($output))) {
                $duracion = intval(floatval(trim($output)));
                if ($duracion > 0 && $duracion <= 86400) {
                    return $duracion;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Método alternativo de duración también falló", [
                'archivo' => $mp3File,
                'error' => $e->getMessage()
            ]);
        }

        // Como último recurso, estimar duración basada en tamaño de archivo
        return self::estimarDuracionPorTamano($mp3File);
    }

    /**
     * Estimar duración basada en el tamaño del archivo (muy aproximado)
     */
    private static function estimarDuracionPorTamano($mp3File)
    {
        try {
            $tamanoBytes = filesize($mp3File);
            if ($tamanoBytes > 0) {
                // Estimación muy aproximada: 1 MB ≈ 60 segundos para MP3 de calidad media
                $duracionEstimada = intval($tamanoBytes / (1024 * 1024) * 60);

                // Límites razonables
                $duracionEstimada = max(30, min($duracionEstimada, 7200)); // Entre 30 segundos y 2 horas

                Log::info("Duración estimada por tamaño de archivo", [
                    'archivo' => $mp3File,
                    'tamano_bytes' => $tamanoBytes,
                    'duracion_estimada' => $duracionEstimada
                ]);

                return $duracionEstimada;
            }
        } catch (\Exception $e) {
            Log::error("Error al estimar duración por tamaño", [
                'archivo' => $mp3File,
                'error' => $e->getMessage()
            ]);
        }

        // Último recurso: duración por defecto
        return 180; // 3 minutos por defecto
    }
}
