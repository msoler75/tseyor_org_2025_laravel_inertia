<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Pigmalion\Markdown;
use App\Pigmalion\StorageItem;

define('USE_PHPWORD', true);


/**
 * Importa el contenido de un documento Microsoft Word
 */
class WordImport
{
    public array $images = [];
    public string $zipFile = "";
    public string $content = "";

    public string $tempDir = "";
    public string $mediaFolder = "";
    public bool $deleteTempAtEnd = true;

    public $images_unique = [
        [
            "size" => 20085,
            "hash" => "630c99e306ce94dd99d173beadad247f",
            "url" => "/almacen/medios/logos/sello_tseyor_64.png",
        ],
        [
            "size" => 20204,
            "hash" => "036c30ea7e25656a87a05db130258d7a",
            "url" => "/almacen/medios/logos/sello_tseyor_64.png",
        ],
        [
            "size" => 20107,
            "hash" => "975cd4e67547113f5be9ebdef8e36bb4",
            "url" => "/almacen/medios/logos/sello_tseyor_64.png",
        ],
        [
            "size" => 20107,
            "hash" => "8881f1a43469a98601e1208bd053e45c",
            "url" => "/almacen/medios/logos/sello_tseyor_64.png",
        ],
        [
            "size" => 37029,
            "hash" => "127a9ca4f3e3cd45dd7f7d9b9bd48f0d",
            "url" => "/almacen/medios/guias/con_nombre/Noiwanak.jpg",
            "width" => "282",
            "height" => "400",
        ],
        [
            "size" => 38835,
            "hash" => "1947db3302b21d527cbd12caba54f8ee",
            "url" => "/almacen/medios/guias/con_nombre/Shilcars.jpg",
            "width" => "281",
            "height" => "400",
        ],
        [
            "size" => 42202,
            "hash" => "d52cb83b3617df4af24387ec2f566298",
            "url" => "/almacen/medios/guias/con_nombre/Shilcars.jpg",
            "width" => "281",
            "height" => "400",
        ],
        [
            "size" => 31293,
            "hash" => "2dee106d62d588a8e3b7738f2afe1d21",
            "url" => "/almacen/medios/guias/con_nombre/Rasbek.jpg",
            "width" => "286",
            "height" => "400",
        ],
        [
            "size" => 39461,
            "hash" => "18482a21b08b7f7147b729ff8751d2f2",
            "url" => "/almacen/medios/guias/con_nombre/Aumnor.jpg",
            "width" => "281",
            "height" => "400",
        ],
        [
            "size" => 50714,
            "hash" => "ce2287014bd81e0aac800f9b9a492007",
            "url" => "/almacen/medios/guias/con_nombre/AiumOm.jpg",
            "width" => "276",
            "height" => "400",
        ],
        [
            "size" => 53321,
            "hash" => "de68d7c10eeac75cc251afaec6456aad",
            "url" => "/almacen/medios/guias/con_nombre/AiumOm.jpg",
            "width" => "276",
            "height" => "400",
        ],
        [
            "size" => 34548,
            "hash" => "66c945fd1b313520ae243741ef58e7b4",
            "url" => "/almacen/medios/guias/con_nombre/Jalied.jpg",
            "width" => "276",
            "height" => "400",
        ],
        [
            "size" => 36589,
            "hash" => "919b1aef38a94ff59ed2470b211d4101",
            "url" => "/almacen/medios/guias/con_nombre/Orjain.jpg",
            "width" => "282",
            "height" => "400",
        ],
        [
            "size" => 49021,
            "hash" => "7f072a52970bf73b7c17fa8b68349aaf",
            "url" => "/almacen/medios/guias/con_nombre/Melcor.jpg",
            "width" => "282",
            "height" => "400",
        ]
    ];


    /**
     * Esta es la función principal a la que podemos llamar desde un controlador CRUD de backpack
     */
    public function __construct(string $docxFilePath = null)
    {
        if (!$docxFilePath) {

            if (!isset($_FILES['file']))
                throw new \Exception("Uploaded file not found");

            $word_file = $_FILES['file'];

            // if (!$docxFilePath)
            $docxFilePath = $this->generateUniqueFilename();

            // Copiar el archivo temporal a la nueva ubicación con la extensión correcta
            if (!copy($word_file['tmp_name'], $docxFilePath)) {
                throw new \Exception("Error al copiar nuevo archivo");
            }
        }

        if (USE_PHPWORD) {
            $this->zipFile = "";
            $this->content = Markdown::fromDocx($docxFilePath);
            $this->images = Markdown::$imagenesExtraidas;
            $this->tempDir = Markdown::$carpetaCreada;
        } else {
            $this->callToConversionService($docxFilePath);
        }

        Log::info("File imported: " . $docxFilePath);
        Log::info("Images extracted " . print_r($this->images, true));
        Log::info("Temp dir: " . $this->tempDir);
        // Log::info("Markdown converted: " . $this->content);
    }

    /**
     * Crea un nombre único de archivo para evitar conflictos
     */
    private function generateUniqueFilename()
    {
        // por defecto, tomamos el valor del campo 'file'
        $word_file = $_FILES['file'];

        // Obtener la el nombre y la extensión del archivo original
        $originalFileName = pathinfo($word_file['name'], PATHINFO_FILENAME);
        $originalExtension = pathinfo($word_file['name'], PATHINFO_EXTENSION);

        // Directorio temporal
        $tempDir = sys_get_temp_dir();

        // Generar una nueva ruta para la copia del archivo con la extensión correcta
        $docxFilePath = $tempDir . '/' . $originalFileName . '_' . uniqid() . '.' . $originalExtension;

        return $docxFilePath;
    }


    private function callToConversionService($docxFilePath)
    {
        // Obtener la URL de la variable de entorno
        $wordToMdUrl = config('services.word_to_md.url');

        if (!$wordToMdUrl)
            throw new \Exception("Servidor de conversión de Word a MD no configurado");

        // Realizar la petición al servidor para convertir el archivo .docx a markdown
        $curl = curl_init();

        $postData = [
            'file' => curl_file_create($docxFilePath)
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $wordToMdUrl,
            CURLOPT_RETURNTRANSFER => true,
            // CURLOPT_VERBOSE => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // Verificar el código de respuesta HTTP
        if ($httpCode === 200) {

            // Directorio temporal para almacenar el archivo ZIP
            $tempDir = sys_get_temp_dir();

            // Generar un nombre único para el archivo ZIP
            $zipFilePath = tempnam($tempDir, 'import_') . '.zip';

            // Guardar la respuesta en un archivo ZIP
            file_put_contents($zipFilePath, $response);

            // Descomprimir el archivo ZIP
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath) === true) {
                // Extraer el archivo content.md
                $contentMd = $zip->getFromName('output.md');

                // Extraer las imágenes de la carpeta 'medios'
                $mediaFolder = 'medios/';
                $extractedImages = array();
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (strpos($filename, $mediaFolder) === 0) {
                        $extractedImages[] = $tempDir . '/' . $filename;
                        $zip->extractTo($tempDir, $filename);
                    }
                }

                $zip->close();

                // rellenamos los atributos con toda la información
                $this->zipFile = $zipFilePath;
                $this->content = $contentMd;
                $this->images = $extractedImages;
            } else {
                throw new \Exception("Error al descomprimir el .zip");
            }
        } else {
            // Mostrar información sobre el error
            $error = curl_error($curl);
            throw new \Exception($error . " " . $response);
        }
    }



    /**
     * Liberamos los archivos temporales
     */
    public function __destruct()
    {
        // borramos los archivos temporales
        if ($this->deleteTempAtEnd)
            $this->cleanTempFiles();
    }


    /**
     * Borra los archivos temporales de operaciones de extracción de zip
     */
    public function cleanTempFiles()
    {
        // Eliminar los archivos y carpetas temporales
        if ($this->zipFile)
            @unlink($this->zipFile);
        if ($this->images)
            foreach ($this->images as $imagePath) {
                @unlink($imagePath);
            }
        if ($this->tempDir) {
            if (Storage::disk('public')->exists($this->tempDir)) {
                Log::info("go to delete folder " . $this->tempDir);
                // we must delete files in folder first
                WordImport::deleteFilesFromFolder($this->tempDir);

                $path = Storage::disk('public')->path($this->tempDir);
                // then we delete folder
                @rmdir($path);
            }
        }
        // @unlink($tempDir . '/output.md');
    }


    /**
     * Borra los archivos de una carpeta en el disco 'public'
     */
    public static function deleteFilesFromFolder($folder)
    {
        // Verificar si la carpeta existe en el disco 'public'
        if (Storage::disk('public')->exists($folder)) {

            // por seguridad, no se permiten rutas relativas extrañas
            if (strpos($folder, "..") !== FALSE)
                return;

            // por seguridad, no se permiten rutas absolutas
            if (strpos($folder, "/") == 0)
                return;

            // Obtener la ruta completa de la carpeta de destino en el disco 'public'
            $destinationFolderPath = Storage::disk('public')->path($folder);

            // eliminamos todas las imagenes de esta carpeta:
            // Obtener la lista de archivos en la carpeta
            $files = glob($destinationFolderPath . '/*');

            // Iterar sobre los archivos y eliminarlos
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }


    /**
     * @param deletePrevious = true: borramos los archivos previos en la carpeta de destino
     */
    public function copyImagesTo(string $folderDest, bool $deletePrevious = false)
    {
        if (!count($this->images))
            return 0;

        $sti = new StorageItem($folderDest);
        $disk = $sti->disk;
        $folder = $sti->relativeLocation;

        Log::info("copyImagesTo disk: {$disk}, folder: {$folder}");

        $this->mediaFolder = $folder;

        if ($deletePrevious) {
            // Borramos las imágenes que pudiera haber, previas
            // (Se asume que disk es public siempre!!?)
            WordImport::deleteFilesFromFolder($folder);
        }

        // Obtener la ruta completa de la carpeta de destino en el disco 'public'
        // $destinationFolderPath = storage_path('app/public/' . $folder);
        $destinationFolderPath = $sti->path;

        Log::info("destinationFolderPath: $destinationFolderPath");

        // Verificar si la carpeta existe en el disco 'public'
        StorageItem::ensureDirExists($folderDest);

        Log::info("images: " . print_r($this->images, true));

        // Copiamos las imágenes a la carpeta de destino
        foreach ($this->images as $imagePath) {
            $imageFilename = basename($imagePath);
            // die("c.id={$comunicado->id};tempDir=$tempDir; image=$image; imageFileName=$imageFilename; dest=".public_path("almacen/".$destinationFolder . "/" .  $imageFilename));

            // aquí busca imagenes que sean habituales en muchos documentos, de los guías o el sello de tseyor
            // y las reemplaza por una versión única de cada imagen
            $img = $this->equivalent_image($imagePath);
            if ($img != null) {
                Log::info("encontrada imagen equivalente en " . $img['url']);
                $params = [];
                if (isset($img["width"]))
                    $params[] = "width=" . $img["width"];
                if (isset($img["height"]))
                    $params[] = "height=" . $img["height"];
                $extra = count($params) ? "{" . implode(",", $params) . "}" : "";

                $this->content = preg_replace("#\/?almacen/{$this->tempDir}/$imageFilename\)#", $img['url'] . ")" . $extra, $this->content);
            } else {
                Log::info("copy $imagePath -> $destinationFolderPath/$imageFilename");
                copy($imagePath, $destinationFolderPath . "/" . $imageFilename);
            }
        }

        Log::info("temp dir: $this->tempDir");

        Log::info("content: " . print_r($this->content, true));

        if (USE_PHPWORD) {
            Log::info("preg_replace(almacen/{$this->tempDir}/   ->   almacen/$folder/");
            $this->content = preg_replace("#\balmacen/{$this->tempDir}/#", "almacen/$folder/", $this->content);

            Log::info("content after: " . print_r($this->content, true));
        } else {
            $this->content = preg_replace("/\bmedios\//", "$folder/", $this->content);
            $this->content = preg_replace("/\.\/medios\//", "/almacen/medios/", $this->content);
        }

        // hacemos los enlaces locales
        $baseHost = config('app.url');
        $this->content = preg_replace("#$baseHost/almacen/#", "/almacen/", $this->content);

        Log::info("content final: " . print_r($this->content, true));

        return count($this->images);
    }

    /**
     * Busca una imagen equivalente
     * Si la encuentra, retorna la url de su ubicación
     */

    private function equivalent_image($imagePath)
    {
        $size = filesize($imagePath);
        foreach ($this->images_unique as $img)
            if ($size == $img['size'] && md5_file($imagePath) == $img['hash'])
                return $img;

        return null;
    }

}
