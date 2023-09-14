<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Libro;
use App\Pigmalion\SEO;
use Illuminate\Support\Facades\Cache;

class LibrosController extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->input('buscar');
        $categoria = $request->input('categoria');

        $resultados = $categoria ?
            Libro::where('categoria', 'LIKE', "%$categoria%")
            ->paginate(12)->appends(['categoria' => $categoria])
            : ($filtro ? Libro::where('titulo', 'like', '%' . $filtro . '%')
                ->orWhere('descripcion', 'like', '%' . $filtro . '%')
                ->paginate(10)->appends(['buscar' => $filtro])
                :
                Libro::latest()->paginate(10)
            );

        $categorias = Cache::remember('libros_categorias', 1, function () {

            $c = [];
            $libros = Libro::select('categoria')->get();
            // Recorrer todos los libros, para cada uno separar las categorias por coma, y esas son contadas en $categorias
            // ejemplo: si la categoria es 'Monografías, cuentos', pues tiene 2 categorías.
            // entonces hay que agregar en $categorias la clave 'Monografías' y el contador a 1, y la clave 'Cuentos' y el contador a 1
            // si en siguiente libro es también una monografía, aumenta el contador de $categorias['Monografías']
            //

            foreach ($libros as $libro) {
                $categoriasLibro = explode(',', $libro->categoria);
                foreach ($categoriasLibro as $categoria) {
                    $categoria = trim($categoria);
                    if (!empty($categoria)) {
                        if (isset($c[$categoria])) {
                            $c[$categoria]++;
                        } else {
                            $c[$categoria] = 1;
                        }
                    }
                }
            }

            return $c;
        });

        ksort($categorias);


        $categorias = array_map(function ($nombre, $total) {
            return (object) ['nombre' => $nombre, 'total' => $total];
        }, array_keys($categorias), $categorias);

            //  dd($categorias);


        return Inertia::render('Libros/Index', [
            'filtrado' => $filtro,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias
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
        $publicado =  $libro->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$libro || (!$publicado && !$borrador && !$editor)) {
            abort(404);
        }


        // Obtener libros relacionados por categoría o coincidencia de palabras clave en la descripción
        $relacionados = Libro::where('id', '!=', $libro->id)
            ->where('categoria', $libro->categoria)
            ->where(function ($query) use ($libro) {
                $tituloPalabras = explode(' ', $libro->titulo);
                $palabrasClave = [];

                foreach ($tituloPalabras as $palabra) {
                    // Filtrar palabras de menos de 3 letras y palabras comunes
                    if (strlen($palabra) > 2 && !in_array($palabra, ['y', 'o', 'en'])) {
                        $palabrasClave[] = $palabra;
                    }
                }

                if (!empty($palabrasClave)) {
                    $query->where(function ($query) use ($palabrasClave) {
                        foreach ($palabrasClave as $clave) {
                            // Asignar una puntuación más alta si la palabra clave aparece en el título
                            $query->where('descripcion', 'like', '%' . $clave . '%')
                                ->orWhere('titulo', 'like', '%' . $clave . '%');
                        }
                    });
                }
            })
            ->orderByRaw('(CASE WHEN titulo LIKE ? THEN 2 WHEN descripcion LIKE ? THEN 1 ELSE 0 END) DESC', [$libro->titulo . '%', '%' . $libro->titulo . '%'])
            ->take(8)
            ->get();

        return Inertia::render('Libros/Libro', [
            'libro' => $libro,
            'relacionados' => $relacionados
        ])
            ->withViewData(SEO::from($libro));
    }
}
