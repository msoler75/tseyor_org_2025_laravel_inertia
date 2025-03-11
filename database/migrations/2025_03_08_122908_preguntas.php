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
            // campos de fecha habituales: created_at, updated_at, deleted_at   
            $table->timestamps();


            // y ahora quiero insertar 3 registros en la tabla:
           
            /*
            INSERT INTO preguntas (titulo, slug, descripcion, texto, created_at, updated_at)
VALUES
    ('Parte 1. Extraterrestres, Hermanos Mayores y Otros Seres', 'parte-1-extraterrestres', 'Quiénes son, de dónde vienen, qué quieren... Conoce a fondo a nuestros amigos del espacio', 'preguntas1.html', NOW(), NOW()),
    ('Parte 2. El Salto Cuántico', 'parte-2-el-salto-cuantico', 'Ahora puedes conocer acerca del próximo evento cósmico que va a vivir este planeta', 'preguntas2.html', NOW(), NOW()),
    ('Parte 3. Sobre Tseyor', 'parte-3-sobre-tseyor', 'Qué o qué es Tseyor, quiénes lo forman, qué es lo que buscamos, etc.', 'preguntas3.html', NOW(), NOW());
    */

            
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
