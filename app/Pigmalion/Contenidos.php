<?php

namespace App\Pigmalion;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Contenido;

class Contenidos
{


    public static function rellenarSlugImagenYDescripcion($objeto)
    {
        $fillable = $objeto->getFillable();

        // rellenamos slug si existe y está vacío
        if (in_array('slug', $fillable) && empty($objeto->slug)) {
            $candidatos = ["titulo", "nombre"];
            foreach ($candidatos as $campo) {
                if (in_array($campo, $fillable)) {
                    $objeto->slug = Str::slug($objeto->{$campo});
                    // dd($objeto->slug);
                    break;
                }
            }
        }
        if (!in_array('texto', $fillable)) return;

        $html = "";
        if ((in_array('imagen', $fillable) && empty($objeto->imagen))
            || (in_array('descripcion', $fillable) && empty($objeto->descripcion))
        ) {
            //$parsedown = new Parsedown();
            //$html = $parsedown->text($objeto->texto);
            $html = Str::markdown($objeto->texto);
        }
        // rellenamos imagen (si está vacía) con el contenido del texto (si existe)
        if (in_array('imagen', $fillable) && empty($objeto->imagen)) {
            $matches = [];
            preg_match('/<img [^>]*src=["\']([^"\']+)/i', $html, $matches);
            $objeto->imagen = isset($matches[1]) ? $matches[1] : null;
            $storageFolderName = basename(storage_path());
            $objeto->imagen = str_replace(url('/') . "/" . $storageFolderName, "", $objeto->imagen); // remueve la parte base
        }
        // generamos una descripción a partir del texto si es necesario
        if (in_array('descripcion', $fillable) && empty($objeto->descripcion)) {
            $descripcion = mb_substr(strip_tags($html), 0, 400 - 3);
            $descripcion = str_replace("\n", ' ', $descripcion); // Agregar espacio entre líneas
            $descripcion = rtrim($descripcion, "!,.-");
            $descripcion = substr($descripcion, 0, strrpos($descripcion, ' ')) . '...';
            $objeto->descripcion = $descripcion;
        }

        // revisa si el campo de "visibilidad" de la publicación tiene la fecha a NULL
        if (in_array('visibilidad', $fillable) && in_array('published_at', $fillable)) {
            if ($objeto->visibilidad != "B" && !$objeto->published_at)
                $objeto->published_at = date('Y-m-d H:i:s');
        }
    }


    public static function guardarContenido($coleccion, $datos)
    {
        $contenido = Contenido::where('coleccion', $coleccion)
            ->where('id_ref', $datos->id)->first();

        if ($contenido == null) {
            // Crear un nuevo modelo Contenido
            $contenido = new Contenido();
            $contenido->coleccion = $coleccion;
            $contenido->id_ref = $datos->id;
        }

        // Asignar las propiedades del modelo con los datos recibidos
        $contenido->titulo = $datos->titulo ?? $datos->nombre;
        $contenido->slug_ref = $datos->slug;
        $contenido->descripcion = $datos->descripcion ?? ($datos->pais . " " . $datos->poblacion);
        $contenido->texto = $datos->texto ?? ($datos->direccion . " " . $datos->codigo . " " . $datos->provincia . " " . $datos->email . " " . $datos->telefono);
        $contenido->imagen = $datos->imagen;
        $contenido->fecha = $datos->published_at ?? $datos->updated_at ?? null;
        $contenido->visibilidad = $datos->visibilidad ?? 'P';


        // Guardar el modelo en la base de datos
        $contenido->save();
    }
}
