<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('boletines', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->enum('tipo', ['semanal', 'quincenal', 'mensual']);
            $table->text('texto');
            //mes y año
            $table->integer('anyo');
            $table->integer('mes');
            $table->integer('semana');
            $table->integer('enviado')->default(0);
            //tipo: enum: semanal, quincenal, mensual, bimensual, trimestral, semestral, anual
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('boletines');
    }
};
