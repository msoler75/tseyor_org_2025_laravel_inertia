<?php

namespace App\Imports;

use App\Models\Pagina;

class PaginaImport
{

    public static function importar()
    {

        $lista_paginas = base_path('resources/seo/SEO.json');

        $contenido = file_get_contents($lista_paginas);
        $paginas = json_decode($contenido, true);

        foreach ($paginas as $ruta => $pagina) {
            $existePagina = Pagina::where('ruta', $ruta)->exists();

            if (!$existePagina) {
                $nuevaPagina = new Pagina();
                $nuevaPagina->titulo = $pagina['title'];
                $nuevaPagina->ruta = $ruta;
                $nuevaPagina->descripcion = $pagina['description'];
                $nuevaPagina->visibilidad = 'P';

                $nuevaPagina->save();
            }
        }
    }
}
