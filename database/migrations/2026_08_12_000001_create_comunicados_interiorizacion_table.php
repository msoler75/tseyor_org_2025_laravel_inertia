<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicados_interiorizacion', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->longText('texto');
            $table->unsignedSmallInteger('nivel')->comment('1 o 2');
            $table->string('ciclo')->comment('Ej: 2024, ciclo-primavera-2024');
            $table->string('numero', 20)->nullable()->comment('Numeración arbitraria dentro del ciclo');
            $table->date('fecha_comunicado');
            $table->year('ano');
            $table->string('imagen')->nullable();
            $table->json('audios')->nullable();
            $table->char('visibilidad', 1)->default('P')->comment('P=Publicado, B=Borrador');
            $table->timestamps();
            $table->softDeletes();

            $table->index('nivel');
            $table->index('ciclo');
            $table->index('ano');
            $table->index('visibilidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicados_interiorizacion');
    }
};
