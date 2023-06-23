<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Guia;

class GuiasController extends Controller
{
    public function index(Request $request)
    {
        $guias = Guia::select(['nombre', 'slug', 'descripcion', 'imagen'])->take(50)->get();

        return Inertia::render('Guias/Index', [
            'guias' => $guias
        ]);
    }


    public function show($id)
    {
        $guia = Guia::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$guia) {
            abort(404); // Manejo de Centro no encontrada
        }

        return Inertia::render('Guias/Guia', [
            'guia' => $guia
        ]);
    }
}
