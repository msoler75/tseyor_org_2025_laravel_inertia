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
use App\Pigmalion\Markdown;

class PaginasController extends Controller
{

    public function show(Request $request)
    {
        $path = $request->path();

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
        return Inertia::render(
            'Portada',
            [
                'stats' => Inertia::lazy(function () {
                    $cc = Comunicado::where('visibilidad', 'P')->count();
                    return
                        [
                            'comunicados' => $cc,
                            'paginas' => $cc * 12 + $cc % 7,
                            'libros' => Libro::where('visibilidad', 'P')->count(),
                            'usuarios' => User::count(),
                            'audios' => Audio::where('visibilidad', 'P')->count(),
                            'entradas' => Entrada::where('visibilidad', 'P')->count(),
                            'videos' => Video::where('visibilidad', 'P')->count(),
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
                            'comunicados' => Comunicado::where('visibilidad', 'P')->count(),
                            'libros' => Libro::where('visibilidad', 'P')->count(),
                            'audios' => Audio::where('visibilidad', 'P')->count(),
                            'entradas' => Entrada::where('visibilidad', 'P')->count(),
                            'videos' => Video::where('visibilidad', 'P')->count(),
                            'meditaciones' => Meditacion::where('visibilidad', 'P')->count(),
                            'psicografias' => Psicografia::count()
                        ];
                })
            ]
        )
            ->withViewData(SEO::get('biblioteca'));
    }


}
