<?php

namespace Database\Seeders;

use App\Models\ComunicadoInteriorizacion;
use Illuminate\Database\Seeder;

class ComunicadoInteriorizacionSeeder extends Seeder
{
    /**
     * Genera comunicados de interiorización de ejemplo.
     */
    public function run(): void
    {
        ComunicadoInteriorizacion::factory()->count(50)->create();
    }
}