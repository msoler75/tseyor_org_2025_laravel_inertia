<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Cambiar ENUM a VARCHAR conservando los valores existentes
        Schema::table('suscriptores', function (Blueprint $table) {
            $table->string('estado', 32)->default('activo')->change();
        });
    }

    public function down()
    {
        // Si quieres restaurar el ENUM, ajusta los valores posibles según tu modelo anterior
        DB::statement("ALTER TABLE suscriptores MODIFY estado VARCHAR(32) DEFAULT 'activo'");
    }
};
