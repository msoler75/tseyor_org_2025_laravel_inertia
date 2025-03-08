<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // crear tabla para modelo Preguntas
        Schema::create('preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug');
            $table->string('descripcion');
            $table->text('texto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // borrar tabla para modelo Preguntas
        Schema::dropIfExists('preguntas');
    }
};
