<?php

namespace App\Traits;

// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Contenido;


trait EsContenido
{
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Acciones antes de guardar el modelo
            self::rellenarSlugImagenYDescripcion($model);
        });

        static::saved(function ($model) {
            // Acciones después de que el modelo se haya guardado
            self::guardarContenido($model->table, $model);
        });

        static::deleting(function ($model) {
             // dd("deleting");
             //self::removerContenido($model);
            // Acciones antes de borrar el modelo
            // ...
        });

        static::deleted(function ($model) {
            //dd("deleted", $model);
            self::removerContenido($model);
            // Acciones después de que el modelo se haya borrado
            // ...
        });
    }


    public static function rellenarSlugImagenYDescripcion($objeto)
    {
        $fillable = $objeto->getFillable();

        // rellenamos slug si existe y está vacío
        if (in_array('slug', $fillable) && empty($objeto->slug)) {
            $candidatos = ["titulo", "nombre"];
            foreach ($candidatos as $campo) {
                if (in_array($campo, $fillable)) {
                    // $prefijo = $objeto->numero ? $objeto->numero . "-" : "";
                    $prefijo = '';
                    $objeto->slug = Str::slug($prefijo . $objeto->{$campo});
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
            $html = Str::markdown($objeto->texto ?? "");
        }
        // Rellenamos imagen (si está vacía) con el contenido del texto (si existe)
        if (in_array('imagen', $fillable) && empty($objeto->imagen)) {
            $matches = [];
            preg_match_all('/<img [^>]*src=["\']([^"\']+)/i', $html, $matches);

            // busca la primera imagen que tenga unas dimensiones minimas
            foreach ($matches[1] as $imageUrl) {
                $imagePath = str_replace(url('/'), '', $imageUrl); // Obtener la ruta relativa de la imagen
                $absolutePath = public_path($imagePath); // Obtener la ruta absoluta de la imagen

                // Obtener las dimensiones de la imagen
                $imageSize = getimagesize($absolutePath);
                $imageWidth = $imageSize[0];
                $imageHeight = $imageSize[1];

                // Verificar las dimensiones mínimas requeridas (ancho y alto)
                $minWidth = 300; // Ancho mínimo deseado
                $minHeight = 250; // Alto mínimo deseado

                if ($imageWidth >= $minWidth && $imageHeight >= $minHeight) {
                    $objeto->imagen = $imagePath;
                    break; // Salir del bucle después de encontrar la primera imagen adecuada
                }
            }
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
        $contenido->slug_ref = $datos->slug ?? $datos->ruta ?? null;
        $contenido->descripcion = $datos->descripcion ?? ($datos->pais . " " . $datos->poblacion);
        $contenido->texto = $datos->texto ?? ($datos->direccion . " " . $datos->codigo . " " . $datos->provincia . " " . $datos->email . " " . $datos->telefono);
        $contenido->imagen = $datos->imagen ?? null;
        $contenido->fecha = $datos->published_at ?? $datos->updated_at ?? null;
        $contenido->visibilidad = $datos->visibilidad ?? 'P';


        // Guardar el modelo en la base de datos
        $contenido->save();
    }


    public static function removerContenido($objeto)
    {
        $coleccion = $objeto->table;
        $contenido = Contenido::where('coleccion', $coleccion)
        ->where('id_ref', $objeto->id);

        if($contenido) {
            // elimina el contenido asociado
            $contenido->delete();
        }
    }
}
