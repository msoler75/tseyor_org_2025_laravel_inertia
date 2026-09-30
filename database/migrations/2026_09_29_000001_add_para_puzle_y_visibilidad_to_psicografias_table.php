<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * para_puzle: controla si la psicografía puede lanzarse en el puzzle.
     *   - Las filas existentes quedan en TRUE (psicografías anteriores a esta
     *     fecha, que sí se lanzaban en el puzle).
     *   - El default de la columna es FALSE para que toda psicografía creada
     *     de aquí en más NO sea lanzable salvo que se marque explícitamente.
     *
     * visibilidad: replica el patrón de libros/comunicados/eventos
     *   (P=Publicado, B=Borrador). Default 'P' para no cambiar el estado
     *   actual de nada: hoy todas las psicografías son públicas.
     */
    public function up(): void
    {
        Schema::table('psicografias', function (Blueprint $table) {
            $table->boolean('para_puzle')
                ->default(false)
                ->after('imagen')
                ->index('psicografias_para_puzle_index')
                ->comment('Si puede lanzarse en el puzzle (puzle.tseyor.org)');

            $table->char('visibilidad', 1)
                ->default('P')
                ->after('para_puzle')
                ->index('psicografias_visibilidad_index')
                ->comment('P=Publicado, B=Borrador');
        });

        // Las psicografías preexistentes sí se lanzaban en el puzle.
        DB::table('psicografias')->update(['para_puzle' => true]);

        // Las borradas también eran públicas: no cambiamos su estado.
        DB::table('psicografias')->update(['visibilidad' => 'P']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psicografias', function (Blueprint $table) {
            $table->dropIndex('psicografias_para_puzle_index');
            $table->dropIndex('psicografias_visibilidad_index');
            $table->dropColumn(['para_puzle', 'visibilidad']);
        });
    }
};
