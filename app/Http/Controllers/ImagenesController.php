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
        $r = self::info([$url]);

        $info = $r[$url];

        if(!$info)
        // retorna respuesta json
        return response()->json(['error' => 'No se encontró la imagen'], 404)
        // expira en 1 minuto
        ->header('Expires', gmdate('D, d M Y H:i:s', time() + 60 ) . ' GMT');

        // retorna respuesta json
        return response()->json(['width' => $info[0] ?? -1, 'height' => $info[1] ?? -1], 200)
        // cache de un año que no tenga que revalidarse:
            ->header('Cache-Control', 'public, max-age=2592000')
            // expira en 1 año
            ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
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


    // devuelve la información de las dimensiones de un array de imágenes
    // usaremos doble cache: una cache individual para cada imagen, y una cache para cada grupo de imágenes

    // info para una imagen dada una url
    private static function info1(string $url)
    {
        $cache_key = "imagen_info_".$url;

        return Cache::remember($cache_key, now()->addDays(365), function () use ($url) {
            $sti = new StorageItem($url);
            $fullpath = $sti->path;
            // obtenemos las dimensiones de la imagen en la ubicación $fullpath
            $info = @getimagesize($fullpath);
            return $info;
        });
    }

    public static function info(array $imagenes)
    {
        if (count($imagenes) == 0) return [];
        if (count($imagenes) == 1) return [$imagenes[0] => self::info1($imagenes[0])];

        $group_id = md5(json_encode($imagenes));
        $cache_key = "imagenes_info_group_" . $group_id;

        return Cache::remember($cache_key, now()->addDays(365), function () use ($imagenes) {

            $r = [];
            foreach ($imagenes as $url) {
                $info = ImagenesController::info1($url);
                if ($info)
                    $r[$url] = $info;
            }

            return $r;
        });
    }
}
