<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Comunicado;
use App\Models\Libro;
use App\Models\Audio;
use App\Models\User;

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
                    return
                        [
                            'comunicados' => Comunicado::count(),
                            'libros' => Libro::count(),
                            'usuarios' => User::count(),
                            'audios' => Audio::count()
                        ];
                })
            ]
        );
    }
}
