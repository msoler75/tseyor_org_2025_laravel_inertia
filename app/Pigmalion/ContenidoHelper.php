<?php


namespace App\Pigmalion;

use Illuminate\Support\Str;
use App\Models\Contenido;
use App\Models\ContenidoBaseModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Pigmalion\Countries;
use App\Pigmalion\StorageItem;

class ContenidoHelper
{

    public static function generarTeaser($texto, $longitudMaxima = 400)
    {
        // Verificar si la longitud del texto es mayor que la longitud máxima permitida
        if (strlen($texto) > $longitudMaxima) {
            // Recortar el texto a la longitud máxima y agregar puntos suspensivos al final
            $teaser = mb_substr($texto, 0, $longitudMaxima - 3) . '...';
        } else {
            // Si el texto es menor o igual a la longitud máxima, no es necesario recortarlo
            $teaser = $texto;
        }

        return trim($teaser);
    }

    public static function removerTitulo($descripcion, $titulo)
    {
        $patron = '/' . preg_quote($titulo, '/') . '/i';
        return trim(preg_replace($patron, '', $descripcion));
    }

    public static function rellenarSlugImagenYDescripcion($objeto)
    {
        Log::info("ContenidoBaseModel::rellenarSlugImagenYDescripcion");
        $fillable = $objeto->getFillable();

        // rellenamos slug si existe y está vacío
        if (in_array('slug', $fillable) && empty($objeto->slug)) {
            $candidatos = ["titulo", "nombre"];
            foreach ($candidatos as $campo) {
                if (in_array($campo, $fillable)) {
                    // $prefijo = $objeto->numero ? $objeto->numero . "-" : "";
                    $prefijo = '';
                    $objeto->slug = substr(Str::slug($prefijo . $objeto->{$campo}), 0, 255);
                    // dd($objeto->slug);
                    break;
                }
            }
        }
        if (!in_array('texto', $fillable))
            return;

        $texto = "";
        if (
            (in_array('imagen', $fillable) && empty($objeto->imagen))
            || (in_array('descripcion', $fillable) && empty($objeto->descripcion))
        ) {
            //$parsedown = new Parsedown();
            //$texto = $parsedown->text($objeto->texto);
            $texto = Str::markdown($objeto->texto ?? "");
        }
        // Rellenamos imagen (si está vacía) con el contenido del texto (si existe)
        if (in_array('imagen', $fillable) && empty($objeto->imagen)) {

            Log::info("ContenidoHelper::rellenarSlugImagenYDescripcion: buscamos imagen del contenido");

            $matches = [];
            preg_match_all('/<img [^>]*src=["\']([^"\']+)/i', $texto, $matches);

            // busca la primera imagen que tenga unas dimensiones minimas
            foreach ($matches[1] as $imageUrl) {

                Log::info("imageUrl: $imageUrl");

                if (!preg_match("/^https?:/", $imageUrl)) {

                    //$imagePath = str_replace(url('/'), '', $imageUrl); // Obtener la ruta relativa de la imagen
                    $absolutePath = (new StorageItem(urldecode($imageUrl)))->path;
                    Log::info("path: $absolutePath");

                    if (!file_exists($absolutePath)) {
                        Log::info("file not found: $absolutePath");
                        continue;
                    }

                    // Obtener las dimensiones de la imagen
                    $imageSize = getimagesize($absolutePath);
                    $imageWidth = $imageSize[0];
                    $imageHeight = $imageSize[1];

                    // Verificar las dimensiones mínimas requeridas (ancho y alto)
                    $minWidth = 300; // Ancho mínimo deseado
                    $minHeight = 250; // Alto mínimo deseado

                    if ($imageWidth >= $minWidth && $imageHeight >= $minHeight) {
                        $objeto->imagen = $imageUrl;
                        break; // Salir del bucle después de encontrar la primera imagen adecuada
                    }
                }
            }
        }

        // cambiamos las imagenes de portada del contenido en el caso de los guías, poniendo la imagen sin texto
        if (in_array('imagen', $fillable)) {
            $objeto->imagen = preg_replace("#/medios/guias/con_nombre/(.*)\.jpg#", "/medios/guias/$1.jpg", $objeto->imagen);
            if (strpos($objeto->imagen, '/medios/guias/') !== false)
                $objeto->imagen = strtolower($objeto->imagen);
        }

        $titulo = $objeto->titulo ?? $objeto->nombre ?? "";

        // generamos una descripción a partir del texto si es necesario
        if (in_array('descripcion', $fillable) && empty($objeto->descripcion)) {
            $descripcion = strip_tags($texto);
            $descripcion = \App\Pigmalion\Markdown::removeMarkdown($descripcion);
            $descripcion = str_replace("\n", ' ', $descripcion); // Agregar espacio entre líneas
            if ($titulo) $descripcion = self::removerTitulo($descripcion, $titulo);
            $descripcion = self::generarTeaser($descripcion);
            //$descripcion = rtrim($descripcion, "!,.-");
            $objeto->descripcion = $descripcion;
        }

        // removemos el título de la descrpcion
        if ($titulo && in_array('descripcion', $fillable)) {
            $objeto->descripcion = self::removerTitulo($objeto->descripcion, $titulo);
        }

        // revisa si el campo de "visibilidad" de la publicación tiene la fecha a NULL
        if (in_array('visibilidad', $fillable) && in_array('published_at', $fillable)) {
            if ($objeto->visibilidad != "B" && !$objeto->published_at)
                $objeto->published_at = date('Y-m-d H:i:s');
        }
    }


    public static function guardarContenido(ContenidoBaseModel $model)
    {
        $coleccion = $model->getTable();

        $contenido = Contenido::where('coleccion', $coleccion)
            ->where('id_ref', $model->id)
            ->withTrashed() // incluimos los marcados como borrados
            ->first();

        Log::info('ContenidoHelper::guardarContenido', ['coleccion' => $coleccion, 'id' => $model->id, 'contenido' => $contenido]);

        if ($contenido == null) {
            // Crear un nuevo modelo Contenido
            $contenido = new Contenido();
            $contenido->coleccion = $coleccion;
            $contenido->id_ref = $model->id;
        }

        // Asignar las propiedades del modelo con los datos recibidos
        $descripcion = $model->descripcion ?? '';
        // si existe atributo pais, lo convertimos
        if (isset($model->pais)) {
            $values = [Countries::getCountry($model->pais)];
            if (isset($model->provincia)) $values[] = $model->provincia;
            if (isset($model->poblacion)) $values[] = $model->poblacion;
            if (isset($model->descripcion)) $values[] = $model->descripcion;
            $descripcion = implode(" ", $values);
        }

        $contenido->titulo = $model->titulo ?? $model->nombre;
        $contenido->slug_ref = $model->slug ?? $model->ruta ?? null;
        $contenido->descripcion = $descripcion;
        $contenido->imagen = $model->imagen ?? null;
        $contenido->fecha = $model->published_at ?? $model->updated_at ?? null;
        $contenido->visibilidad = $model->visibilidad  ?? 'P';
        if ($model->oculto) $contenido->visibilidad = 'O';
        $contenido->deleted_at = $model->deleted_at ?? null;

        if (strlen($contenido->descripcion) > 400)
            $contenido->descripcion = mb_substr($contenido->descripcion, 0, 399);

        // obtiene el texto que servirá para indexar el buscador
        $contenido->texto_busqueda = $model->getTextoContenidoBuscador();

        if (isset($model->numero)) {
            $contenido->texto_busqueda .= " " . $model->numero;
        }


        // Guardar el modelo en la base de datos
        $contenido->save();
    }


    public static function removerContenido($objeto)
    {
        $coleccion = $objeto->getTable();
        $contenido = Contenido::where('coleccion', $coleccion)
            ->where('id_ref', $objeto->id)->first();

        Log::info("ContenidoHelper::removerContenido, ", ['coleccion' => $coleccion, 'id' => $objeto->id, 'contenido' => $contenido]);

        if ($contenido) {
            // elimina el contenido asociado
            $contenido->delete();
        }
    }

    /**
     * Analiza el texto y mueve las imagenes de la carpeta temporal a la carpeta de medios
     * este método modifica el texto del modelo (si existe el campo texto)
     * Importante: Se asume que el campo 'texto' está en MARKDOWN
     * @return true si ha habido cambios
     **/
    public static function moverImagenesContenido($objeto)
    {
        $cambio = false;
        $desde = ContenidoBaseModel::getCarpetaMediosTemp();
        $hacia = $objeto->getCarpetaMedios();

        Log::info("moverImagenesContenido " . ($objeto->texto ?? ''), ['hacia' => $hacia, 'imagen'=>$objeto->imagen]);
        if ($objeto->texto ?? '') {
            $texto = $objeto->texto;

            $imagenes_movidas = \App\Pigmalion\Markdown::moverImagenes($texto, $desde, $hacia);
            if (count($imagenes_movidas)) {
                $objeto->texto = $texto;
                if ($objeto->imagen ?? '') {
                    Log::info("imagen del objeto: " . $objeto->imagen);
                    foreach ($imagenes_movidas as $mov) {
                        // cambia la referencia de la imagen del contenido de carpeta temporal a la de la carpeta de medios
                        if ($objeto->imagen == $mov['desde'])
                            $objeto->imagen = $mov['a'];
                    }
                }

                $cambio = true;
            }
        }

        $imagen = $objeto->imagen;
        if($imagen)
            Log::info("Imagen=$imagen");
        if ($imagen && strpos($imagen, 'temp/') !== false) {

            // renombramos la imagen
            $nuevoNombre = $hacia . "/" . basename($imagen);

            $disk = 'public';
            Log::info("move1: $disk: $imagen -> $nuevoNombre");

            $origen = $imagen;
            $destino =(new StorageItem($nuevoNombre))->location;

            Log::info("move2: $disk: ", ['origen'=>$origen, 'nuevoNombre'=>$nuevoNombre, 'destino'=>$destino]);

            // movemos la imagen a la nueva carpeta
            if (StorageItem::copy($imagen, $destino)) {
                (new StorageItem( $imagen))->delete();
            }

            $objeto->imagen = $destino;

            $cambio = true;
        }

        /*if($objeto->imagen&& strpos($objeto->imagen, 'medios/') === 0) {
            $objeto->imagen = '/almacen/'.$objeto->imagen;
            $cambio = true;
        }*/

        // si ha habido cambios retornamos true
        return $cambio;
    }
}
