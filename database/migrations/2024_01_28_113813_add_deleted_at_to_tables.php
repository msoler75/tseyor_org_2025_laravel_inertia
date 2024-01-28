<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


    private $tables_to_change = [
        'audios',
        'centros',
        'comentarios',
        'comunicados',
        'contactos',
        'emails',
        'entradas',
        'equipos',
        'eventos',
        'experiencias',
        'grupos',
        'guias',
        'informes',
        'libros',
        'lugares',
        'meditaciones',
        'nodos',
        'nodos_acl',
        'normativas',
        'noticias',
        'paginas',
        'publicaciones',
        'radio',
        'salas',
        'seo',
        'settings',
        'solicitudes',
        'terminos',
        'users',
        'videos',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables_to_change as $table)
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables_to_change as $table)
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
    }
};
