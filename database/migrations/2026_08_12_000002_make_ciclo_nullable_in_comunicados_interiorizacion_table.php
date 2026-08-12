<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunicados_interiorizacion', function (Blueprint $table) {
            $table->string('ciclo')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('comunicados_interiorizacion', function (Blueprint $table) {
            $table->string('ciclo')->nullable(false)->change();
        });
    }
};
