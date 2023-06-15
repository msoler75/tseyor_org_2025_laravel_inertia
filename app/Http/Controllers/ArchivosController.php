<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ArchivosController extends Controller
{
    public function index(Request $request)
    {
        $ruta = $request->path();

        Log::info("explorar archivos en ". $ruta);
        // Obtener la URL relativa actual de la aplicación
        $baseUrl = url("");

        $items = [];

        if ($ruta != "archivos" && $ruta != "/archivos") {
            // Agregar elemento de carpeta padre
            $carpeta = $ruta;
            $lm=Storage::disk('public')->lastModified($carpeta);
            $archivos_internos = Storage::disk('public')->files($carpeta);
            $subcarpetas_internas = Storage::disk('public')->directories($carpeta);
            $items[] = [
                'nombre' => "..",
                'clase' => "parent",
                'tipo' => 'carpeta',
                'ruta' => str_replace($baseUrl, '', str_replace('/storage', '', Storage::disk('public')->url(dirname($ruta)))),
                'archivos' => count($archivos_internos),
                'subcarpetas' => count($subcarpetas_internas),
                'fecha_modificacion' => Storage::disk('public')->lastModified($carpeta),
            ];
        }

        // Agregar carpetas a la colección de elementos
        $carpetas = Storage::disk('public')->directories($ruta);
        Log::info("actualizarItems.ruta=". $ruta);
        Log::info("actualizarItems.carpetas=". count($carpetas));
        //dd($carpetas);
        foreach ($carpetas as $carpeta) {
            $archivos_internos = Storage::disk('public')->files($carpeta);
            $subcarpetas_internas = Storage::disk('public')->directories($carpeta);
            $items[] = [
                'nombre' => basename($carpeta),
                'clase' => "",
                'tipo' => 'carpeta',
                'ruta' => str_replace($baseUrl, '',  str_replace('/storage', '', Storage::disk('public')->url($carpeta))),
                'archivos' => count($archivos_internos),
                'subcarpetas' => count($subcarpetas_internas),
                'fecha_modificacion' => Storage::disk('public')->lastModified($carpeta),
            ];
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

        Log::info("actualizarItems.items=". count($items));

        return Inertia::render('Archivos', [
            'items' => $items,
            'ruta' => $ruta
        ]);
    }

}
