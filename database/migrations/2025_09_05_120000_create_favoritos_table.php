<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            // Relación al usuario
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Referencia genérica al contenido: colección y id_ref
            $table->string('coleccion')->index();
            $table->unsignedBigInteger('id_ref')->index();

            // Evitar duplicados: un usuario no puede tener el mismo par (coleccion,id_ref)
            $table->unique(['user_id', 'coleccion', 'id_ref']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favoritos');
    }
};
