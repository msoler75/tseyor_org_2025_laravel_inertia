<?php

namespace Tests;

use Illuminate\Support\Facades\Log;
use App\Pigmalion\StrEx;

class BenchmarkAccentRemover
{
    /**
     * Para ver cual es más rápido
     */

    public static function benchmark($texto)
    {
        $reps = 100;


        // Función 1: removerAcentosIconv
        $start = microtime(true);
        for ($i = 0; $i < $reps; $i++) {
            StrEx::removerAcentosIconv($texto);
        }
        $end = microtime(true);
        $time1 = $end - $start;

        // Función 2: removerAcentosStrtr
        $start = microtime(true);
        for ($i = 0; $i < $reps; $i++) {
            StrEx::removerAcentosStrtr($texto);
        }
        $end = microtime(true);
        $time2 = $end - $start;

        // Función 3: removerAcentosRegex
        $start = microtime(true);
        for ($i = 0; $i < $reps; $i++) {
            StrEx::removerAcentosRegex1($texto);
        }
        $end = microtime(true);
        $time3 = $end - $start;

        // Función 3: removerAcentosRegex
        $start = microtime(true);
        for ($i = 0; $i < $reps; $i++) {
            StrEx::removerAcentosRegex2($texto);
        }
        $end = microtime(true);
        $time4 = $end - $start;


        // Función 4: removerAcentosEreg
        $start = microtime(true);
        for ($i = 0; $i < $reps; $i++) {
            StrEx::removerAcentosEreg($texto);
        }
        $end = microtime(true);
        $time5 = $end - $start;

        // Resultados
        Log::info("Tiempo removerAcentosIconv: " . $time1 . " segundos");
        Log::info("Tiempo removerAcentosStrtr: " . $time2 . " segundos");
        Log::info("Tiempo removerAcentosRegx1: " . $time3 . " segundos");
        Log::info("Tiempo removerAcentosRegx2: " . $time4 . " segundos");
        Log::info("Tiempo removerAcentosEreg : " . $time5 . " segundos");
    }

}
