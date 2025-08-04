<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->timestamp('ultima_actividad')->nullable()->after('updated_at')
                ->comment('Fecha de la última actividad real del tutor (comentarios, cambios de estado, etc.)');
        });

        // Inicializar con el valor de updated_at para registros existentes
        DB::statement('UPDATE inscripciones SET ultima_actividad = updated_at WHERE ultima_actividad IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->dropColumn('ultima_actividad');
        });
    }
};
