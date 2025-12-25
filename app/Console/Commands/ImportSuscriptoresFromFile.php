<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Suscriptor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ImportSuscriptoresFromFile extends Command
{
    protected $signature = 'suscriptores:import {file} {--servicio=boletin:mensual} {--delimiter=,}';
    protected $description = 'Importa suscriptores desde un archivo de texto o CSV. Cada línea debe contener un correo electrónico.';

    public function handle()
    {
        $filePath = $this->argument('file');
        $servicio = $this->option('servicio');
        $delimiter = $this->option('delimiter');
        $estado = 'ok'; // Estado fijo

        // Validar que el archivo existe
        if (!file_exists($filePath)) {
            $this->error("El archivo '$filePath' no existe.");
            return 1;
        }

        // Validar el servicio
        $serviciosValidos = [
            'boletin:semanal',
            'boletin:quincenal',
            'boletin:mensual',
            'boletin:bimensual',
            'boletin:trimestral'
        ];

        if (!in_array($servicio, $serviciosValidos)) {
            $this->error("Servicio '$servicio' no válido. Debe ser uno de: " . implode(', ', $serviciosValidos));
            return 1;
        }

        $this->info("Leyendo archivo: $filePath");
        $this->info("Servicio: $servicio");
        $this->newLine();

        // Leer el archivo
        $content = file_get_contents($filePath);
        if ($content === false) {
            $this->error("No se pudo leer el archivo.");
            return 1;
        }

        // Separar por líneas y limpiar
        $lines = explode("\n", $content);
        $emails = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) {
                // Ignorar líneas vacías y comentarios
                continue;
            }

            // Si el delimitador es coma o punto y coma, separar
            if ($delimiter !== "\n") {
                $parts = explode($delimiter, $line);
                foreach ($parts as $part) {
                    $email = trim($part);
                    if (!empty($email)) {
                        $emails[] = $email;
                    }
                }
            } else {
                $emails[] = $line;
            }
        }

        if (empty($emails)) {
            $this->warn("No se encontraron correos electrónicos en el archivo.");
            return 1;
        }

        // Eliminar duplicados
        $emails = array_unique($emails);

        $this->info("Total correos encontrados: " . count($emails));
        $this->newLine();

        $insertados = 0;
        $actualizados = 0;
        $errores = 0;
        $duplicados = 0;
        $invalidos = 0;

        $progressBar = $this->output->createProgressBar(count($emails));
        $progressBar->start();

        foreach ($emails as $email) {
            $email = trim($email);

            // Validar formato de email
            $validator = Validator::make(['email' => $email], [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                Log::channel('mailing')->error("Suscriptor no importado: '$email' - formato inválido");
                $invalidos++;
                $progressBar->advance();
                continue;
            }

            try {
                // Verificar si ya existe
                $suscriptor = Suscriptor::where('email', $email)->first();

                if ($suscriptor) {
                    // Si existe, verificar si tiene el mismo servicio
                    if ($suscriptor->servicio === $servicio && $suscriptor->estado === $estado) {
                        $duplicados++;
                    } else {
                        // Actualizar el servicio y/o estado
                        $servicioAnterior = $suscriptor->servicio;
                        $estadoAnterior = $suscriptor->estado;
                        $suscriptor->servicio = $servicio;
                        $suscriptor->estado = $estado;
                        $suscriptor->save();
                        Log::channel('mailing')->info("Suscriptor actualizado: $email (servicio: $servicioAnterior -> $servicio, estado: $estadoAnterior -> $estado)");
                        $actualizados++;
                    }
                } else {
                    // Crear nuevo suscriptor
                    $token = Str::random(32);
                    Suscriptor::create([
                        'email' => $email,
                        'servicio' => $servicio,
                        'estado' => $estado,
                        'token' => $token,
                    ]);
                    Log::channel('mailing')->info("Suscriptor insertado: $email (servicio: $servicio)");
                    $insertados++;
                }
            } catch (\Exception $e) {
                Log::channel('mailing')->error("Error al importar suscriptor '$email': " . $e->getMessage());
                $errores++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("=== RESUMEN ===");
        $this->info("Insertados: $insertados");
        $this->info("Actualizados: $actualizados");
        $this->info("Duplicados (sin cambios): $duplicados");
        $this->info("Emails inválidos: $invalidos");
        $this->info("Errores: $errores");
        $this->info("Total procesados: " . count($emails));

        Log::channel('mailing')->info("Importación de suscriptores desde archivo completada. Insertados: $insertados, Actualizados: $actualizados, Duplicados: $duplicados, Inválidos: $invalidos, Errores: $errores");

        return 0;
    }

    public function getSynopsis(bool $short = false): string
    {
        return "Uso: php artisan suscriptores:import <archivo> [--servicio=boletin:mensual] [--delimiter=,]\n\n" .
               "Ejemplo: php artisan suscriptores:import emails.txt\n" .
               "Ejemplo: php artisan suscriptores:import emails.csv --delimiter=,\n" .
               "Ejemplo: php artisan suscriptores:import lista.txt --servicio=boletin:semanal";
    }

    public function getHelp(): string
    {
        return $this->getSynopsis() . "\n\n" .
               "El archivo puede contener:\n" .
               "  - Un correo por línea\n" .
               "  - Varios correos separados por comas (CSV)\n" .
               "  - Líneas que comienzan con # serán ignoradas (comentarios)\n\n" .
               "Servicios disponibles:\n" .
               "  - boletin:semanal\n" .
               "  - boletin:quincenal\n" .
               "  - boletin:mensual (por defecto)\n" .
               "  - boletin:bimensual\n" .
               "  - boletin:trimestral\n\n" .
               "Nota: Todos los suscriptores se importan con estado 'ok'";
    }
}
