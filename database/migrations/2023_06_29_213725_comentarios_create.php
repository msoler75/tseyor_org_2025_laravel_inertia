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
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->string('uid');
            $table->text('texto');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('respuesta_a')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('respuesta_a')->references('id')->on('comentarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
