<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Entrada;
use App\Pigmalion\SEO;

class EntradasController extends Controller
{

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $resultados = $buscar ? Entrada::select(['id', 'slug', 'titulo', 'imagen', 'descripcion', 'published_at'])
            ->where('visibilidad', 'P')
            ->whereRaw('CONCAT(titulo," ", descripcion, " ", texto) LIKE \'%' . $buscar . '%\'')
            // ordenar por published_at
            ->orderBy('published_at', 'desc')
            ->paginate(12)->appends(['buscar' => $buscar])
            :
            Entrada::select(['id', 'slug', 'titulo', 'imagen', 'descripcion', 'published_at'])->where('visibilidad', 'P')->orderBy('published_at', 'desc')->paginate(10);

        $recientes = Entrada::select(['slug', 'titulo', 'published_at'])->where('visibilidad', 'P')->orderBy('published_at', 'desc')->take(32)->get();

        return Inertia::render('Entradas/Index', [
            'filtrado' => $buscar,
            'listado' => $resultados,
            'recientes' => $recientes
        ])
            ->withViewData(SEO::get('entradas'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $entrada = Entrada::findOrFail($id);
        } else {
            $entrada = Entrada::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $entrada->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$entrada || (!$publicado && !$borrador && !$editor)) {
            abort(404);
        }

        $siguiente = Entrada::select(['id', 'slug', 'titulo', 'imagen', 'descripcion', 'published_at'])
            ->where('visibilidad', 'P')
            ->where('published_at', '>', $entrada->published_at)->orderBy('published_at', 'asc')->first();

        $anterior = Entrada::select(['id', 'slug', 'titulo', 'imagen', 'descripcion', 'published_at'])
            ->where('visibilidad', 'P')
            ->where('published_at', '<', $entrada->published_at)->orderBy('published_at', 'desc')->first();

        return Inertia::render('Entradas/Entrada', [
            'entrada' => $entrada,
            'siguiente' => $siguiente,
            'anterior' => $anterior
        ])
            ->withViewData(SEO::from($entrada));
    }

    /**
     * Genera un PDF desde los datos de un artículo de blog
     */
    public function pdf($id)
    {
        if (is_numeric($id)) {
            $contenido = Entrada::findOrFail($id);
        } else {
            $contenido = Entrada::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado = $contenido->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$contenido || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        // Agregar los encabezados para evitar el caché
    $headers = [
        'Content-Type' => 'application/pdf',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ];

        return $contenido->generatePdf();
    }
}
