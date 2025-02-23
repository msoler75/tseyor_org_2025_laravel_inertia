<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Guia;
use App\Models\Contenido;
use App\Models\Libro;
use App\Pigmalion\SEO;

class GuiasController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $guias = $buscar ? Guia::search($buscar)
            :
            Guia::select(['nombre', 'slug', 'descripcion', 'imagen']);

        $guias = $guias->paginate(24);

        return Inertia::render('Guias/Index', [
            'guias' => $guias
        ])
            ->withViewData(SEO::get('guias'));
    }


    public function show($id)
    {
        if (is_numeric($id)) {
            $guia = Guia::findOrFail($id);
        } else {
            $guia = Guia::where('slug', $id)->firstOrFail();
        }

        if (!$guia) {
            abort(404); // Guía Estelar no encontrada
        }

        $guias = Guia::select(['nombre', 'slug'])->get();

        $libros_slugs = preg_split("/[\n\r\t\s;,]+/", $guia->libros, -1, PREG_SPLIT_NO_EMPTY);

        $libros = Libro::select(['titulo', 'imagen', 'slug'])->whereIn('slug', $libros_slugs)->get();

        return Inertia::render('Guias/Guia', [
            'guia' => $guia,
            'libros' =>  $libros,
            'guias' => $guias,
        ])
            ->withViewData(SEO::from($guia));
    }
}
