<?php

namespace App\Pigmalion;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToRetrieveMetadata;

class StorageItem
{
    protected $_location; // Ejemplo: /almacen/medios/equipos/1/imagen.jpg

    public function __construct($loc)
    {
        if ($loc == 'mis_archivos' || $loc == 'archivos_raiz') {
            //
        } else {
            if (substr($loc, 0, 1) != '/')
                $loc = "/" . $loc;
        }
        $this->_location = rawurldecode($loc);
    }

    public function isSpecial()
    {
        return $this->location == 'mis_archivos' || $this->location == 'archivos_raiz';
    }

    // Método mágico para acceder a propiedades
    public function __get($name)
    {
        if ($name === 'location') {
            return $this->getLocation();            // /almacen/imagenes/1.png
        }
        if ($name === 'disk') {
            return $this->getDisk();                // public
        }
        if ($name === 'path') {
            return $this->getPath();                // D:\projects\tseyor\laravel_inertia\storage\app\public\imagenes\1.png
        }
        if ($name === 'url') {
            return $this->getUrl();                 // http://localhost/almacen/imagenes/1.png
        }
        if ($name === 'urlPath') {
            return $this->getUrlPath();             // /almacen/imagenes/1.png
        }
        if ($name === 'relativeLocation') {
            return $this->getRelativeLocation();    // imagenes/1.png
        }
        return null; // O lanzar una excepción si el nombre no es válido
    }

    // Método mágico para establecer propiedades
    public function __set($name, $value)
    {
        if ($name === 'location') {
            $this->setLocation($value); // Llama al mutador
        }
    }

    // Accesor para obtener el valor de $_location
    public function getLocation()
    {
        return $this->_location; // Cambiado a _location
    }

    // Mutador para establecer el valor de $_location
    public function setLocation($loc)
    {
        // Aquí puedes agregar validaciones si es necesario
        $this->_location = $loc; // Cambiado a _location
    }

    // Accesor para obtener el valor de ruta física en disco
    public function getPath()
    {
        $path = Storage::disk($this->disk)->path($this->relativeLocation);
        return preg_replace("/[\/\\\\]/", DIRECTORY_SEPARATOR, $path);
    }

    // Accesor para obtener el valor de URL
    public function getUrl()
    {
        $disk = $this->disk;
        $i = $this->relativeLocation;
        if ($disk == 'archivos')
            $i = preg_replace("#^archivos\/#", "", $i);
        return Storage::disk($disk)->url($i);
    }


    // Accesor para obtener el valor de URL, solo la parte de path
    public function getUrlPath()
    {
        return preg_replace("/https?:\/\/[^\/]+/", "", $this->url);
    }


    // Accesor para obtener el valor de localización relativa interno
    public function getRelativeLocation()
    {
        $r = ltrim($this->_location, '/');
        if ($r == 'mis_archivos')
            return $r; // si estamos en 'mis_archivos' es un caso especial

        $disk = $this->disk;
        if ($disk == "public")
            $r =   preg_replace("#^almacen\/?#", "", $r);
        return $r;
    }


    /**
     * Create StorageItem object from full disk path
     * @param mixed $path
     * @return StorageItem object
     */
    public static function fromPath($path)
    {
        $base_public = Storage::disk('public')->path('');
        $base_archivos = Storage::disk('archivos')->path('') . 'archivos/';

        $path = str_replace('\\', '/', $path);
        $base_public = str_replace('\\', '/', $base_public);
        $base_archivos = str_replace('\\', '/', $base_archivos);

        // \Log::info("fromPath: $path", ['base_public' => $base_public, 'base_archivos' => $base_archivos]);

        if (strpos($path, $base_public) === 0) {
            $dir = substr($path, strlen($base_public));
            return new StorageItem('/almacen/' . $dir);
        }
        if (strpos($path, $base_archivos) === 0) {
            $dir = substr($path, strlen($base_archivos));
            return new StorageItem('/archivos/' . $dir);
        }
        return new StorageItem($path);
    }

    /**
     * Build location from disk and relative path
     * @param mixed $disk
     * @param mixed $relativePath
     * @return StorageItem
     */
    public static function build($disk, $relativePath)
    {
        $path = Storage::disk($disk)->path($relativePath);
        return self::fromPath($path);
    }


    /**
     * Create StorageItem object from URL
     * @param mixed $url
     * @return StorageItem object
     */
    public static function fromUrl($url)
    {
        if (!preg_match("#^https?:\/\/#", $url))
            throw new \Exception("StorageItem::fromUrl: URL no válida $url");
        $base_public = Storage::disk('public')->url('');
        $base_archivos = Storage::disk('archivos')->url('');
        if (strpos($url, $base_public) === 0) {
            $dir = substr($url, strlen($base_public));
            return new StorageItem('/almacen/' . $dir);
        }
        if (strpos($url, $base_archivos) === 0) {
            $dir = substr($url, strlen($base_archivos));
            return new StorageItem('/archivos/' . $dir);
        }
        return new StorageItem("");
    }



    /**
     * Determina el disco
     */
    public function getDisk()
    {
        $dir = ltrim($this->_location, '/'); // Asegúrate de usar _location

        if ($dir == '')
            return 'raiz';

        if ($dir == 'mis_archivos')
            return 'archivos';

        if (strpos($dir, 'archivos') === 0)
            return 'archivos';
        else if ($dir == 'mis_archivos')
            return 'archivos'; // REVISAR
        else
            return 'public';
    }


    /**
     * Storage functions
     */
    public function size()
    {
        try {
            return Storage::disk($this->disk)->size($this->relativeLocation);
        } catch (UnableToRetrieveMetadata $e) {
            return 0;
        }
    }

    public function exists()
    {
        //\Log::info("StorageItem:exists? disk: {$this->disk} relativeLocation: {$this->relativeLocation} path: {$this->path}");
        $r = Storage::disk($this->disk)->exists($this->relativeLocation);
        //\Log::info("StorageItem:exists? result: $r");
        return $r;
    }

    public function directoryExists()
    {
        return Storage::disk($this->disk)->directoryExists($this->relativeLocation);
    }

    public function files($relativeLocation = false)
    {
        if ($this->disk == 'raiz') return [];

        $files = Storage::disk($this->disk)->files($this->relativeLocation);
        if ($relativeLocation)
            return $files;
        $disk = $this->disk;
        return array_map(function ($file) use ($disk) {
            $sti = StorageItem::build($disk, $file);
            return $sti->location;
        }, $files);
    }

    public function allFiles($relativeLocation = false)
    {
        if ($this->disk == 'raiz') return [];
        $files = Storage::disk($this->disk)->allFiles($this->relativeLocation);
        if ($relativeLocation)
            return $files;
        $disk = $this->disk;
        return array_map(function ($file) use ($disk) {
            $sti = StorageItem::build($disk, $file);
            return $sti->location;
        }, $files);
    }

    public function directories($relativeLocation = false)
    {
        if ($this->disk == 'raiz') return [];
        $dirs = Storage::disk($this->disk)->directories($this->relativeLocation);
        if ($relativeLocation)
            return $dirs;
        $disk = $this->disk;
        return array_map(function ($dir) use ($disk) {
            $sti = StorageItem::build($disk, $dir);
            return $sti->location;
        }, $dirs);
    }

    public function mimeType()
    {
        return Storage::disk($this->disk)->mimeType($this->relativeLocation);
    }


    public function delete()
    {
        return Storage::disk($this->disk)->delete($this->relativeLocation);
    }

    public function deleteDirectory()
    {
        return Storage::disk($this->disk)->deleteDirectory($this->relativeLocation);
    }

    public function makeDirectory()
    {
        // \Log::info("makeDirectory : " . $this->path);
        return Storage::disk($this->disk)->makeDirectory($this->relativeLocation);
    }

    public static function move(string $from, string $to)
    {
        $f = new StorageItem($from);
        $d = new StorageItem($to);
        if ($f->disk == $d->disk)
            return Storage::disk($f->disk)->move($f->relativeLocation, $d->relativeLocation);
        return false;
    }

    public static function copy(string $from, string $to)
    {
        $f = new StorageItem($from);
        $d = new StorageItem($to);
        if ($f->disk == $d->disk)
            return Storage::disk($f->disk)->copy($f->relativeLocation, $d->relativeLocation);
        return false;
    }



    /**
     * Dada una carpeta, busca los 10 archivos más recientes en esta o subcarpetas
     *
     * retorna un array de archivos
     * */
    private function _listFilesRecursive($directorio, &$todos, &$rutasRecorridas = [])
    {
        if (in_array($directorio, $rutasRecorridas)) {
            return; // Evitar bucles infinitos
        }
        $rutasRecorridas[] = $directorio;

        if (!is_dir($directorio)) {
            return; // Si no es un directorio, salir
        }

        // Obtener todos los archivos y subdirectorios en el directorio actual
        $patron = $directorio . DIRECTORY_SEPARATOR . '*';
        $archivos = glob($patron);

        foreach ($archivos as $archivo) {
            if (is_file($archivo)) {
                $fecha_modificacion = filemtime($archivo);
                $ruta_completa = $archivo;

                $sti = StorageItem::fromPath($archivo);

                $ruta_publica = $sti->urlPath;
                $todos[] = ['archivo' => basename($ruta_publica), 'carpeta' => dirname($ruta_publica), 'url' => $ruta_publica, 'fecha_modificacion' => $fecha_modificacion];
            } else {
                $this->_listFilesRecursive($archivo, $todos, $rutasRecorridas);
            }
        }
    }

    public function lastFiles($num = 100, &$rutasRecorridas = [] )
    {
        $todos = [];
        $this->_listFilesRecursive($this->path, $todos, $rutasRecorridas);

        usort($todos, function ($a, $b) {
            return $b['fecha_modificacion'] - $a['fecha_modificacion'];
        });

        $ultimos = array_slice($todos, 0, $num);

        return $ultimos;
    }


    public function listImages(): array
    {
        $files = $this->files();

        //filtrar los archivos que son imagenes
        $images = array_filter($files, function ($file) {
            $sti = new StorageItem($file);
            $mime = $sti->mimeType();
            return strpos($mime, 'image') !== false;
        });



        return $images;
    }

    public static function ensureDirExists($path_o_location) {
        $dir = $path_o_location;
        $home = base_path();
        if(strpos($dir, $home)===0) {
            // es una ruta física
            if(!file_exists($dir))
                mkdir($dir, 0777, true);
            return;
        }

        // es una ruta tipo '/almacen'
        $sti = new StorageItem($dir);
        if(!$sti->directoryExists())
            $sti->makeDirectory();
    }

    /**
     * Guarda contenido en la ubicación de este StorageItem
     * @param string|resource $contenido
     * @param array $options Opciones para Storage::put
     * @return bool
     */
    public function put($contenido, array $options = [])
    {
        return Storage::disk($this->disk)
            ->put($this->relativeLocation, $contenido, $options);
    }

    /*public static function getPath($rutaOrig) {
        return (new StorageItem($rutaOrig))->path;
    }*/

}
