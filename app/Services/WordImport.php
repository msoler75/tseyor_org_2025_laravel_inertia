<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Pigmalion\Markdown;
use App\Pigmalion\DiskUtil;

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


    /**
     * Esta es la función principal a la que podemos llamar desde un controlador CRUD de backpack
     */
    public function __construct(string $docxFilePath = null)
    {
        if (!isset ($_FILES['file']))
            throw new \Exception("Uploaded file not found");

        $word_file = $_FILES['file'];

        if (!$docxFilePath)
            $docxFilePath = $this->generateUniqueFilename();

        // Copiar el archivo temporal a la nueva ubicación con la extensión correcta
        if (!copy($word_file['tmp_name'], $docxFilePath)) {
            throw new \Exception("Error al copiar nuevo archivo");
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
            if(Storage::disk('public')->exists($this->tempDir)) {
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

        Log::info("copyImagesTo ".$folderDest);

        if (!count($this->images))
            return 0;

        list($disk, $folder) = DiskUtil::obtenerDiscoRuta($folderDest);

        Log::info("copyImagesTo disk: $disk, folder: $folder");

        $this->mediaFolder = $folder;

        if ($deletePrevious) {
            // Borramos las imágenes que pudiera haber, previas
            WordImport::deleteFilesFromFolder($folder);
        }

        // Obtener la ruta completa de la carpeta de destino en el disco 'public'
        // $destinationFolderPath = storage_path('app/public/' . $folder);
        $destinationFolderPath = Storage::disk($disk)->path($folder);

        Log::info("destinationFolderPath: $destinationFolderPath");

        // Verificar si la carpeta existe en el disco 'public'
        if (!Storage::disk($disk)->exists($folder)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk($disk)->makeDirectory($folder);
        }

        Log::info("images: ".print_r($this->images, true));

        // Copiamos las imágenes a la carpeta de destino
        foreach ($this->images as $imagePath) {
            $imageFilename = basename($imagePath);
            // die("c.id={$comunicado->id};tempDir=$tempDir; image=$image; imageFileName=$imageFilename; dest=".public_path("almacen/".$destinationFolder . "/" .  $imageFilename));
            Log::info("copy $imagePath -> $destinationFolderPath/$imageFilename");
            copy($imagePath, $destinationFolderPath . "/" . $imageFilename);
        }

        Log::info("temp dir: $this->tempDir");

        Log::info("content: ". print_r($this->content, true));

        if (USE_PHPWORD) {
            Log::info("preg_replace(almacen/{$this->tempDir}/   ->   almacen/$folder/");
            $this->content = preg_replace("#\balmacen/{$this->tempDir}/#", "almacen/$folder/", $this->content);

            Log::info("content after: ". print_r($this->content, true));
        } else {
            $this->content = preg_replace("/\bmedios\//", "$folder/", $this->content);
            $this->content = preg_replace("/\.\/medios\//", "/almacen/medios/", $this->content);
        }

        // hacemos los enlaces locales
        $baseHost = config('app.url');
        $this->content = preg_replace("#$baseHost/almacen/#", "/almacen/", $this->content);
        Log::info("content final: ". print_r($this->content, true));

        return count($this->images);
    }

}
