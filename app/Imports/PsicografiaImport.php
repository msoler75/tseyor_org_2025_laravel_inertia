<?php

namespace App\Imports;

use App\Models\Psicografia;

class PsicografiaImport
{

    public static function importar()
    {

        Psicografia::truncate();

        $imagenes_dir = storage_path('app/public/medios/psicografias');

        $imagenes = glob($imagenes_dir . "/*");
        foreach ($imagenes as $imagen) {
            // obtiene el mimetype
            $mimetype = mime_content_type($imagen);
            $fichero = basename($imagen);

            // si es una imagen:
            if (strpos($mimetype, 'image/') !== false) {
                $data = [
                    'titulo' => substr($fichero, 0, strrpos($fichero, '.')),
                    'imagen' => "/almacen/medios/psicografias/$fichero",
                    'categoria' => 'General',
                    'descripcion' => $fichero
                ];
                if (preg_match("/CasaTseyor|Muulasterio/i", $fichero))
                    $data['categoria'] = 'Muulasterios y Casas Tseyor';
                if (preg_match("/belankil|prior/i", $fichero))
                    $data['categoria'] = 'Priores y Belankiles';
                if (preg_match("/contactobases/i", $fichero))
                    $data['categoria'] = 'Contacto con las bases';
                if (preg_match("/fractalom/i", $fichero))
                    $data['categoria'] = 'FractalOm';
                if (preg_match("/neent/i", $fichero))
                    $data['categoria'] = 'Neent';
                if (preg_match("/palabrasmaya/i", $fichero))
                    $data['categoria'] = 'Palabras Maya';

                Psicografia::create($data);
            }
        }
    }
}
