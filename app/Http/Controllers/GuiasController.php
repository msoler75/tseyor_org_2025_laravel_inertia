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

        $categoria = $request->input('categoria');

        $guias = $buscar ? Guia::search($buscar)
            :
            Guia::select(['nombre', 'slug', 'descripcion', 'imagen'])
            ->when($categoria, function ($query) use ($categoria) {
                return $query->where('categoria', $categoria);
            })
            ->orderBy('nombre');

        $guias = $guias->paginate(24);

        // $categorias = Guia::select(['categoria'])->get(); añadir distinct, extrar como array de categorias
           // obtiene el listado de categorías de los Libros
           $categorias = (new Guia())->getCategorias();

        return Inertia::render('Guias/Index', [
            'guias' => $guias,
            'categoriaActiva' => $categoria,
            'categorias' => $categorias,
            'filtrado' => $buscar
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

        // $guias = Guia::select(['nombre', 'slug'])->whereNot('id', $guia->id)->orderBy('nombre')->get();

        $siguiente = Guia::select(['id', 'slug', 'nombre', 'imagen'])
        ->where('visibilidad', 'P')
        ->where('nombre', '>', $guia->nombre)->orderBy('nombre', 'asc')->first();

        $anterior = Guia::select(['id', 'slug', 'nombre', 'imagen'])
        ->where('visibilidad', 'P')
        ->where('nombre', '<', $guia->nombre)->orderBy('nombre', 'desc')->first();


        $libros_slugs = preg_split("/[\n\r\t\s;,]+/", $guia->libros, -1, PREG_SPLIT_NO_EMPTY);

        $libros = Libro::select(['titulo', 'imagen', 'slug'])->whereIn('slug', $libros_slugs)->get();

        return Inertia::render('Guias/Guia', [
            'guia' => $guia,
            'libros' =>  $libros,
            'siguiente' => $siguiente,
            'anterior' => $anterior
        ])
            ->withViewData(SEO::from($guia));
    }
}
