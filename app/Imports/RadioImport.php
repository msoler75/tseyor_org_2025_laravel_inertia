<?php

namespace App\Imports;

use App\Models\Audio;
use App\Models\Comunicado;
use App\Models\RadioItem;
use App\Pigmalion\Markdown;
use Illuminate\Support\Facades\Storage;
use wapmorgan\Mp3Info\Mp3Info;
use App\Pigmalion\DiskUtil;

class RadioImport
{

    public static function importar()
    {
        // borra todas las entradas
        RadioItem::whereRaw("1")->forceDelete();


        // JINGLES

        $carpetaJingles = "/almacen/medios/radio";

        list($disk, $ruta) = DiskUtil::obtenerDiscoRuta($carpetaJingles);

        $dir_jingles = Storage::disk($disk)->path($ruta);

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
        foreach($comunicados as $comunicado) {
            if($comunicado->audios) {
                $mp3File = $comunicado->audios[0];
                list($disk, $ruta) = \App\Pigmalion\DiskUtil::obtenerDiscoRuta($mp3File);
                $mp3File = Storage::disk($disk)->path($ruta);

                $duracion = self::duracion($mp3File);
                $url = DiskUtil::normalizePath($ruta);
                if($duracion && $url) {
                    echo "Importando comunicado $mp3File - $comunicado->titulo\n";
                    RadioItem::create(['titulo' => $comunicado->titulo, 'categoria' => 'Comunicados', 'url' => $url, 'duracion' => $duracion]);
                }
            }
        }


        // Talleres y meditaciones, cuentos
        // ....

    }


    private static function duracion($mp3File)
    {
        // To get basic audio information
        try {

            $audio = new Mp3Info($mp3File);
        return intval($audio->duration);
        } catch(\DivisionByZeroError $e){
            return 0;
        } catch(\ErrorException $e) {
            return 0;
        }
    }
}
