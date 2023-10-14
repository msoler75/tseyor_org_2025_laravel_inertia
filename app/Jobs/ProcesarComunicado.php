<?php

namespace App\Jobs;

use App\Models\Comunicado;
use App\Services\AudioConverter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $inputFile = "";
        $outputFile = "";

        $converter = new AudioConverter($inputFile, $outputFile);
        $converter->convert();
    }
}
