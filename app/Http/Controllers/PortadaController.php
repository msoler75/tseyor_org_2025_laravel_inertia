<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Comunicado;
use App\Models\Libro;
use App\Models\Audio;
use App\Models\User;
use App\Models\Entrada;
use App\Models\Centro;

class PortadaController extends Controller
{
    /**
     * Show the form for creating the resource.
     */

    public function index()
    {
        return Inertia::render(
            'Portada',
            [
                'stats' => Inertia::lazy(function () {
                    $cc = Comunicado::count();
                    return
                        [
                            'comunicados' => $cc,
                            'paginas' => $cc *12 + $cc %7,
                            'libros' => Libro::count(),
                            'usuarios' => User::count(),
                            'audios' => Audio::count(),
                            'entradas' => Entrada::count(),
                            'videos' => 20,
                            'centros' => Centro::count()
                        ];
                })
            ]
        );
    }
}
