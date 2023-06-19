<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contacto;
use App\Pigmalion\Countries;

class ContactosController extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->input('buscar');
        $pais = $request->input('pais');

        $resultados = $pais ?
            Contacto::where('pais', '=', $pais)
            ->paginate(50)->appends(['pais' => $pais])
            : ($filtro ? Contacto::where('nombre', 'like', '%' . $filtro . '%')
                ->orWhere('pais', 'like', '%' . $filtro . '%')
                ->orWhere('poblacion', 'like', '%' . $filtro . '%')
                ->paginate(50)->appends(['buscar' => $filtro])
                :
                Contacto::latest()->paginate(50)
            );

        $paises = Contacto::selectRaw('pais as codigo, count(*) as total')
            ->groupBy('pais')
            ->get();

        // Traducir el código ISO del país a su nombre correspondiente
        foreach ($paises as $idx => $pais) {
            $paises[$idx]["nombre"] = Countries::getCountry($pais["codigo"]);
        }

        foreach ($resultados as $idx => $centro) {
            $centro->pais = Countries::getCountry($centro->pais);
        }

        return Inertia::render('Contactos/Index', [
            'filtrado' => $filtro,
            'paisActivo' => $pais,
            'listado' => $resultados,
            'paises' => $paises
        ]);
    }

    public function show($id)
    {
        $contacto = Contacto::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$contacto) {
            abort(404); // Manejo de Contacto no encontrada
        }

        return Inertia::render('Contactos/Contacto', [
            'contacto' => $contacto
        ]);
    }
}
