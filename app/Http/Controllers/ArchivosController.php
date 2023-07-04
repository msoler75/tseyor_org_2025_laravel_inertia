<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use App\Policies\CarpetaPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\Carpeta;

class ArchivosController extends Controller
{
    public function index(Request $request)
    {
        $ruta = $request->path();

        Log::info("explorar archivos en " . $ruta);

        $carpetaPolicy = new CarpetaPolicy();
        if (!$carpetaPolicy->leer(auth()->user(), $ruta)) {
            throw new AuthorizationException('No tienes permisos para leer la carpeta', 403);
        }

        // Obtener la URL relativa actual de la aplicación
        $baseUrl = url("");

        $items = [];

        if ($ruta != "archivos" && $ruta != "/archivos") {
            // Agregar elemento de carpeta padre
            $archivos_internos = Storage::disk('public')->files($ruta);
            $subcarpetas_internas = Storage::disk('public')->directories($ruta);
            $items[] = [
                'nombre' => "..",
                'clase' => "parent",
                'tipo' => 'carpeta',
                'ruta' => str_replace($baseUrl, '', str_replace('/storage', '', Storage::disk('public')->url(dirname($ruta)))),
                'archivos' => count($archivos_internos),
                'subcarpetas' => count($subcarpetas_internas),
                'fecha_modificacion' => Storage::disk('public')->lastModified($ruta),
            ];
        }

        // Agregar carpetas a la colección de elementos
        $carpetas = Storage::disk('public')->directories($ruta);
        Log::info("actualizarItems.ruta=" . $ruta);
        Log::info("actualizarItems.carpetas=" . count($carpetas));
        //dd($carpetas);
        foreach ($carpetas as $carpeta) {
            $archivos_internos = Storage::disk('public')->files($carpeta);
            $subcarpetas_internas = Storage::disk('public')->directories($carpeta);
            $item = [
                'nombre' => basename($carpeta),
                'clase' => "",
                'tipo' => 'carpeta',
                'ruta' => str_replace($baseUrl, '',  str_replace('/storage', '', Storage::disk('public')->url($carpeta))),
                'archivos' => count($archivos_internos),
                'subcarpetas' => count($subcarpetas_internas),
                'fecha_modificacion' => Storage::disk('public')->lastModified($carpeta)
            ];
            // Obtener información de la carpeta correspondiente, si existe
            $carpetaModel = Carpeta::where('ruta', $ruta."/". $item['nombre'])->first();
            if ($carpetaModel)
                $item['privada'] = $carpetaModel->privada;
            $items[] = $item;
        }

        // Agregar archivos a la colección de elementos
        $archivos = Storage::disk('public')->files($ruta);
        foreach ($archivos as $archivo) {
            $items[] = [
                'nombre' => basename($archivo),
                'clase' => "",
                'tipo' => 'archivo',
                'ruta' => str_replace($baseUrl, '', Storage::disk('public')->url($ruta . '/' . basename($archivo))),
                'tamano' => Storage::disk('public')->size($archivo),
                'fecha_modificacion' => Storage::disk('public')->lastModified($archivo),
            ];
        }

        Log::info("actualizarItems.items=" . count($items));

        return Inertia::render('Archivos', [
            'items' => $items,
            'ruta' => $ruta
        ])
            ->withViewData([
                'seo' => new SEOData(
                    title: 'Archivos de Tseyor',
                    description: 'Contenido de ' . $ruta,
                )
            ]);
    }
}
