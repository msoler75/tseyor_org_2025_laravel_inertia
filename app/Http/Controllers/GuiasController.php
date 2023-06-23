<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Guia;

class GuiasController extends Controller
{
    public function index(Request $request)
    {
        $listado = Guia::select(['nombre', 'slug', 'descripcion', 'imagen'])->take(50)->get();

        $todos = Guia::select(['slug', 'nombre', 'categoria'])->take(100)->get();

        return Inertia::render('Lugares/Index', [
            'listado' => $listado,
            'todos' => $todos
        ]);
    }
}
