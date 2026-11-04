<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Slugs temáticos de la filosofía → filosofia=true + orden de portada.
     * Solo actualiza las filas que existan (no-op silencioso para el resto).
     */
    private array $temas = [
        'el-rayo-sincronizador' => 10,
        'las-sociedades-armonicas' => 20,
        'confederacion-de-mundos-habitados-de-la-galaxia' => 30,
        'la-autoobservacion' => 40,
        'salto-cuantico' => 50,
        'especializacion' => 51,
        'retroalimentacion' => 52,
        'espejos' => 53,
    ];

    public function up(): void
    {
        foreach ($this->temas as $ruta => $orden) {
            DB::table('paginas')->where('ruta', $ruta)->update([
                'filosofia' => true,
                'orden' => $orden,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('paginas')->whereIn('ruta', array_keys($this->temas))->update([
            'filosofia' => false,
            'orden' => null,
        ]);
    }
};
