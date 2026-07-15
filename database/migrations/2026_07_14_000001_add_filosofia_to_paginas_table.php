<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('paginas', function (Blueprint $table) {
            $table->boolean('filosofia')->default(false)->after('descubre');
            $table->integer('orden')->nullable()->after('filosofia');
        });
    }

    public function down()
    {
        Schema::table('paginas', function (Blueprint $table) {
            $table->dropColumn('filosofia');
            $table->dropColumn('orden');
        });
    }
};
