<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Libro;
use App\Models\Guia;
use App\Pigmalion\SEO;
use Illuminate\Support\Facades\Cache;
use App\Pigmalion\BusquedasHelper;

class LibrosController extends Controller
{

    public static $ITEMS_POR_PAGINA = 20;

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        $query = Libro::select(['slug', 'titulo', 'descripcion', 'updated_at', 'imagen'])
            ->publicado();

        if ($buscar) {
            $ids = Libro::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('libros.id', $ids);
        }

        if (!$categoria)
            $query->latest();
        else if ($categoria == '_')
            $query->orderBy('titulo', 'asc');
        else
            $query->where('categoria', 'like', '%' . $categoria . '%');

        $resultados = $query->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        // obtiene el listado de categorías de los Libros
        $categorias = (new Libro())->getCategorias();

        return Inertia::render('Libros/Index', [
            'filtrado' => $buscar,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias,
            'busquedaValida' => BusquedasHelper::validarBusqueda($buscar)
        ])
            ->withViewData(SEO::get('libros'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $libro = Libro::findOrFail($id);
        } else {
            $libro = Libro::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado = $libro->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$libro || (!$publicado && !$borrador && !$editor)) {
            abort(404);
        }


        $palabrasClave = [];

        // Obtener libros relacionados por categoría o coincidencia de palabras clave en la descripción
        $relacionados = Libro::where('id', '!=', $libro->id)
            ->where('categoria', $libro->categoria)
            ->where(function ($query) use ($libro) {
                $tituloPalabras = explode(' ', strtolower($libro->titulo));

                foreach ($tituloPalabras as $palabra) {
                    // Filtrar palabras de menos de 3 letras y palabras comunes
                    if (strlen($palabra) > 2 && !in_array($palabra, BusquedasHelper::$palabrasComunes)) {
                        $palabrasClave[] = $palabra;
                    }
                }

                if (!empty($palabrasClave)) {
                    $query->where(function ($query) use ($palabrasClave) {
                        foreach ($palabrasClave as $clave) {
                            $query->orWhere('descripcion', 'like', '%' . $clave . '%')
                                ->orWhere('titulo', 'like', '%' . $clave . '%');
                        }
                    });
                }
            })
            // descartamos el mismo libro que estamos mostrando
            ->orderByRaw('(CASE WHEN titulo LIKE ? THEN 2 WHEN descripcion LIKE ? THEN 1 ELSE 0 END) DESC', [$libro->titulo . '%', '%' . $libro->titulo . '%'])
            ->take(8)
            ->get();


        // si no ha encontrado ninguno buscamos de la misma categoría
        if ($relacionados->count() == 0) {
            $categorias = preg_split("/,/", $libro->categoria, -1, PREG_SPLIT_NO_EMPTY);
            $relacionados = Libro::where('id', '!=', $libro->id)
                ->where(function ($query) use ($categorias) {
                    foreach ($categorias as $categoria) $query->orWhere('categoria', 'like', '%' . $categoria . '%');
                })
                // descartamos el mismo libro que estamos mostrando
                // ->orderByRaw('(CASE WHEN titulo LIKE ? THEN 2 WHEN descripcion LIKE ? THEN 1 ELSE 0 END) DESC', [$libro->titulo . '%', '%' . $libro->titulo . '%'])
                ->take(8)
                ->get();
        }


        // busca los guías asociados a este libro. Sea por la descripción, o por la bibliografía asociada a cada guía
        $descripcion_tokens = preg_split("/[,.\(\)\t\s\n\r]/", strtolower(BusquedasHelper::descartarPalabrasComunes($libro->descripcion)[0]), -1, PREG_SPLIT_NO_EMPTY);
        $descripcion_tokens = array_map(function ($m) {
            return "\\b" . $m . "\\b";
        }, $descripcion_tokens);
        $descripcion_en_or = implode("|", $descripcion_tokens);
        $guias = Guia::select('nombre', 'slug', 'imagen')->where('libros', 'like', '%' . $libro->slug . '%')
            ->orWhereRaw('LOWER(nombre) REGEXP ?', [$descripcion_en_or])
            ->get();


        // $palabras = implode(" ", $palabrasClave);

        // if (!$palabras)
        // $palabras = BusquedasHelper::descartarPalabrasComunes($libro->titulo);

        // BusquedasHelper::formatearResultados($relacionados, preg_split("/\s+/", $palabras));

        return Inertia::render('Libros/Libro', [
            'libro' => $libro,
            'relacionados' => $relacionados,
            'guias' => $guias
        ])
            ->withViewData(SEO::from($libro));
    }
}
