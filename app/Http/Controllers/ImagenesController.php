<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Pigmalion\DiskUtil;
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
    private function diskRuta(string $ruta): array
    {
        // si la ruta comienza por "archivos", el disco es "archivos"
        // sino, es "public"
        $ruta = $this->normalizarRuta($ruta);
        if ($ruta == '')
            return ['raiz', '']; // raiz

        if (strpos($ruta, 'archivos') === 0) {
            // $ruta = preg_replace("#^archivos\/?#", "", $ruta);
            return ['archivos', $ruta];
        } else if ($ruta == 'mis_archivos') {
            return ['archivos', $ruta];
        } else {
            return ['public', $ruta];
        }
    }

    public function size(Request $request)
    {
        $url = $request->input('url');
        if (!$url)
            abort(400, 'Debe especificar la URL de la imagen');

        // obtenemos las dimensiones de la imagen

        list($disk, $ruta) = $this->diskRuta($url);

        $fullpath = Storage::disk($disk)->path($ruta);

        // obtenemos las dimensiones de la imagen en la ubicación $fullpath, hemos de comprobar si es un PNG, o JPG...
        $info = @getimagesize($fullpath);

        // retorna respuesta json
        return response()->json(['width' => $info[0] ?? 0, 'height' => $info[1] ?? 0], 200)
            ->header('Cache-Control', 'public, max-age=2592000');
    }


    public function descargar(Request $request, $rutaImagen)
    {
        list($disk, $ruta) = DiskUtil::obtenerDiscoRuta($rutaImagen);
        $imageFullPath = Storage::disk($disk)->path($ruta);
        // dd($rutaImagen, $disk, $ruta, $imageFullPath);

        $mime = File::mimeType($imageFullPath);

        $params = $request->input();

        if (empty($params)) {
            return response()->file($imageFullPath, ['Content-Type' => $mime]);
        }

        $format = "webp";
        $quality = 70;

        // create cache path
        $cachePath = 'images/cache/';
        $cacheFilename = md5($imageFullPath . serialize($params)) . '.' . $format;
        $cacheFilePath = $cachePath . $cacheFilename;
        $cacheFullPath = Storage::disk('local')->path($cacheFilePath);

        // check if image exists in cache
        if (Storage::disk('local')->exists($cacheFilePath)) {
            $originalModifiedTime = filemtime($imageFullPath);
            $cacheModifiedTime = filemtime($cacheFullPath);

            if ($originalModifiedTime <= $cacheModifiedTime) {
                return response()->file(storage_path('app/' . $cacheFilePath), ['Content-Type' => $mime]);
            }
        }

        // create image manager with desired driver
        $manager = new ImageManager(new Driver());

        // read image from file system
        $image = $manager->read($imageFullPath);

        // Apply image transformations
        $this->transformarImagen($image, $params);

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

        return response()->file(storage_path('app/' . $cacheFilePath), ['Content-Type' => $mime]);
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

        if($width=="auto") $width = null;
        if($height=="auto") $height = null;

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
                $image = $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Aplicar redimensionado máximo si es necesario
            if ($maxWidth || $maxHeight) {
                if ($maxWidth && $image->width() > $maxWidth) {
                    $image = $image->resize($maxWidth, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }

                if ($maxHeight && $image->height() > $maxHeight) {
                    $image = $image->resize(null, $maxHeight, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            }
        }
    }
}
