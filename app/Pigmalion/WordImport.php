<?php

namespace App\Pigmalion;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Contenido;

/**
 * Importa el contenido de un documento Microsoft Word
 */
class WordImport
{
    /**
     * Se le pasa normalmente $_FILES['file']
     */
    public static function fromFormFile(array $word_file)
    {
        // Directorio temporal para almacenar el archivo ZIP
        $tempDir = sys_get_temp_dir();

        // Generar un nombre único para el archivo ZIP
        $zipFilePath = tempnam($tempDir, 'import_') . '.zip';

        // Obtener la el nombre y la extensión del archivo original
        $originalFileName = pathinfo($word_file['name'], PATHINFO_FILENAME);
        $originalExtension = pathinfo($word_file['name'], PATHINFO_EXTENSION);

        // Generar una nueva ruta para la copia del archivo con la extensión correcta
        $docxFilePath = $tempDir . '/' . $originalFileName . '_'. uniqid() . '.' . $originalExtension;

        // Copiar el archivo temporal a la nueva ubicación con la extensión correcta
        if (!copy($word_file['tmp_name'], $docxFilePath)) {
            throw new \Exception("Error al copiar nuevo archivo");
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
            // Guardar la respuesta en un archivo ZIP
            file_put_contents($zipFilePath, $response);

            // Descomprimir el archivo ZIP
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath) === true) {
                // Extraer el archivo content.md
                $contentMd = $zip->getFromName('output.md');
                // dd($contentMd);

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

                return ['zipFile' => $zipFilePath, 'content' => $contentMd, 'images' => $extractedImages];
            } else {
                throw new \Exception("Error al descomprimir el .zip");
            }
        } else {
            // Mostrar información sobre el error
            $error = curl_error($curl);
            throw new \Exception($error . " " .  $response);
        }
    }

    public static  function cleanTempFiles($zipFilePath, $extractedImages)
    {
         // Directorio temporal para almacenar el archivo ZIP
         $tempDir = sys_get_temp_dir();

        // Eliminar los archivos y carpetas temporales
        @unlink($zipFilePath);
        foreach ($extractedImages as $image) {
            @unlink($tempDir . '/' . $image);
        }
        // @unlink($tempDir . '/output.md');
    }
}
