<?php

// use Illuminate\Http\Response;

// archivo a enviar
$docxFilePath = __DIR__ . "/word_file.docx";

// Obtener la URL de la variable de entorno
$wordToMdUrl = "http://localhost:8080";

if (!$wordToMdUrl)
    die("Servidor de conversión no configurado");

// Realizar la petición al servidor para convertir el archivo .docx a markdown
$mime = mime_content_type($docxFilePath);
$postfields = ['file' =>
//new CURLFile($docxFilePath, $mime, basename($docxFilePath))
curl_file_create($docxFilePath)
];

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $wordToMdUrl,
    CURLOPT_RETURNTRANSFER => true,
    // CURLOPT_VERBOSE => true,
    CURLOPT_POST => true,

    CURLOPT_POSTFIELDS => $postfields,
    /* CURLOPT_HTTPHEADER => array(
        'Accept: application/form-data',
        'User-Agent' => 'Laravel App Client (https://www.tseyor.org)',
        // 'Content-Type: multipart/form-data',
    ) */
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

// Verificar el código de respuesta HTTP
if ($httpCode === 200) {
    // Guardar la respuesta en un archivo ZIP
    file_put_contents($outputFilePath, $response);

    // Descomprimir el archivo ZIP
    $zip = new \ZipArchive();
    if ($zip->open($outputFilePath) === true) {
        // Extraer el archivo content.md
        $contentMd = $zip->getFromName('output.md');
        return "contenido:" . $contentMd;

        // Extraer las imágenes de la carpeta 'media'
        $mediaFolder = 'medios/';
        $extractedImages = array();
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (strpos($filename, $mediaFolder) === 0) {
                $extractedImages[] = $filename;
                $zip->extractTo($tempDir, $filename);
            }
        }

        $zip->close();

        // Copiar las imágenes a la carpeta de destino
        $destinationFolder = 'ruta/destino/';
        foreach ($extractedImages as $image) {
            $imageFilename = basename($image);
            copy($tempDir . '/' . $image, $destinationFolder . $imageFilename);
        }

        // Leer el contenido del archivo content.md
        $texto = file_get_contents($tempDir . '/output.md');

        // Realizar más acciones con $texto y las imágenes copiadas

        // Eliminar los archivos y carpetas temporales
        @unlink($outputFilePath);
        foreach ($extractedImages as $image) {
            @unlink($tempDir . '/' . $image);
        }
        @unlink($tempDir . '/output.md');

        die("ok");
    } else {
        die($response);
    }
} else {
    // Mostrar información sobre el error
    $error = curl_error($curl);
    $verboseInfo = curl_multi_getcontent($curl);
    die("Error en la solicitud cURL: " . $error . ' response:' . $response . ' v:' . $verboseInfo);
}

die("unexpected end");
