<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Centro;
use App\Pigmalion\Countries;
use App\Pigmalion\SEO;

class CentrosController extends Controller
{

    public function index(Request $request)
    {
        $muulasterios =
            Centro::select(['id', 'nombre', 'imagen', 'pais'])
            ->where('nombre', 'like', 'Muulasterio%')
            ->take(7)->get();

        $casas =
            Centro::select(['id', 'nombre', 'imagen', 'pais'])
            ->where('nombre', 'like', 'Casa%')
            ->take(7)->get();

        $paises = Centro::selectRaw('pais as codigo, count(*) as total')
            ->groupBy('pais')
            ->get();

        // Traducir el código ISO del país a su nombre correspondiente
        foreach ($paises as $idx => $pais) {
            $paises[$idx]["nombre"] = Countries::getCountry($pais["codigo"]);
        }

        foreach ($muulasterios as $idx => $centro) {
            $centro->pais = Countries::getCountry($centro->pais);
        }

        foreach ($casas as $idx => $centro) {
            $centro->pais = Countries::getCountry($centro->pais);
        }

        return Inertia::render('Centros/Index', [
            'muulasterios' => $muulasterios,
            'casas' => $casas,
            'paises' => $paises
        ])
            ->withViewData(SEO::get('centros'));
    }

    public function show($id)
    {
        $centro = Centro::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$centro) {
            abort(404); // Manejo de Centro no encontrada
        }

        $centro->pais = Countries::getCountry($centro->pais);

        return Inertia::render('Centros/Centro', [
            'centro' => $centro
        ])
            ->withViewData(SEO::from($centro));
    }
}
