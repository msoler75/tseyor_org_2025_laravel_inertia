<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Pigmalion\StorageItem;
use Illuminate\Support\Facades\Cache;


class ImagenesController extends Controller
{

    /**
     * Quita la primera barra si es necesario
     */
    private function normalizarRuta($ruta)
    {
        if (strpos($ruta, '/') === 0) {
            $ruta = substr($ruta, 1);
        }

        if (strpos($ruta, 'almacen') === 0) {
            $ruta = substr($ruta, 8);
        }
        return $ruta;
    }


    public function size(Request $request)
    {
        $url = $request->input('url');
        if (!$url)
            abort(400, 'Debe especificar la URL de la imagen');

        // obtenemos las dimensiones de la imagen

        $sti = new StorageItem($url);

        $fullpath = $sti->path;

        // obtenemos las dimensiones de la imagen en la ubicación $fullpath, hemos de comprobar si es un PNG, o JPG...
        $info = @getimagesize($fullpath);

        // retorna respuesta json
        return response()->json(['width' => $info[0] ?? 0, 'height' => $info[1] ?? 0], 200)
            ->header('Cache-Control', 'public, max-age=2592000');
    }


    public function descargar(Request $request, $rutaImagen)
    {
        $sti = new StorageItem($rutaImagen);
        $imageFullPath = $sti->path;
        // dd($rutaImagen, $disk, $ruta, $imageFullPath);

        $mime = File::mimeType($imageFullPath);

        $params = $request->input();


        if (empty($params)) {
            return response()->file($imageFullPath, ['Content-Type' => $mime]);
        }

        $format = "webp";
        $quality = 70;

       // create cache path
        $cachePath = 'framework/image_cache/';
        $cacheFilename = md5($imageFullPath . serialize($params)) . '.' . $format;
        $cacheFilePath = $cachePath . $cacheFilename;
        $cacheFullPath = storage_path($cacheFilePath);

        // check if image exists in cache
        if (file_exists($cacheFullPath)) {
            $originalModifiedTime = filemtime($imageFullPath);
            $cacheModifiedTime = filemtime($cacheFullPath);

            if ($originalModifiedTime <= $cacheModifiedTime) {
                return response()->file($cacheFullPath, ['Content-Type' => $mime]);
            }
        }

        // create image manager with desired driver
        $manager = new ImageManager(new Driver());

        // read image from file system
        $image = $manager->read($imageFullPath);

        if(count($params))
        {
            ini_set('memory_limit', '256M');
            // Apply image transformations
            $this->transformarImagen($image, $params);
        }

        $format = $params["fmt"] ?? "webp";


        // browser accept webp format?
        if ($format == "webp") {
            $mime = "image/webp";
            $image->toWebp($quality);
        } else {
            $mime = "image/jpeg";
            $image->toJpeg($quality);
        }

        // create the cache folder if it doesn't exist
        $folder = dirname($cacheFullPath);
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0777, true, true);
        }

        $image->save($cacheFullPath);

        return response()->file($cacheFullPath, ['Content-Type' => $mime]);
    }






    private function transformarImagen($image, array $params)
    {
        // Obtener dimensiones actuales de la imagen
        $currentWidth = $image->width();
        $currentHeight = $image->height();


        // Variables para almacenar las dimensiones deseadas
        $width = $params['w'] ?? null;
        $height = $params['h'] ?? null;
        $maxWidth = $params['mw'] ?? null;
        $maxHeight = $params['mh'] ?? null;

        if ($width == "auto") $width = null;
        if ($height == "auto") $height = null;

        $cover = array_key_exists('cover', $params);
        $crop = array_key_exists('crop', $params);
        $contain = array_key_exists('contain', $params);


        // Si se especifica 'cover' y dimensiones están definidas
        if ($cover && ($width || $height)) {
            if ($width == null) $width = $currentWidth;
            if ($height == null) $height = $currentHeight;
            $image->cover($width, $height);
        } else if ($crop && ($width || $height)) {
            if ($width == null) $width = $currentWidth;
            if ($height == null) $height = $currentHeight;
            $image->crop($width, $height);
        } else   if ($contain && ($width || $height)) {
            if ($width == null) $width = $currentWidth;
            if ($height == null) $height = $currentHeight;
            $image->contain($width, $height);
        } else {
            // Redimensionar manteniendo el aspect ratio sin 'cover'
            if ($width || $height) {
                $image = $image->scale($width, $height);
            }

            // Aplicar redimensionado máximo si es necesario
            if ($maxWidth || $maxHeight) {
                if ($maxWidth && $image->width() > $maxWidth) {
                    $image = $image->scale($maxWidth, null);
                }

                if ($maxHeight && $image->height() > $maxHeight) {
                    $image = $image->scale(null, $maxHeight);
                }
            }
        }
    }


    public function mockupLibro($rutaImagen)
    {
        $rutaImagen = urldecode($rutaImagen);
        // Generar una clave única para el caché basada en la ruta de la imagen
        $cacheKey = 'mockup_libro_' . md5($rutaImagen);

        // Intentar recuperar la imagen del caché
        $mockupPath = Cache::remember($cacheKey, now()->addDays(365), function () use ($rutaImagen) {
            $fondoPath = storage_path("app/public/medios/mockups/seo-libros-bg.jpg");
            $sti = new StorageItem($rutaImagen);
            $libroImagenPath = $sti->path;

            $manager = new ImageManager(new Driver());

            // Crear las instancias de Image
            $fondo = $manager->read($fondoPath);
            $libro =  $manager->read($libroImagenPath);

            // Calcular las dimensiones para la portada del libro
            $altoPortada = $fondo->height() * 0.9;    // 90% del alto del fondo
            $anchoPortada = $libro->width() * ($altoPortada / $libro->height());

            // Redimensionar la portada del libro manteniendo la proporción
            $libro->resize($anchoPortada, $altoPortada);

            // Calcular la posición para centrar la portada
            $posX = ($fondo->width() - $libro->width()) / 2;
            $posY = ($fondo->height() - $libro->height()) / 2;

            // Insertar la portada en el fondo
            $fondo = $fondo->place($libro, 'top-left', $posX, $posY);

            // Generar un nombre único para el archivo de salida
            $outputPath = storage_path('app/public/mockups/' . md5($rutaImagen) . '.jpg');

            // Asegurar que el directorio existe
            File::makeDirectory(dirname($outputPath), 0755, true, true);

            // Guardar la imagen resultante
            $fondo->save($outputPath);

            // Devolver la ruta del archivo generado
            return $outputPath;
        });

        // Devolver la imagen resultante como respuesta HTTP de imagen
        return response()->file($mockupPath);
    }
}
