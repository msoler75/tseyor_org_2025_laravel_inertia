<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Evento;

class CursosController extends Controller
{
    public function index()
    {
        // obtiene los próximos eventos de tipo "Cursos"
        $proximosCursos = Evento::select('titulo', 'slug', 'descripcion', 'imagen', 'fecha_inicio')
            ->where('categoria', 'Cursos')->latest()->take(4)->get();

        return Inertia::render('Cursos/Index', [
            'proximosCursos' => $proximosCursos
        ]);
    }
}
