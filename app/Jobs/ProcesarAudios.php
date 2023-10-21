<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Bus;
use App\Services\AudioConverter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ContenidoConAudios;

/**
 * Procesa los audios y el pdf de un comunicado (si es necesario)
 */
class ProcesarAudios implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ContenidoConAudios $contenido)
    {
        $this->contenido = $contenido;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $audios_pendientes = 0;
        $audios_procesados = 0;
        $index = 0;

        $folder = $this->contenido->generarRutaAudios();

        // $folder = "media/informes/{$equipo->slug}/$año";
        foreach ($this->contenido->audios as $key => $audio) {
            if ($this->mustBeProcessed($audio)) {
                $audios_pendientes++;
                if ($audios_procesados == 0) // solo procesa 1 audio cada vez, ya que es muy costoso
                {

                    $outputFile = $this->contenido->generarNombreAudio($index);

                    // $outputFile = "$equipo $fecha $tipo" . ($multiple ? " " . ('a' + $idx) : "") . ".mp3";
                    $outputFilePath = $folder . "/" . $outputFile;
                    $converter = new AudioConverter($audio, $outputFilePath);
                    $converter->convert();

                    $audios[$key] = $outputFilePath;

                    $audios_procesados++;
                    $audios_pendientes--;
                }
            }

            $index++;
        }

        if ($audios_procesados > 0) {
            $this->contenido->audios = json_encode($audios);
            $this->contenido->save();
        }

        if ($audios_pendientes > 0) {
            // Encolar la tarea nuevamente
            Bus::dispatch(new self($this->contenido));
        }
    }


    /**
     * return true si el audio está aun sin convertir (está en carpeta 'upload')
     */
    private function mustBeProcessed($audio)
    {
        return preg_match("#upload\/#", $audio);
    }

}
