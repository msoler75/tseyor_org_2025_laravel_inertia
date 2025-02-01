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
        // crear tabla para modelo Psicografia:
        Schema::create('psicografias', function (Blueprint $table) {
            $table->id(); // Auto-incremental primary key
            $table->string('titulo'); // A string column
            $table->string('slug')->unique();
            $table->string('categoria');
            $table->text('descripcion')->nullable(); // A text column (nullable)
            $table->string('imagen'); // imagen
            $table->timestamps(); // Created_at and updated_at timestamps
            // campo deleted_at
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psicografias');
    }
};
