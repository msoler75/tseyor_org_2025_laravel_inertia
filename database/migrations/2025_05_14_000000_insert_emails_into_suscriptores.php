<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

return new class extends Migration
{
    public function up()
    {
        $filePath = base_path('suscripciones.txt');
        $emails = File::exists($filePath)
            ? array_unique(array_filter(array_map('trim', iterator_to_array(File::lines($filePath)))))
            : [];

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    DB::table('suscriptores')->insert([
                        'servicio' => 'boletin:mensual',
                        'email' => $email,
                        'token' => Str::random(32), // Genera un token único
                        'estado' => 'ok',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (QueryException $e) {
                    // Ignorar errores de duplicados y continuar
                    if ($e->getCode() !== '23000') { // Código SQL para violación de clave única
                        //throw $e;
                        \Log::error('Error al insertar el email (no por duplicado): ' . $email . ' - ' . $e->getMessage());
                    }
                }
            }
        }
    }

    public function down()
    {
        DB::table('suscriptores')->where('servicio', 'boletin:mensual')->delete();
    }
};
