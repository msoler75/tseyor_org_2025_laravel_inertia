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
        // Cambiar el campo estado de ENUM a VARCHAR(16)
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->string('estado', 16)->default('nueva')->change();
        });

        // Migrar cualquier valor 'desinteresado' existente a 'nointeresado'
        DB::table('inscripciones')
            ->where('estado', 'desinteresado')
            ->update(['estado' => 'nointeresado']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a ENUM con los valores originales
        DB::statement("ALTER TABLE inscripciones MODIFY COLUMN estado ENUM(
            'nueva',
            'asignada',
            'nocontesta',
            'contactado',
            'encurso',
            'duplicada'
        ) NOT NULL DEFAULT 'nueva'");

        // Migrar 'nointeresado' de vuelta a 'duplicado' (fallback a un estado válido)
        DB::table('inscripciones')
            ->where('estado', 'nointeresado')
            ->update(['estado' => 'duplicada']);

        // También migrar otros estados nuevos a estados válidos en el ENUM original
        DB::table('inscripciones')
            ->whereIn('estado', ['rebotada', 'finalizado', 'abandonado'])
            ->update(['estado' => 'duplicada']);
    }
};
