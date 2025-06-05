<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Preguntas;
use App\Pigmalion\SEO;

class PreguntasController extends Controller
{
    public function index()
    {
        $secciones = Preguntas::select('titulo', 'id', 'slug', 'descripcion')->get();

        return Inertia::render('Preguntas/Index', [
            'secciones' => $secciones
        ])
            ->withViewData(SEO::get('preguntas-frecuentes'));
    }

    public function show($id)
    {

        if (is_numeric($id)) {
            $preguntas = Preguntas::findOrFail($id);
        } else {
            $preguntas = Preguntas::where('slug', $id)->firstOrFail();
        }

        $file = $preguntas->texto;

        // obtener la ruta ./resources/preguntas/{$f}
        $contenido_html = file_get_contents(base_path('resources/preguntas/' . $file));

        return Inertia::render('Preguntas/PreguntasSeccion', [
            'titulo' => $preguntas->titulo,
            'texto' => $contenido_html
        ])
            ->withViewData(SEO::from($preguntas));
    }
}
