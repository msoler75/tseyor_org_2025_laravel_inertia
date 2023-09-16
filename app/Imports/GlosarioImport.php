<?php

namespace App\Imports;

use App\Pigmalion\WordImport;
use App\Models\Termino;


class GlosarioImport
{

    public static function importar()
    {
        try {
            $imported = new WordImport();


            $glosario = $imported->content;
            dd($glosario);


            /*$termino = Comunicado::create([
                "nombre" => $nombre,
                "texto" => $texto
            ]);

            $contenido = Comunicado::findOrFail($id);

            $contenido->texto = $imported->content;

            // Copiaremos las imágenes a la carpeta de destino
            $imagesFolder = "media/comunicados/_{$contenido->id}";

            // reemplazar la ubicación de las imágenes en el texto del comunicado
            $contenido->texto = preg_replace("/\bmedia\//", "$imagesFolder/", $contenido->texto);
            $contenido->texto = preg_replace("/\.\/media\//", "/storage/media/", $contenido->texto);

            $contenido->descripcion = null; // para que se regenere

            $contenido->imagen = null; // para que se elija otra nueva, si la hay
            $contenido->save();

            // copia las imágenes desde la carpeta temporal al directorio destino, sobreescribiendo las anteriores en la carpeta
            $imported->copyImagesTo($imagesFolder, true);
*/
    }
}
