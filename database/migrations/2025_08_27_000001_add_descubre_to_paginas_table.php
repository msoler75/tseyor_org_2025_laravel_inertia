<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('paginas', function (Blueprint $table) {
            $table->boolean('descubre')->default(false)->after('texto');
        });
    }

    public function down()
    {
        Schema::table('paginas', function (Blueprint $table) {
            $table->dropColumn('descubre');
        });
    }
};
