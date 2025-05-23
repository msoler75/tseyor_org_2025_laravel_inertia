<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('boletines', function (Blueprint $table) {
            $table->string('tipo', 32)->change();
        });
    }

    public function down()
    {
        Schema::table('boletines', function (Blueprint $table) {
            $table->enum('tipo', ['semanal', 'quincenal', 'mensual'])->change();
        });
    }
};
