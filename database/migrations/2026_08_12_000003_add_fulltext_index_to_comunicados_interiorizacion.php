<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE comunicados_interiorizacion ADD FULLTEXT texto_fulltext (texto)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE comunicados_interiorizacion DROP INDEX texto_fulltext');
    }
};
