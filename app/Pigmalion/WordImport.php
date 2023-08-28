<?php

namespace App\Pigmalion;

use Illuminate\Support\Facades\Storage;

/**
 * Importa el contenido de un documento Microsoft Word
 */
class WordImport
{
    public array $images = [];
    public string $zipFile = "";
    public string $content = "";
    public string $mediaFolder = "";
    public string $mediaFolderPath = "";
    public bool $deleteTempAtEnd = true;

    /**
     * Esta es la función principal a la que podemos llamar desde un controlador CRUD de backpack
     */
    public function __construct(string $docxFilePath = null)
    {
        // Directorio temporal para almacenar el archivo ZIP
        $tempDir = sys_get_temp_dir();

        if (!$docxFilePath) {

            // por defecto, tomamos el valor del campo 'file'
            $word_file = $_FILES['file'];

            // Obtener la el nombre y la extensión del archivo original
            $originalFileName = pathinfo($word_file['name'], PATHINFO_FILENAME);
            $originalExtension = pathinfo($word_file['name'], PATHINFO_EXTENSION);

            // Generar una nueva ruta para la copia del archivo con la extensión correcta
            $docxFilePath = $tempDir . '/' . $originalFileName . '_' . uniqid() . '.' . $originalExtension;

            // Copiar el archivo temporal a la nueva ubicación con la extensión correcta
            if (!copy($word_file['tmp_name'], $docxFilePath)) {
                throw new \Exception("Error al copiar nuevo archivo");
            }
        }

        // Obtener la URL de la variable de entorno
        $wordToMdUrl = env('WORD_TO_MD_URL');

        if (!$wordToMdUrl)
            throw new \Exception("Servidor de conversión no configurado");

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

             // Generar un nombre único para el archivo ZIP
            $zipFilePath = tempnam($tempDir, 'import_') . '.zip';

            // Guardar la respuesta en un archivo ZIP
            file_put_contents($zipFilePath, $response);

            // Descomprimir el archivo ZIP
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath) === true) {
                // Extraer el archivo content.md
                $contentMd = $zip->getFromName('output.md');

                // Extraer las imágenes de la carpeta 'media'
                $mediaFolder = 'media/';
                $extractedImages = array();
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (strpos($filename, $mediaFolder) === 0) {
                        $extractedImages[] = $filename;
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
            throw new \Exception($error . " " .  $response);
        }
    }



    /**
     * Liberamos los archivos temporales
     */
    public function __destruct()
    {
        // borramos los archivos temporales
        if($this->deleteTempAtEnd)
            $this->cleanTempFiles();
    }


    /**
     * Borra los archivos temporales de operaciones de extracción de zip
     */
    public function cleanTempFiles()
    {
        // Directorio temporal para almacenar el archivo ZIP
        $tempDir = sys_get_temp_dir();

        // Eliminar los archivos y carpetas temporales
        if($this->zipFile)
            @unlink($this->zipFile);
        if($this->images)
        foreach ($this->images as $image) {
            @unlink($tempDir . '/' . $image);
        }
        // @unlink($tempDir . '/output.md');
    }


    /**
     * Borra los archivos de una carpeta
     */
    public static function deleteFilesFromFolder($folder)
    {
        // Verificar si la carpeta existe en el disco 'public' y crearla si hace falta
        if (Storage::disk('public')->exists($folder)) {

            // por seguridad, no se permiten rutas relativas extrañas
            if (strpos($folder, "..") !== FALSE)
                return;

            // por seguridad, no se permiten rutas absolutas
            if (strpos($folder, "/") == 0)
                return;

            // Obtener la ruta completa de la carpeta de destino en el disco 'public'
            $destinationFolderPath = storage_path('app/public/' . $folder);

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


    public function copyImagesTo($imagesDestinationFolder, $deletePrevious = false)
    {
        if(!count($this->images))
            return 0;

        $this->mediaFolder = $imagesDestinationFolder;

        if ($deletePrevious) {
            // Borramos las imágenes que pudiera haber, previas
            WordImport::deleteFilesFromFolder($imagesDestinationFolder);
        }

        // Directorio temporal
        $tempDir = sys_get_temp_dir();

        // Obtener la ruta completa de la carpeta de destino en el disco 'public'
        $destinationFolderPath = storage_path('app/public/' . $imagesDestinationFolder);

        // Verificar si la carpeta existe en el disco 'public'
        if (!Storage::disk('public')->exists($imagesDestinationFolder)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk('public')->makeDirectory($imagesDestinationFolder);
        }

        // Copiamos las imágenes a la carpeta de destino
        foreach ($this->images as $image) {
            $imageFilename = basename($image);
            // die("c.id={$comunicado->id};tempDir=$tempDir; image=$image; imageFileName=$imageFilename; dest=".public_path("storage/".$destinationFolder . "/" .  $imageFilename));
            copy($tempDir . '/' . $image, $destinationFolderPath . "/" .  $imageFilename);
        }

        return count($this->images);
    }
}
