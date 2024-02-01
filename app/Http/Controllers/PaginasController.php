<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;

use Illuminate\Http\Request;
use App\Models\Pagina;
use Inertia\Inertia;
use App\Pigmalion\SEO;

class PaginasController extends Controller
{

    public function show(Request $request)
    {
        $path = $request->path();

        $pagina = Pagina::where('ruta', $path)->first();

        if(!$pagina)
            abort(404);

        $borrador = request()->has('borrador');
        $publicado = $pagina->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$publicado && !$borrador && !$editor) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Pagina', [
            'pagina' => $pagina,
        ])
            ->withViewData(SEO::from($pagina));
    }


}
