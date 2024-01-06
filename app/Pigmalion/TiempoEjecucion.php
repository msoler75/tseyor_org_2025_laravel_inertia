<?php

namespace App\Pigmalion;

use Illuminate\Support\Facades\Log;

class TiempoEjecucion
{
    private $startTime;
    private $string;

    public function __construct($string)
    {
        $this->startTime = microtime(true);
        $this->string = $string;
    }

    public function time()
    {
        $endTime = microtime(true);
        $totalDuration = $endTime - $this->startTime;

        // Registrar el tiempo transcurrido en el registro de Laravel
        Log::info($this->string . ': ' . $totalDuration . ' segundos');

        return $totalDuration;
    }

    public function __destruct()
    {
        $this->time();
    }
}
