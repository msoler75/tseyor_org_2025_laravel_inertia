<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Guia;
use App\Models\Libro;
use App\Pigmalion\SEO;

class GuiasController extends Controller
{
    public function index(Request $request)
    {
        $guias = Guia::select(['nombre', 'slug', 'descripcion', 'imagen'])->take(50)->get();

        return Inertia::render('Guias/Index', [
            'guias' => $guias
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


        $guias = Guia::select(['nombre', 'slug'])->take(50)->get();

        try {

            $data = json_decode($guia->libros, true);

            $libros_texto = $data['texto'];

            $libros_slug = $data['items'];

            $libros = Libro::whereIn('slug', $libros_slug)->get();
        }
        catch(\Exception $e) {

        }

        return Inertia::render('Guias/Guia', [
            'guia' => $guia,
            'guias' => $guias,
            'libros_texto'=>$libros_texto ?? null,
            'libros' => [
                'texto' => $data['texto'] ?? null,
                'items' => $libros ?? []
            ]
        ])
       ->withViewData(SEO::from($guia));
    }
}
