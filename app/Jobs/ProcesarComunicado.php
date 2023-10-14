<?php

namespace App\Jobs;

use App\Models\Comunicado;
use Illuminate\Support\Facades\Bus;
use App\Services\AudioConverter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Procesa los audios y el pdf de un comunicado (si es necesario)
 */
class ProcesarComunicado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Comunicado $comunicado;

    /**
     * Create a new job instance.
     */
    public function __construct(public Comunicado $c)
    {
        $this->comunicado = $c;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch ($this->comunicado->categoria) {
            case 1:
                $tipo = "TAP";
                break;
            case 2:
                $tipo = "DDM";
                break;
            case 3:
                $tipo = "MUUL";
                break;
        }

        $fecha = date('ymd', strtotime($this->comunicado->fecha_comunicado));
        $audios = json_decode($this->comunicado->audios, true);
        $multiple = count($audios) > 1;

        $audios_pendientes = 0;
        $audios_procesados = 0;
        $idx = 0;
        foreach ($audios as $key => $audio) {
            if ($this->mustBeProcessed($audio)) {
                $audios_pendientes++;
                if ($audios_procesados == 0) // solo procesa 1 audio cada vez, ya que es muy costoso
                {
                    $outputFile = "TSEYOR $fecha ({$this->comunicado->num})" . $tipo . ($multiple ? " " . ('a' + $idx) : "") . ".mp3";

                    $converter = new AudioConverter($audio, $outputFile);
                    $converter->convert();

                    $audios[$key] = $outputFile;

                    $audios_procesados++;
                    $audios_pendientes--;
                }
            }

            $idx++;
        }

        if ($audios_procesados > 0) {
            $this->comunicado->audios = json_encode($audios);
            $this->comunicado->save();
        }

        if ($audios_pendientes > 0) {
            // Encolar la tarea nuevamente
            Bus::dispatch(new self($this->comunicado));
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
