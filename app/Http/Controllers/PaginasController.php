<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pagina;
use Inertia\Inertia;

class PaginasController extends Controller
{
    //
    public function index(Request $request) {

         $path = $request->path();

         $pagina = Pagina::where('ruta', $path)->firstOrFail();

         $borrador = request()->has('borrador');
         $publicado =  $pagina->visibilidad == 'P';
         $editor = optional(auth()->user())->can('administrar contenidos');
         if (!$pagina || (!$publicado && !$borrador && !$editor)) {
             abort(404); // Manejo de comunicado no encontrado o no autorizado
         }

         return Inertia::render('Pagina', [
            'pagina' => $pagina,
         ]);
    }
}
