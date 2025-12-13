<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Guia;
use App\Pigmalion\Markdown;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obtener todas las guías
        $guias = Guia::all();

        Log::info("Iniciando conversión de {$guias->count()} guías de HTML a Markdown");

        foreach ($guias as $guia) {
            try {
                // Solo convertir si el campo texto tiene contenido
                if (!empty($guia->texto)) {

                    // Detectar si el texto ya es Markdown (contiene ## o más # sin etiquetas HTML)
                    $esMarkdown = $this->esFormatoMarkdown($guia->texto);

                    if ($esMarkdown) {
                        Log::info("Guía ID {$guia->id}: {$guia->nombre} ya está en formato Markdown, omitiendo conversión");
                        continue;
                    }

                    Log::info("Convirtiendo guía ID {$guia->id}: {$guia->nombre}");

                    $html = $guia->texto;

                    // Eliminar TODOS los divs (tanto de apertura como cierre) manteniendo su contenido
                    // Esto incluye divs con class="description", "quotes", "presentation", "scrollh", "release", etc.
                    $html = preg_replace('/<div[^>]*>/i', '', $html);
                    $html = preg_replace('/<\/div>/i', '', $html);

                    // Las cabeceras h4 se mantendrán y se convertirán a #### en markdown
                    // Los párrafos, blockquotes, etc. se convertirán correctamente

                    // Convertir HTML limpio a Markdown
                    $markdown = Markdown::toMarkdown($html);

                    // Guardar el texto convertido
                    $guia->texto = $markdown;
                    $guia->save();

                    Log::info("Guía ID {$guia->id} convertida exitosamente");
                }
            } catch (\Exception $e) {
                Log::error("Error al convertir guía ID {$guia->id}: {$e->getMessage()}");
                // Continuar con la siguiente guía en caso de error
            }
        }

        Log::info("Conversión de guías finalizada");
    }

    /**
     * Detecta si el texto está en formato Markdown
     * Cuenta etiquetas HTML vs marcadores Markdown
     */
    private function esFormatoMarkdown($texto): bool
    {
        // Contar etiquetas HTML comunes
        $htmlTagsCount = preg_match_all('/<(p|div|h[1-6]|blockquote|strong|em|ul|ol|li|br)[^>]*>/i', $texto);

        // Contar marcadores Markdown
        $markdownCount = 0;
        $markdownCount += preg_match_all('/^#{2,6}\s+/m', $texto); // Cabeceras ##, ###, etc.
        $markdownCount += preg_match_all('/^\>\s+/m', $texto);     // Blockquotes >
        $markdownCount += preg_match_all('/\*\*[^\*]+\*\*/u', $texto); // Bold **text**
        $markdownCount += preg_match_all('/\*[^\*]+\*/u', $texto);    // Italic *text*

        // Si hay más marcadores Markdown que etiquetas HTML, es Markdown
        // También si no hay etiquetas HTML pero sí marcadores Markdown
        return ($markdownCount > $htmlTagsCount) || ($htmlTagsCount == 0 && $markdownCount > 0);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar datos desde el backup SQL
        $sqlFile = base_path('guias.sql');

        if (!file_exists($sqlFile)) {
            Log::error("No se encontró el archivo de backup: {$sqlFile}");
            throw new \Exception("Archivo de backup no encontrado: {$sqlFile}");
        }

        Log::info("Restaurando guías desde backup: {$sqlFile}");

        // Leer el contenido del archivo SQL
        $sql = file_get_contents($sqlFile);

        // Ejecutar el SQL
        \DB::unprepared($sql);

        Log::info("Restauración completada desde backup");
    }
};
