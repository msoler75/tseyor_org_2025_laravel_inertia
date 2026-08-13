<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Equipo especial de Interiorización
    |--------------------------------------------------------------------------
    |
    | El equipo "Iniciados a los talleres de Interiorización" es un caso
    | especial: su slug no debe modificarse y los enlaces del front deben
    | construirse a partir de su ID, nunca de un slug hardcodeado.
    |
    */

    'interiorizacion' => [
        'id' => env('EQUIPO_INTERIORIZACION_ID', 2),
        'slug' => env('EQUIPO_INTERIORIZACION_SLUG', 'iniciados-a-los-talleres-de-interiorizacion'),
    ],

];