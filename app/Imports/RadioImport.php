<?php

namespace App\Imports;

use App\Models\Audio;
use App\Models\Comunicado;
use App\Models\RadioItem;
use App\Pigmalion\Markdown;
use Illuminate\Support\Facades\Storage;
use wapmorgan\Mp3Info\Mp3Info;
use App\Pigmalion\StorageItem;

class RadioImport
{

    public static function importar()
    {
        // borra todas las entradas
        RadioItem::whereRaw("1")->forceDelete();


        // comunicados activados en la radio:
        $activados = [
            0 => [
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
            1 => [
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

        $comunicados = Comunicado::where('numero', '<', 1100)->get();
        foreach ($comunicados as $comunicado) {
            if ($comunicado->audios) {
                foreach ($comunicado->audios as $index=>$audio) {
                    $mp3File = $audio;
                    $loc = new StorageItem($mp3File);
                    $mp3File = $loc->getPath();

                    $duracion = self::duracion($mp3File);
                    $url = $loc->url;
                    if ($duracion && $url) {
                        echo "Importando comunicado $mp3File - $comunicado->titulo\n";
                        $activado = isset($activados[$comunicado->categoria][$comunicado->numero]) &&
                        array_search($index, $activados[$comunicado->categoria][$comunicado->numero]) !== false;
                        $desactivado = !$activado;
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
        $conteoAudios = $audiosPorCategoria->map->count();

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
            }

            // Pasar a la siguiente categoría
            $indiceCategoriaActual = ($indiceCategoriaActual + 1) % count($categoriasDisponibles);
        }
    }


    private static function duracion($mp3File)
    {
        // To get basic audio information
        try {

            $audio = new Mp3Info($mp3File);
            return intval($audio->duration);
        } catch (\DivisionByZeroError $e) {
            return 0;
        } catch (\ErrorException $e) {
            return 0;
        }
    }
}
