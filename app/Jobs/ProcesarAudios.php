<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
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

    private ContenidoConAudios $contenido; // modelo
    private string $carpeta; // path donde se guardarn los audios
    private string $disk; // disco donde se guardan los audios


    /**
     * Create a new job instance.
     */
    public function __construct( ContenidoConAudios $contenido,  string $carpeta = null,  string $disk = 'public')
    {
        $this->contenido = $contenido;
        $this->carpeta = $carpeta;
        $this->disk = $disk;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $audios_pendientes = 0;
        $audios_procesados = 0;
        $index = 0;

        $audios = $this->contenido->audios;
        if($this->carpeta)
            $folder = $this->carpeta;
        else {
            $modelName = $this->contenido->getMorphClass();
            $año = $this->contenido->created_at->year;
            $carpeta = "/almacen/medios/$modelName/$año";
            $folder = $carpeta;
        }

        Log::channel('jobs')->info("handle audios". var_export($this->contenido->audios, true));
        Log::channel('jobs')->info("folder: ". $folder);

        // $folder = "medios/informes/{$equipo->slug}/$año";
        foreach ($audios as $key => $audio) {
            if ($this->mustBeProcessed($audio)) {
                $audios_pendientes++;
                if ($audios_procesados == 0) // solo procesa 1 audio cada vez, ya que es muy costoso
                {
                    $outputFile = $this->contenido->generarNombreAudio($index);
                    $outputFilePath = $folder . "/" . $outputFile;
                    $converter = new AudioConverter($audio, $outputFilePath, $this->disk);
                    $converter->convert();

                    $audios[$key] = $outputFilePath;

                    $audios_procesados++;
                    $audios_pendientes--;
                }
            }

            $index++;
        }

        if ($audios_procesados > 0) {
            Log::channel('jobs')->info("audios_procesados: $audios_procesados ");
            // $this->contenido->audios = json_encode($audios);
            // $this->contenido->save();
            $this->contenido->update(['audios'=>$audios]);
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
        Log::channel('jobs')->info("MustBeProcessed? ". var_export($audio, true));
        return preg_match("#^upload\/#", $audio);
    }

}
