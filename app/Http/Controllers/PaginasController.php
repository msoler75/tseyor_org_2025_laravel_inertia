<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pagina;
use Inertia\Inertia;
use App\Pigmalion\SEO;
use App\Models\Comunicado;
use App\Models\Libro;
use App\Models\Audio;
use App\Models\User;
use App\Models\Entrada;
use App\Models\Meditacion;
use App\Models\Psicografia;
use App\Models\Video;
use App\Models\Centro;
use App\Models\Evento;
use App\Pigmalion\Markdown;

class PaginasController extends Controller
{
    public static $ITEMS_POR_PAGINA = 20;

    public function index(Request $request)
    {
        $page = $request->input("page", 1);
        $buscar = $request->input('buscar');
        $query = Pagina::select(['ruta', 'titulo', 'visibilidad', 'updated_at'])
            ->publicada();

        if ($buscar) {
            $ids = Pagina::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('paginas.id', $ids);
        } else $query->orderBy('ruta');

        $resultados = $query->paginate(PaginasController::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        // obtener todas las paginas (usamos cualquier vista, esto va para MCP)
        return Inertia::render('Libros/Index', [
            'listado' => $resultados,
        ]);
    }

    public function descubre(Request $request)
    {
        $page = $request->input("page", 1);
        $buscar = $request->input('buscar');


        $query = Pagina::select(['ruta', 'titulo', 'imagen', 'descripcion', 'visibilidad', 'updated_at'])
            ->publicada()
            // debe tener el campo texto
            ->where('descubre', TRUE);

        if ($buscar) {
            $ids = Pagina::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('paginas.id', $ids);
        } else $query->orderBy('ruta');

        $resultados = $query->paginate(PaginasController::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        // obtener todas las paginas (usamos cualquier vista, esto va para MCP)
        return Inertia::render('Descubre', [
            'listado' => $resultados,
        ]);
    }

    public function show(Request $request, $ruta = null)
    {
        $path = $request->path();

        // se añadió este parámetro opcional para hacer posible los test
        if ($ruta)
            $path = $ruta;

        // \Log::info("PaginasController::show $path");

        $pagina = Pagina::where('ruta', $path)->first();

        if (!$pagina)
            abort(404);

        $borrador = request()->has('borrador');
        $publicado = $pagina->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$publicado && !$borrador && !$editor) {
            abort(404); // Item no encontrado o no autorizado
        }

        // toma el texto de la entrada, obtiene las imagenes, y de cada una de ellas, obtiene las dimensiones
        $imagenes = Markdown::images($pagina->texto);
        $imagenesInfo = ImagenesController::info($imagenes);

        return Inertia::render('Pagina', [
            'pagina' => $pagina,
            'imagenesInfo' => $imagenesInfo
        ])
            ->withViewData(SEO::from($pagina));
    }


    public function portada()
    {
        // Verificar si hay eventos próximos o en curso
        $hay_proximos_eventos = Evento::publicado()
            ->where(function($q) {
                $q->where('fecha_inicio', '>=', now()) // próximos
                  ->orWhere(function($q2) {
                      $q2->where('fecha_inicio', '<=', now()) // en curso
                         ->where('fecha_fin', '>=', now());
                  });
            })
            ->exists();

        return Inertia::render(
            'Portada',
            [
                'hayProximosEventos' => $hay_proximos_eventos,
                'stats' => Inertia::lazy(function () {
                    $cc = Comunicado::publicado()->count();
                    return
                        [
                            'comunicados' => $cc,
                            'paginas' => $cc * 12 + $cc % 7,
                            'libros' => Libro::publicado()->count(),
                            'usuarios' => User::count(),
                            'audios' => Audio::publicado()->count(),
                            'entradas' => Entrada::publicado()->count(),
                            'meditaciones' => Meditacion::publicado()->count(),
                            'videos' => Video::publicado()->count(),
                            'centros' => Centro::count()
                        ];
                })
            ]
        );
    }

    public function biblioteca()
    {
        return Inertia::render(
            'Biblioteca',
            [
                'stats' => Inertia::lazy(function () {
                    return
                        [
                            'comunicados' => Comunicado::publicado()->count(),
                            'libros' => Libro::publicado()->count(),
                            'audios' => Audio::publicado()->count(),
                            'entradas' => Entrada::publicado()->count(),
                            'videos' => Video::publicado()->count(),
                            'meditaciones' => Meditacion::publicado()->count(),
                            'psicografias' => Psicografia::count(),
                            'descubre'=>Pagina::publicado()->where('descubre', TRUE)->count()
                        ];
                })
            ]
        )
            ->withViewData(SEO::get('biblioteca'));
    }
}
