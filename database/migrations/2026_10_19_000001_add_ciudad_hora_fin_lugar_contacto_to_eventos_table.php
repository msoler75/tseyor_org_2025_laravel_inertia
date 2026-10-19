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
        Schema::table('eventos', function (Blueprint $table) {
            $table->time('hora_fin')->nullable()->after('hora_inicio');
            $table->string('ciudad')->nullable()->after('pais');
            $table->string('lugar')->nullable()->after('ciudad');
            $table->string('contacto')->nullable()->after('lugar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['hora_fin', 'ciudad', 'lugar', 'contacto']);
        });
    }
};
