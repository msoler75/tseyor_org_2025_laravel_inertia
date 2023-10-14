<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Importa el contenido de un documento Microsoft Word
 */
class AudioConverter
{
    public string $source;
    public string $destination;

    /**
     * Esta es la función principal a la que podemos llamar desde un controlador CRUD de backpack
     */
    public function __construct(string $audioFilePath = null, string $destinationFilePath)
    {
        $this->source = $audioFilePath;
        $this->destination = $destinationFilePath;
    }


    public function convert()
    {
        // Obtener la URL de la variable de entorno
        $converterUrl = config('services.audio_converter.url');

        if (!$converterUrl)
            throw new \Exception("Servidor de conversión de audio no configurado");

        $frecuencia = config('services.audio_converter.frecuencia');
        $kbps = config('services.audio_converter.kbps');

        // Realizar la petición al servidor para convertir el archivo .docx a markdown
        $curl = curl_init();

        $postData = [
            'file' => curl_file_create($this->source)
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $converterUrl . "?frecuencia={$frecuencia}&kbps={$kbps}",
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
             // Guardar la respuesta en el destino
             file_put_contents($this->destination, $response);

         } else {
            // Mostrar información sobre el error
            $error = curl_error($curl);
            throw new \Exception($error . " " .  $response);
        }
    }
}
