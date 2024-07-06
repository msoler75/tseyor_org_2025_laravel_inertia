<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Centro;
use App\Models\Entrada;
use App\Models\Libro;
use App\Pigmalion\Countries;
use App\Pigmalion\SEO;
use Illuminate\Support\Facades\Storage;
use App\Pigmalion\DiskUtil;


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
        if (is_numeric($id)) {
            $centro = Centro::with('contacto')->findOrFail($id);
        } else {
            $centro = Centro::with('contacto')->where('slug', $id)->firstOrFail();
        }

        if (!$centro) {
            abort(404); // Manejo de Centro no encontrada
        }

        $imagenes = DiskUtil::obtenerImagenes($centro->getCarpetaMedios());

        if(!in_array($centro->imagen, $imagenes))
            array_unshift($imagenes, $centro->imagen);

        $entradas = Entrada::select(['id', 'titulo', 'slug', 'descripcion', 'imagen'])->whereIn('slug', preg_split("/[\r\n\t\s,]+/", $centro->entradas, -1, PREG_SPLIT_NO_EMPTY))->where('visibilidad', 'P')->get();
        $libros = Libro::whereIn('slug', preg_split("/[\r\n\t\s,]+/", $centro->libros, -1, PREG_SPLIT_NO_EMPTY))->where('visibilidad', 'P')->get();

        $centro->pais = $centro->nombrePais;// Countries::getCountry($centro->pais);

        return Inertia::render('Centros/Centro', [
            'centro' => $centro,
            'imagenes' => $imagenes,
            'entradas' => $entradas,
            'libros'    => $libros
        ])
            ->withViewData(SEO::from($centro));
    }
}
