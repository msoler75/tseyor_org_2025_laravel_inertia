<?php

namespace App\Observers;

use App\Models\Contenido;
use App\Models\EnlaceCorto;
use Illuminate\Support\Facades\Log;

class ContenidoObserver
{
    /**
     * Handle the Contenido "saved" event.
     * Actualizar enlaces cortos relacionados cuando cambie el contenido
     */
    public function saved(Contenido $contenido)
    {
        // Solo procesar contenido público
        if ($contenido->visibilidad !== 'P') {
            // Si el contenido ya no es público, desactivar enlaces relacionados
            if ($contenido->isDirty('visibilidad')) {
                $this->desactivarEnlacesRelacionados($contenido);
            }
            return;
        }

        // Buscar enlaces cortos directamente relacionados con este contenido
        $enlacesCortos = EnlaceCorto::where('contenido_id', $contenido->id)
            ->where('activo', true)
            ->get();

        if ($enlacesCortos->isEmpty()) {
            return;
        }

        foreach ($enlacesCortos as $enlace) {
            // Actualizar datos SEO del enlace corto
            $seoData = $this->extraerSeoDelContenido($contenido);

            $enlace->update([
                'titulo' => $contenido->titulo,
                'descripcion' => $contenido->descripcion,
                'meta_titulo' => $seoData['meta_titulo'],
                'meta_descripcion' => $seoData['meta_descripcion'],
                'og_titulo' => $seoData['og_titulo'],
                'og_descripcion' => $seoData['og_descripcion'],
                'og_imagen' => $seoData['og_imagen'] ?? null,
                'twitter_imagen' => $seoData['twitter_imagen'] ?? null,
                'updated_at' => now()
            ]);

            Log::info('Enlace corto actualizado desde Contenido', [
                'contenido_id' => $contenido->id,
                'enlace_codigo' => $enlace->codigo,
                'titulo' => $contenido->titulo,
                'tipo' => 'contenido_interno'
            ]);
        }
    }

    /**
     * Desactivar enlaces relacionados cuando el contenido no es público
     */
    private function desactivarEnlacesRelacionados(Contenido $contenido)
    {
        EnlaceCorto::where('contenido_id', $contenido->id)
            ->where('activo', true)
            ->update([
                'activo' => false,
                'updated_at' => now()
            ]);

        Log::info('Enlaces cortos desactivados por cambio de visibilidad', [
            'contenido_id' => $contenido->id,
            'nueva_visibilidad' => $contenido->visibilidad
        ]);
    }

    /**
     * Handle the Contenido "deleted" event.
     * Desactivar enlaces cortos cuando se elimine el contenido
     */
    public function deleted(Contenido $contenido)
    {
        EnlaceCorto::where('contenido_id', $contenido->id)
            ->update([
                'activo' => false,
                'updated_at' => now()
            ]);

        Log::info('Enlaces cortos desactivados por eliminación de contenido', [
            'contenido_id' => $contenido->id
        ]);
    }

    /**
     * Extraer datos SEO del modelo Contenido
     */
    private function extraerSeoDelContenido(Contenido $contenido): array
    {
        $seoData = [
            'meta_titulo' => $contenido->titulo,
            'meta_descripcion' => $contenido->descripcion,
            'og_titulo' => $contenido->titulo,
            'og_descripcion' => $contenido->descripcion,
        ];

        // Si tiene imagen, agregarla a los metadatos
        if ($contenido->imagen) {
            $seoData['og_imagen'] = url($contenido->imagen);
            $seoData['twitter_imagen'] = url($contenido->imagen);
        }

        return $seoData;
    }
}
