<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pagina;
use Inertia\Inertia;
use App\Pigmalion\SEO;
use App\Pigmalion\DiskUtil;
use Illuminate\Support\Facades\Cache;
use App\Models\Comunicado;
use App\Models\Libro;
use App\Models\Audio;
use App\Models\User;
use App\Models\Entrada;
use App\Models\Meditacion;
use App\Models\Psicografia;
use App\Models\Video;
use App\Models\Galeria;
use App\Models\Centro;
use App\Models\Evento;
use App\Pigmalion\Markdown;

class PaginasController extends Controller
{
    public static $ITEMS_POR_PAGINA = 20;

    // esto se usa solo par MCP
    public function index(Request $request)
    {
        $page = $request->input("page", 1);
        $buscar = $request->input('buscar');
        $query = Pagina::select(['ruta', 'titulo', 'visibilidad', 'updated_at'])
            ->publicada();

        if($buscar)
            $query->buscar($buscar);
        else
            $query->orderBy('ruta');

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

        $query = Pagina::select(['ruta', 'titulo', 'imagen', 'descripcion', 'visibilidad', 'updated_at'])
            ->publicada()
            // debe tener el campo texto
            ->where('descubre', TRUE);

       $query->orderBy('ruta');

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


    public function filosofia()
    {
        return Inertia::render('Filosofia')
            ->withViewData(SEO::get('filosofia'));
    }

    public function temas()
    {
        $categorias = [
            ['key' => 'El cambio de era', 'icono' => 'ph:planet-duotone'],
            ['key' => 'Quiénes somos realmente', 'icono' => 'ph:sparkle-duotone'],
            ['key' => 'El camino de autodescubrimiento', 'icono' => 'ph:eye-duotone'],
            ['key' => 'Hacia las Sociedades Armónicas', 'icono' => 'ph:users-three-duotone'],
        ];

        $categoriaPorSlug = [
            'confederacion-de-mundos-habitados-de-la-galaxia' => 'El cambio de era',
            'el-rayo-sincronizador' => 'El cambio de era',
            'salto-cuantico' => 'El cambio de era',
            'especializacion' => 'Quiénes somos realmente',
            'la-autoobservacion' => 'El camino de autodescubrimiento',
            'retroalimentacion' => 'Hacia las Sociedades Armónicas',
            'espejos' => 'Hacia las Sociedades Armónicas',
            'las-sociedades-armonicas' => 'Hacia las Sociedades Armónicas',
        ];

        $paginas = Pagina::select(['ruta', 'titulo', 'imagen', 'descripcion', 'orden'])
            ->publicada()
            ->where('filosofia', TRUE)
            ->orderBy('orden')
            ->get();

        $groups = [];
        foreach ($paginas as $pagina) {
            $cat = $categoriaPorSlug[$pagina->ruta] ?? 'Otras';
            $groups[$cat][] = $pagina;
        }

        $paginasPorCategoria = [];
        foreach ($categorias as $cat) {
            if (isset($groups[$cat['key']])) {
                $paginasPorCategoria[] = [
                    'categoria' => $cat['key'],
                    'icono' => $cat['icono'],
                    'paginas' => $groups[$cat['key']],
                ];
            }
        }

        return Inertia::render('Filosofia/Temas', [
            'paginasPorCategoria' => $paginasPorCategoria,
        ])
            ->withViewData(SEO::get('filosofia'));
    }

    public function portada($component = 'Portada')
    {
        $hay_proximos_eventos = Evento::publicado()
            ->where(function($q) {
                $q->where('fecha_inicio', '>=', now())
                  ->orWhere(function($q2) {
                      $q2->where('fecha_inicio', '<=', now())
                         ->where('fecha_fin', '>=', now());
                  });
            })
            ->exists();

        $stats = Cache::remember('stats_portada', now()->addDay(), function () {
            return [
                'comunicados' => Comunicado::publicado()->count(),
                'libros' => Libro::publicado()->count(),
                'usuarios' => User::count(),
                'audios' => Audio::publicado()->count(),
                'entradas' => Entrada::publicado()->count(),
                'meditaciones' => Meditacion::publicado()->count(),
                'videos' => Video::publicado()->count(),
                'centros' => Centro::count()
            ];
        });

        $usuariosImagenes = DiskUtil::obtenerImagenes('/almacen/medios/portada/usuarios');
        shuffle($usuariosImagenes);

        return Inertia::render(
            $component,
            [
                'hayProximosEventos' => $hay_proximos_eventos,
                'stats' => $stats,
                'paginasFilosofia' => Pagina::select(['ruta', 'titulo', 'imagen', 'descripcion'])
                    ->publicada()
                    ->where('filosofia', TRUE)
                    ->where(function ($q) {
                        $q->where('ruta', 'like', '%rayo%')
                          ->orWhere('ruta', 'like', '%sociedades%')
                          ->orWhere('ruta', 'like', '%confederacion%')
                          ->orWhere('ruta', 'like', '%observacion%');
                    })
                    ->orderBy('orden')
                    ->limit(4)
                    ->get(),
                'entradasRecientes' => Entrada::publicada()
                    ->latest('published_at')
                    ->limit(24)
                    ->get(['slug', 'titulo', 'imagen', 'descripcion', 'published_at'])
                    ->map(fn($e) => [
                        'slug' => $e->slug,
                        'titulo' => $e->titulo,
                        'imagen' => $e->imagen,
                        'descripcion' => $e->descripcion,
                        'fecha' => $e->published_at ? date('j M Y', strtotime($e->published_at)) : '',
                        'ruta' => route('entrada', $e->slug),
                    ]),
                'usuariosImagenes' => $usuariosImagenes,
                'meditaciones' => Audio::publicado()
                    ->latest()
                    ->limit(20)
                    ->get(['id', 'titulo', 'slug', 'audio']),
            ]
        );
    }

    public function biblioteca()
    {
        return Inertia::render(
            'Biblioteca',
            [
                'stats' => Cache::remember('stats_biblioteca', now()->addDay(), function () {
                    return [
                        'comunicados' => Comunicado::publicado()->count(),
                        'libros' => Libro::publicado()->count(),
                        'audios' => Audio::publicado()->count(),
                        'entradas' => Entrada::publicado()->count(),
                        'videos' => Video::publicado()->count(),
                        'meditaciones' => Meditacion::publicado()->count(),
                        'psicografias' => Psicografia::count(),
                        'galerias' => Galeria::count(),
                    ];
                })
            ]
        )
            ->withViewData(SEO::get('biblioteca'));
    }
}
