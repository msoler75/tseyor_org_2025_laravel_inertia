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
    public static $ITEMS_POR_PAGINA = 24;

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $categoria = $request->input('categoria');
        $page = $request->input('page', 1);

        $guias = $buscar ?
            Guia::search($buscar)->paginate(self::$ITEMS_POR_PAGINA, 'page', $page)
            :
            Guia::select(['nombre', 'slug', 'descripcion', 'imagen'])
                ->when($categoria, function ($query) use ($categoria) {
                    return $query->where('categoria', $categoria);
                })
                ->orderBy('nombre')
                ->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page);

        // $categorias = Guia::select(['categoria'])->get(); añadir distinct, extrar como array de categorias
           // obtiene el listado de categorías de los Libros
           $categorias = (new Guia())->getCategorias();

        return Inertia::render('Guias/Index', [
            'listado' => $guias,
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
        ->publicada()
        ->where('nombre', '>', $guia->nombre)->orderBy('nombre', 'asc')->first();

        $anterior = Guia::select(['id', 'slug', 'nombre', 'imagen'])
        ->publicada()
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
