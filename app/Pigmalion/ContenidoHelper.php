<?php


namespace App\Pigmalion;

use Illuminate\Support\Str;
use App\Models\Contenido;
use App\Models\ContenidoBaseModel;
use Illuminate\Support\Facades\Log;
use App\Pigmalion\DiskUtil;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;


class ContenidoHelper
{

    public static function generarTeaser($texto, $longitudMaxima = 400) {
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
                    //$absolutePath = DiskUtil::getAbsolutePath($imagePath); // Obtener la ruta absoluta de la imagen
                    list ($disk, $folder) = DiskUtil::obtenerDiscoRuta(urldecode($imageUrl));
                    Log::info("disk: $disk, folder: $folder");

                    $absolutePath = Storage::disk($disk)->path($folder);
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
            $objeto->imagen = strtolower(preg_replace("#/medios/guias/con_nombre/(.*)\.jpg#", "/medios/guias/$1.jpg", $objeto->imagen));
        }

        // generamos una descripción a partir del texto si es necesario
        if (in_array('descripcion', $fillable) && empty($objeto->descripcion)) {
            $descripcion = strip_tags($texto);
            $descripcion = \App\Pigmalion\Markdown::removeMarkdown($descripcion);
            $descripcion = str_replace("\n", ' ', $descripcion); // Agregar espacio entre líneas
            $descripcion = self::generarTeaser($descripcion);
            //$descripcion = rtrim($descripcion, "!,.-");
            $objeto->descripcion = $descripcion;
        }

        // revisa si el campo de "visibilidad" de la publicación tiene la fecha a NULL
        if (in_array('visibilidad', $fillable) && in_array('published_at', $fillable)) {
            if ($objeto->visibilidad != "B" && !$objeto->published_at)
                $objeto->published_at = date('Y-m-d H:i:s');
        }
    }


    public static function guardarContenido(string $coleccion, ContenidoBaseModel $model)
    {
        $contenido = Contenido::where('coleccion', $coleccion)
            ->where('id_ref', $model->id)->first();

        if ($contenido == null) {
            // Crear un nuevo modelo Contenido
            $contenido = new Contenido();
            $contenido->coleccion = $coleccion;
            $contenido->id_ref = $model->id;
        }

        // Asignar las propiedades del modelo con los datos recibidos
        $contenido->titulo = $model->titulo ?? $model->nombre;
        $contenido->slug_ref = $model->slug ?? $model->ruta ?? null;
        $contenido->descripcion = $model->descripcion ?? ($model->pais . " " . $model->poblacion);
        $contenido->imagen = $model->imagen ?? null;
        $contenido->fecha = $model->published_at ?? $model->updated_at ?? null;
        $contenido->visibilidad = $model->visibilidad ?? 'P';

        if (strlen($contenido->descripcion) > 400)
            $contenido->descripcion = mb_substr($contenido->descripcion, 0, 399);

        // obtiene el texto que servirá para indexar el buscador
        $contenido->texto_busqueda = $model->getTextoContenidoBuscador();

        // Guardar el modelo en la base de datos
        $contenido->save();

    }


    public static function removerContenido($objeto)
    {
        $coleccion = $objeto->table;
        $contenido = Contenido::where('coleccion', $coleccion)
            ->where('id_ref', $objeto->id);

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
        Log::info("moverImagenesContenido " . ($objeto->texto ?? ''));
        if ($objeto->texto ?? '') {
            $texto = $objeto->texto;

            $desde = ContenidoBaseModel::getCarpetaMediosTemp();
            $hacia = $objeto->getCarpetaMedios();

            $imagenes_movidas = \App\Pigmalion\Markdown::moverImagenes($texto, $desde, $hacia);
            if (count($imagenes_movidas)) {
                $objeto->texto = $texto;
                if ($objeto->imagen ?? '') {
                    Log::info("imagen del objeto: ".$objeto->imagen);
                    foreach ($imagenes_movidas as $mov) {
                        // cambia la referencia de la imagen del contenido de carpeta temporal a la de la carpeta de medios
                        if ($objeto->imagen == $mov['desde'])
                            $objeto->imagen = $mov['a'];
                    }
                }

                // si ha habido cambios retornamos true
                return true;
            }
        }
        return false;
    }


    public static function generatePdf(ContenidoBaseModel $contenido) {

        $nombreArchivo = ($contenido->titulo ?? $contenido->nombre) . ' - TSEYOR.pdf';

        // comprobaremos si existe ya el archivo pdf generado

        $pdf_path = $contenido->pdfPath; // attribute with accesor

        $pdf_full_path = Storage::disk('public')->path($pdf_path);

        $headers = [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        // dd(dirname($pdf_full_path));
        DiskUtil::ensureDirExists(dirname($pdf_full_path));

        if (file_exists($pdf_full_path) && filemtime($pdf_full_path) > $contenido->updated_at->getTimestamp()) {
            return response()->file($pdf_full_path, $headers);
        }

        $texto = $contenido->texto;

        // reducimos las imagenes de los guías para que no sean tan grandes
        $texto = preg_replace_callback("#\!\[\]\(\/almacen\/medios\/guias\/con_nombre\/.+?\.jpg\)\{width=(\d+),height=(\d+)\}#", function ($match) {
            $w = intval($match[1]);
            $h = intval($match[2]);
            $r = $w / $h;
            $h = 250;
            $w = $h * $r;
            return str_replace("width=" . $match[1] . ",height=" . $match[2], "width=$w,height=$h", $match[0]);
        }, $texto);


        $html = \App\Pigmalion\Markdown::toHtml($texto);

        // para que procese las imagenes en el pdf:
        // método 1: reemplazar todas las imagenes sus rutas relativas con rutas absolutas de disco (NO FUNCIONA)
        // método 2: codificarlas en base64 (ACTUAL MÉTODO)
        $html = preg_replace_callback('/<img([^>]+)src="([^"]+)"/', function ($matches) {
            $fullpath = DiskUtil::getRealPath($matches[2]);
            //dd($matches);
            // $prefix = ""; // "file://";
            // $r = '<img' . $matches[1] . 'src="' . $prefix.$fullpath .'"'; // método 1
            // dd($fullpath);
            if(!$fullpath) {
                return $matches[0];
            }
            $r = '<img' . $matches[1] . 'src="data:image/png;base64,' . base64_encode(file_get_contents($fullpath)) . '"'; // método 2
            return $r;
        }, $html);

        // dd($html);

        // Contenido HTML completo con etiquetas <html>, <head> y <body>
        $pdf = Pdf::loadView('contenido-pdf', [
            'titulo' => $contenido->titulo ?? $contenido->nombre,
            'texto' => $html,
        ]);

        // guardamos el pdf generado
        DiskUtil::ensureDirExists(dirname($pdf_full_path));
        $pdf->save($pdf_full_path);

        // descargamos el archivo pdf
        // return $pdf->download($nombreArchivo);

        // mostramos el contenido del pdf en la página
        // return $pdf->stream($nombreArchivo);
        return response($pdf->stream($nombreArchivo), 200, $headers);
    }
}
