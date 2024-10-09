<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->enum('estado', ['nuevo', 'asignado', 'no contesta', 'en curso'])
                  ->nullable()
                  ->default('nuevo')
                  ->collation('utf8mb4_general_ci');

            $table->string('asignado', 256)
                  ->nullable()
                  ->collation('utf8mb4_general_ci');

            $table->text('notas')
                  ->nullable()
                  ->collation('utf8mb4_general_ci');
        });
    }

    public function down()
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->dropColumn(['estado', 'asignado', 'notas']);
        });
    }
};
