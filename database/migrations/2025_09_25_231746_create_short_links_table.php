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
        Schema::create('enlaces_cortos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique()->index(); // Código único del enlace corto
            $table->text('url_original'); // URL original
            $table->string('prefijo', 10)->default('s')->index(); // Prefijo del enlace (s, e, d, etc)

            // Campos básicos
            $table->string('titulo', 255)->nullable(); // Título descriptivo
            $table->string('descripcion', 400)->nullable(); // Descripción opcional
            $table->integer('contenido_id')->nullable(); // Relación con contenido (signed int para compatibilidad)

            // Campos SEO específicos
            $table->string('meta_titulo', 255)->nullable(); // Meta título para SEO (máx 255 chars)
            $table->string('meta_descripcion', 400)->nullable(); // Meta descripción para SEO (máx 400 chars)
            $table->string('meta_keywords')->nullable(); // Palabras clave separadas por comas
            $table->string('og_titulo', 255)->nullable(); // Open Graph título (máx 255 chars)
            $table->string('og_descripcion', 400)->nullable(); // Open Graph descripción (máx 400 chars)
            $table->string('og_imagen')->nullable(); // URL de imagen para Open Graph
            $table->string('og_tipo', 20)->default('website'); // Tipo de contenido OG (website, article, etc)
            $table->string('twitter_card', 20)->default('summary'); // Tipo de Twitter Card
            $table->string('twitter_titulo', 255)->nullable(); // Twitter título (máx 255 chars)
            $table->string('twitter_descripcion', 400)->nullable(); // Twitter descripción (máx 400 chars)
            $table->string('twitter_imagen')->nullable(); // URL de imagen para Twitter
            $table->string('canonical_url')->nullable(); // URL canónica (si es diferente de la original)

            // Estadísticas básicas
            $table->unsignedBigInteger('clics')->default(0); // Contador de clics
            $table->timestamp('ultimo_clic')->nullable(); // Último clic

            // Estados
            $table->boolean('activo')->default(true); // Estado activo/inactivo

            $table->timestamps();

            // Índices compuestos
            $table->index(['prefijo', 'codigo']);
            $table->index(['contenido_id']);
            $table->index(['activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enlaces_cortos');
    }
};
