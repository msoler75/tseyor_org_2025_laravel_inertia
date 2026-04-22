<?php

namespace App\Http\Controllers;

use App\Models\Galeria;
use Illuminate\Http\Request;
use Inertia\Inertia;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class GaleriaController extends Controller
{
    public static $ITEMS_POR_PAGINA = 12;

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $page = $request->input('page', 1);

        $query = Galeria::select('id', 'titulo', 'descripcion', 'imagen', 'created_at', 'updated_at');

         if ($buscar)
            $query->buscar($buscar);

        $resultados = $query
            ->orderBy('created_at', 'desc')
            ->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        // Agregar imagen_principal a cada galeria
        $resultados->getCollection()->transform(function ($galeria) {
            $galeria->imagen_principal = $galeria->imagen_principal;
            return $galeria;
        });

        return Inertia::render('Galerias/Index', [
            'filtrado' => $buscar,
            'listado' => $resultados,
        ])
            ->withViewData([
                'seo' => new SEOData(
                    title: 'Galerías - Tseyor',
                    description: 'Explora nuestras colecciones de imágenes y arte de la comunidad Tseyor.',
                    image: null
                )
            ]);
    }

    /**
     * Mostrar la página de una galería específica.
     *
     * @param  string $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        if (is_numeric($id)) {
            $galeria = Galeria::with(['items.nodo', 'items.user'])->findOrFail($id);
        } else {
            $galeria = Galeria::with(['items.nodo', 'items.user'])->where('slug', $id)->firstOrFail();
        }

        return Inertia::render('Galerias/Galeria', [
            'galeria' => $galeria,
        ])
            ->withViewData([
                'seo' => new SEOData(
                    title: $galeria->titulo . ' - Galerías Tseyor',
                    description: $galeria->descripcion ?? 'Explora esta galería de imágenes.',
                    image: $galeria->imagen_principal
                )
            ]);
    }

    /**
     * Devolver la información de una galería específica en formato JSON (API).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiShow($id)
    {
         if (is_numeric($id)) {
            $galeria = Galeria::with(['items.nodo', 'items.user'])->findOrFail($id);
        } else {
            $galeria = Galeria::with(['items.nodo', 'items.user'])->where('slug', $id)->firstOrFail();
        }

        return response()->json([
            'titulo' => $galeria->titulo,
            'descripcion' => $galeria->descripcion,
            'imagen' => asset($galeria->imagen_principal),
            'items' => $galeria->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'titulo' => $item->titulo,
                    'descripcion' => $item->descripcion,
                    'orden' => $item->orden,
                    'imagen' => $item->nodo ? asset($item->nodo->ubicacion) : null,
                    'user' => $item->user ? [
                        'id' => $item->user->id,
                        'name' => $item->user->name,
                        'slug' => $item->user->slug ?? null,
                        'url' => route('usuario', ['slug' => $item->user->slug ?? $item->user->id])
                    ] : null,
                ];
            }),
        ]);
    }
}
