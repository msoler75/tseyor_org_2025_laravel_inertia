<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Evento;
use App\Models\Libro;
use App\Pigmalion\SEO;

class CursosController extends Controller
{
    public function index()
    {
        // obtiene los próximos eventos de tipo "Cursos"
        $proximosCursos = Evento::select('titulo', 'slug', 'descripcion', 'imagen', 'fecha_inicio')
            ->where('categoria', 'Cursos')->latest()->take(2)->get();

        $libro = Libro::where('slug', 'curso-holistico-tseyor')->first();
        $libroGuias = Libro::where('slug', 'los-guias-estelares')->first();

        return Inertia::render('Cursos/Index', [
            'proximosCursos' => $proximosCursos,
            'libro' => $libro,
            'libroGuias' => $libroGuias,
        ])
        ->withViewData(SEO::get('cursos'));
    }
}
