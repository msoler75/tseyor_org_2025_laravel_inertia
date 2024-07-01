<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Libro;
use App\Pigmalion\SEO;
use Illuminate\Support\Facades\Cache;
use App\Pigmalion\BusquedasHelper;

class LibrosController extends Controller
{

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        $resultados = $categoria ?
            Libro::where('categoria', 'LIKE', "%$categoria%")
            ->when($categoria === '_', function ($query) {
                $query->orderByRaw('LOWER(titulo)');
            })
            ->paginate(20)->appends(['categoria' => $categoria])
            : ($buscar ?
                BusquedasHelper::buscar(Libro::class, $buscar)->paginate(10)
                ->appends(['buscar' => $buscar])
                //Libro::where('titulo', 'LIKE', "%$buscar%")
                  //    ->orWhere('descripcion', 'LIKE', "%$buscar%")->paginate(20)
                :
                Libro::orderBy('updated_at', 'desc')->paginate(20) // novedades
            );

        if ($buscar && !$resultados->count()) // por si acaso, por algun motivo algunas busquedas no las encuentra
            $resultados = Libro::where('titulo', 'LIKE', "%$buscar%")->orWhere('descripcion', 'LIKE', "%$buscar%")->paginate();

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

        // $palabras = implode(" ", $palabrasClave);

        // if (!$palabras)
        // $palabras = BusquedasHelper::descartarPalabrasComunes($libro->titulo);

        // BusquedasHelper::formatearResultados($relacionados, preg_split("/\s+/", $palabras));

        return Inertia::render('Libros/Libro', [
            'libro' => $libro,
            'relacionados' => $relacionados,
        ])
            ->withViewData(SEO::from($libro));
    }
}
