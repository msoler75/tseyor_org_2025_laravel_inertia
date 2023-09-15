<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Termino;
use App\Pigmalion\SEO;

class TerminosController extends Controller
{
    public function index(Request $request)
    {
        $listado = Termino::select(['slug', 'nombre'])
            ->latest()->paginate(20);

            $todos = Termino::select('nombre')->get();

            $letras = [];

            foreach ($todos->toArray() as $item) {
                $letras[strtoupper(substr($item['nombre'], 0, 1))] = 1;
            }

            $letras = array_keys($letras);

        return Inertia::render('Terminos/Index', [
            'listado' => $listado,
            'letras'=>$letras
        ])
            ->withViewData(SEO::get('glosario'));
    }


    public function show($id)
    {
        if (is_numeric($id)) {
            $termino = Termino::findOrFail($id);
        } else {
            $termino = Termino::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $termino->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$termino || (!$publicado && !$borrador && !$editor)) {
            abort(404);
        }

        return Inertia::render('Terminos/Termino', [
            'termino' => $termino
        ])
            ->withViewData(SEO::from($termino));
    }
}
