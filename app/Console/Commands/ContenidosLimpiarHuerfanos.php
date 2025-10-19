<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contenido;
use App\Models\Noticia;
use App\Models\Normativa;
use App\Models\Comunicado;
use App\Models\Libro;
use App\Models\Audio;
use App\Models\Pagina;
use App\Models\Entrada;
use App\Models\Evento;
use App\Models\Psicografia;
use App\Models\Centro;
use App\Models\Contacto;
use App\Models\Equipo;
use App\Models\Experiencia;
use App\Models\Guia;
use App\Models\Informe;
use App\Models\Lugar;
use App\Models\Meditacion;
use App\Models\Sala;
use App\Models\Termino;
use App\Models\Tutorial;
use App\Models\Video;

class ContenidosLimpiarHuerfanos extends Command
{
    protected $signature = 'contenidos:huerfanos {--eliminar : Eliminar automáticamente las entradas huérfanas}';
    protected $description = 'Detecta, sincroniza y opcionalmente elimina entradas huérfanas en la tabla contenidos que apuntan a modelos inexistentes. También sincroniza deleted_at y visibilidad.';

    public function handle()
    {
        $this->info('🔍 Buscando entradas huérfanas en la tabla contenidos...');

        $contenidos = Contenido::all();
        $huerfanas = [];
        $totalVerificados = 0;
        $actualizacionesVisibilidad = 0;
        $actualizacionesDeletedAt = 0;

        $bar = $this->output->createProgressBar(count($contenidos));
        $bar->start();

        foreach($contenidos as $contenido) {
            $totalVerificados++;
            $esHuerfano = $this->verificarYActualizarExistenciaContenido($contenido, $actualizacionesVisibilidad, $actualizacionesDeletedAt);

            if($esHuerfano) {
                $huerfanas[] = $contenido;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if(count($huerfanas) == 0) {
            $this->info('✅ No se encontraron entradas huérfanas.');
            return;
        }

        $this->warn("⚠️  Se encontraron " . count($huerfanas) . " entradas huérfanas:");

        foreach($huerfanas as $huerfana) {
            $this->line("  ID {$huerfana->id}: {$huerfana->titulo} ({$huerfana->coleccion}:{$huerfana->id_ref})");
        }

        $this->newLine();

        $eliminadas = 0;
        if($this->option('eliminar')) {
            if($this->confirm('¿Está seguro de que desea eliminar estas ' . count($huerfanas) . ' entradas huérfanas?')) {
                $eliminadas = 0;
                foreach($huerfanas as $huerfana) {
                    $huerfana->forceDelete();
                    $eliminadas++;
                }
                $this->info("✅ Eliminadas {$eliminadas} entradas huérfanas definitivamente.");
                $this->info('💡 El índice de búsqueda se ha actualizado automáticamente.');
            }
        } else {
            $this->info('💡 Use --eliminar para eliminar automáticamente las entradas huérfanas.');
            $this->info('💡 O ejecute: php artisan contenidos:huerfanos --eliminar');
        }

        $this->newLine();
        $this->info('📊 Resumen:');
        $this->info("   Total verificados: {$totalVerificados}");
        $this->info("   Huérfanos encontrados: " . count($huerfanas));
        $this->info("   Huérfanos eliminados: {$eliminadas}");
        $this->info("   Actualizaciones de visibilidad: {$actualizacionesVisibilidad}");
        $this->info("   Actualizaciones de deleted_at: {$actualizacionesDeletedAt}");
    }

    /**
     * Verifica si el contenido referenciado realmente existe y actualiza el registro en Contenido si es necesario
     * Retorna true si es huérfano y debe eliminarse definitivamente
     */
    private function verificarYActualizarExistenciaContenido(Contenido $contenido, &$actualizacionesVisibilidad, &$actualizacionesDeletedAt): bool
    {
        $modelo = $this->obtenerModeloPorColeccion($contenido->coleccion);

        if (!$modelo) {
            // Colección desconocida, asumir que existe
            return false;
        }

        // Buscar el modelo incluyendo soft deletes
        $instancia = null;
        if ($contenido->coleccion === 'paginas') {
            $instancia = $modelo::withTrashed()->where('ruta', $contenido->slug_ref)->first();
        } else {
            $instancia = $modelo::withTrashed()->find($contenido->id_ref);
        }

        if (!$instancia) {
            // No existe ni siquiera con soft delete, es huérfano
            return true;
        }

        // El modelo existe, actualizar el Contenido si es necesario
        $actualizar = false;

        // Si el modelo está soft deleted, actualizar deleted_at en Contenido
        if ($instancia->trashed()) {
            if (!$contenido->trashed()) {
                $contenido->deleted_at = $instancia->deleted_at;
                $actualizacionesDeletedAt++;
                $actualizar = true;
            }
        } else {
            // Si no está deleted, verificar visibilidad
            if (isset($instancia->visibilidad) && $instancia->visibilidad !== $contenido->visibilidad) {
                $contenido->visibilidad = $instancia->visibilidad;
                $actualizacionesVisibilidad++;
                $actualizar = true;
            }
            // Si Contenido estaba soft deleted pero el original no, restaurarlo
            if ($contenido->trashed()) {
                $contenido->deleted_at = null;
                $actualizacionesDeletedAt++;
                $actualizar = true;
            }
        }

        if ($actualizar) {
            $contenido->save();
        }

        return false; // No es huérfano
    }

    /**
     * Obtiene la clase del modelo correspondiente a la colección
     */
    private function obtenerModeloPorColeccion(string $coleccion): ?string
    {
        $mapa = [
            'noticias' => Noticia::class,
            'normativas' => Normativa::class,
            'comunicados' => Comunicado::class,
            'libros' => Libro::class,
            'audios' => Audio::class,
            'paginas' => Pagina::class,
            'entradas' => Entrada::class,
            'eventos' => Evento::class,
            'psicografias' => Psicografia::class,
            'centros' => Centro::class,
            'contactos' => Contacto::class,
            'equipos' => Equipo::class,
            'experiencias' => Experiencia::class,
            'guias' => Guia::class,
            'informes' => Informe::class,
            'lugares' => Lugar::class,
            'meditaciones' => Meditacion::class,
            'salas' => Sala::class,
            'terminos' => Termino::class,
            'tutoriales' => Tutorial::class,
            'videos' => Video::class,
        ];

        return $mapa[$coleccion] ?? null;
    }
}

