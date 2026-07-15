<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::table('paginas')
            ->where('filosofia', true)
            ->where(function ($q) {
                $q->whereNull('atras_ruta')
                  ->orWhere('atras_ruta', 'filosofia');
            })
            ->update([
                'atras_ruta' => 'filosofia/temas',
                'atras_texto' => 'Todos los temas',
            ]);
    }

    public function down()
    {
        DB::table('paginas')
            ->where('filosofia', true)
            ->where('atras_ruta', 'filosofia/temas')
            ->update([
                'atras_ruta' => 'filosofia',
                'atras_texto' => 'Filosofía',
            ]);
    }
};
