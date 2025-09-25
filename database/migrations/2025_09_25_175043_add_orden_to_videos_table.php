<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->integer('orden')->nullable()->after('visibilidad')->comment('Orden de visualización de los videos');
        });

        // Asignar números de orden basándose en la fecha de creación
        $videos = DB::table('videos')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($videos as $index => $video) {
            DB::table('videos')
                ->where('id', $video->id)
                ->update(['orden' => $index + 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('orden');
        });
    }
};
