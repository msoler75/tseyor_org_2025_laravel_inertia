<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('suscriptores', function (Blueprint $table) {
            $table->id();
            $table->string('servicio');
            $table->string('email')->unique();
            $table->string('token')->unique();
            // estado (enum): ok, error
            $table->enum('estado', ['ok', 'error'])->default('ok');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suscriptores');
    }
};
