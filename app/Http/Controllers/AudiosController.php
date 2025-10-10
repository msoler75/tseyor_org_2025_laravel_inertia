<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Audio;
use App\Pigmalion\SEO;

class AudiosController extends Controller
{
    public static $ITEMS_POR_PAGINA = 50;

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $page = $request->input('page', 1);

        $query = Audio::select(['id', 'slug', 'titulo', 'descripcion', 'audio', 'enlace', 'updated_at', 'categoria'])
            ->withFavorito()
            ->publicado();

        if ($buscar)
            $query->buscar($buscar);
        else if ($categoria=='_') // todos por orden alfabético
            $query->orderByRaw('LOWER(titulo)');
        else if (strcasecmp($categoria, 'favoritos') === 0)
            $query->whereNotNull('favoritos.id');
        else if($categoria)
            $query->where('categoria', $categoria);
        else
            $query->latest();

        $resultados = $query->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        $categorias = Audio::selectRaw('categoria as nombre, count(*) as total')
            ->groupBy('categoria')
            ->get();

        return Inertia::render('Audios/Index', [
            'filtrado' => $buscar,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('audios'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $audio = Audio::findOrFail($id);
        } else {
            $audio = Audio::where('slug', $id)->firstOrFail();
        }

        if (!$audio) {
            abort(404); // Manejo de audio no encontrada
        }

        return Inertia::render('Audios/Audio', [
            'audio' => $audio
        ])
            ->withViewData(SEO::from($audio));
    }
}
