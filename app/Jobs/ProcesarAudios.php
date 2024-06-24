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

    private int $contenidoId; // ID del modelo
    private string $contenidoClass; // Clase del modelo
    private string $carpeta; // path donde se guardarn los audios
    private string $disk; // disco donde se guardan los audios


    /**
     * Create a new job instance.
     */
    public function __construct(string $contenidoClass, int $contenidoId, string $carpeta = null, string $disk = 'public')
    {
        $this->contenidoClass = $contenidoClass;
        $this->contenidoId = $contenidoId;
        $this->carpeta = $carpeta;
        $this->disk = $disk;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // Cargar el modelo usando la clase y el ID
        $contenido = $this->contenidoClass::find($this->contenidoId);

        // Verificar si el modelo existe antes de proceder
        if (!$contenido) {
            Log::channel('jobs')->warning("El modelo {$this->contenidoClass} con id {$this->contenidoId} no existe. Se termina la tarea.");
            return;
        }

        $audios_pendientes = 0;
        $audios_procesados = 0;
        $index = 0;

        $audios = $contenido->audios;
        if ($this->carpeta)
            $folder = $this->carpeta;
        else {
            $modelName = $contenido->getMorphClass();
            $año = $contenido->created_at->year;
            $carpeta = "/almacen/medios/$modelName/$año";
            $folder = $carpeta;
        }

        Log::channel('jobs')->info("handle audios" . var_export($contenido->audios, true));
        Log::channel('jobs')->info("folder: " . $folder);

        // $folder = "medios/informes/{$equipo->slug}/$año";
        foreach ($audios as $key => $audio) {
            if ($this->mustBeProcessed($audio)) {
                $audios_pendientes++;
                if ($audios_procesados == 0) // solo procesa 1 audio cada vez, ya que es muy costoso
                {
                    $outputFile = $contenido->generarNombreAudio($index);
                    $outputFilePath = $folder . "/" . $outputFile;
                    $converter = new AudioConverter($audio, $outputFilePath, $this->disk);
                    try {
                        $converter->convert();
                        $audios[$key] = $outputFilePath;
                        $audios_procesados++;
                        $audios_pendientes--;
                    } catch (\Exception $e) {
                        // no se pudo procesar el audio
                    }
                }
            }

            $index++;
        }

        if ($audios_procesados > 0) {
            Log::channel('jobs')->info("audios_procesados: $audios_procesados ");
            // $contenido->audios = json_encode($audios);
            // $contenido->save();
            $contenido->update(['audios' => $audios]);
        }

        if ($audios_pendientes > 0) {
            // Encolar la tarea nuevamente
            Log::info("Quedan $audios_pendientes audios pendientes de tratar. Reencolamos");
            Bus::dispatch(new self($contenido::class, $contenido->id, $folder, $this->disk));
        }
    }


    /**
     * return true si el audio está aun sin convertir (está en carpeta 'upload')
     */
    private function mustBeProcessed($audio)
    {
        Log::channel('jobs')->info("MustBeProcessed? " . var_export($audio, true) . ' ' . (preg_match("#^upload\/#", $audio) ? "true" : "false"));
        return preg_match("#^upload\/#", $audio);
    }


    // Método __toString para evitar el error
    public function __toString()
    {
        return str_replace("App\\Models\\", "", $this->contenidoClass) . " id:" . $this->contenidoId;
    }
}
