<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        // dd($this->source, $this->destination);
    }


    public function convert()
    {
        try {
            // Obtener la URL de la variable de entorno
            $converterUrl = config('services.audio_converter.url');

            if (!$converterUrl)
                throw new \Exception("Servidor de conversión de audio no configurado");

            Log::info("Inicia la conversión del archivo '{$this->source}' a '{$this->destination}'");

            $frecuencia = config('services.audio_converter.frecuencia');
            $kbps = config('services.audio_converter.kbps');

            // Realizar la petición al servidor para convertir el archivo .docx a markdown
            $curl = curl_init();

            $sourcePath = Storage::disk('public')->path($this->source);
            $destinationPath = Storage::disk('public')->path($this->destination);

            // Verificar si el archivo existe
            if (!Storage::disk('public')->exists($this->source))
                throw new \Exception("El archivo {$this->source} no existe en el disco de almacenamiento");

            // Realizar la petición al servidor para convertir el archivo .docx a markdown
            $curl = curl_init();

            $postData = [
                'file' => curl_file_create($sourcePath)
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

                // Verificar si la carpeta existe en el disco 'public'
                if (!Storage::disk('public')->exists(dirname($this->destination))) {
                    // Crear la carpeta en el disco 'public'
                    Storage::disk('public')->makeDirectory(dirname($this->destination));
                }

                // Guardar la respuesta en el destino
                file_put_contents($destinationPath, $response);
                Log::info('La conversión del archivo se realizó exitosamente.');

            } else {
                // Mostrar información sobre el error
                $error = curl_error($curl);
                throw new \Exception($error . " " . $response);
            }
        } catch (\Exception $exception) {
            Log::error('Error al convertir el archivo: ' . $exception->getMessage());
            throw $exception;
        }
    }
}
