<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('day');
            $table->string('month');
            $table->string('year');
            $table->string('city');
            $table->string('region');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('contact')->nullable();
            $table->boolean('agreement');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
