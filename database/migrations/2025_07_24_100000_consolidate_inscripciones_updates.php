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
        Schema::table('inscripciones', function (Blueprint $table) {
            // Agregar nueva columna user_id para asignaciones
            $table->unsignedBigInteger('user_id')->nullable()->after('estado');
            $table->foreign('user_id')->references('id')->on('users');

            // Campos para seguimiento automático
            $table->timestamp('fecha_asignacion')->nullable()->after('user_id');
            $table->timestamp('ultima_notificacion')->nullable()->after('fecha_asignacion');
        });

        // Migrar datos existentes de 'asignado' a 'user_id'
        $this->migrateAssignedUsers();

        // Cambiar el campo estado de ENUM a VARCHAR(16)
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->string('estado', 16)->default('nuevo')->change();
        });

        // Migrar cualquier valor 'desinteresado' existente a 'nointeresado'
        DB::table('inscripciones')
            ->where('estado', 'desinteresado')
            ->update(['estado' => 'nointeresado']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios de estado primero
        DB::statement("ALTER TABLE inscripciones MODIFY COLUMN estado ENUM(
            'nuevo',
            'asignado',
            'nocontesta',
            'contactado',
            'encurso',
            'duplicado'
        ) NOT NULL DEFAULT 'nuevo'");

        // Migrar estados nuevos a estados válidos en el ENUM original
        DB::table('inscripciones')
            ->where('estado', 'nointeresado')
            ->update(['estado' => 'duplicado']);

        DB::table('inscripciones')
            ->whereIn('estado', ['rebotada', 'finalizado', 'abandonado'])
            ->update(['estado' => 'duplicado']);

        // Eliminar campos de automatización
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'fecha_asignacion',
                'ultima_notificacion'
            ]);
        });
    }

    /**
     * Migrar datos existentes de la columna 'asignado' a 'user_id'
     */
    private function migrateAssignedUsers(): void
    {
        $inscripciones = DB::table('inscripciones')
            ->whereNotNull('asignado')
            ->where('asignado', '!=', '')
            ->get();

        $usuariosNoEncontrados = [];
        $migracionExitosa = 0;

        foreach ($inscripciones as $inscripcion) {
            // Buscar usuario por nombre (asumiendo que 'asignado' contiene el nombre)
            $usuario = DB::table('users')
                ->where('name', 'LIKE', '%' . trim($inscripcion->asignado) . '%')
                ->orWhere('email', 'LIKE', '%' . trim($inscripcion->asignado) . '%')
                ->first();

            if ($usuario) {
                DB::table('inscripciones')
                    ->where('id', $inscripcion->id)
                    ->update([
                        'user_id' => $usuario->id,
                        'fecha_asignacion' => now()
                    ]);
                $migracionExitosa++;
            } else {
                $usuariosNoEncontrados[] = [
                    'inscripcion_id' => $inscripcion->id,
                    'asignado' => $inscripcion->asignado
                ];
            }
        }

        // Mostrar resultados de la migración
        echo "\n=== MIGRACIÓN DE USUARIOS ASIGNADOS ===\n";
        echo "Inscripciones migradas exitosamente: {$migracionExitosa}\n";

        if (!empty($usuariosNoEncontrados)) {
            $count = count($usuariosNoEncontrados);
            echo "Usuarios NO encontrados ({$count}):\n";
            foreach ($usuariosNoEncontrados as $noEncontrado) {
                echo "- ID {$noEncontrado['inscripcion_id']}: '{$noEncontrado['asignado']}'\n";
            }
            echo "\nEstas inscripciones mantienen el valor en la columna 'asignado' para revisión manual.\n";
        }
        echo "==========================================\n\n";
    }
};
