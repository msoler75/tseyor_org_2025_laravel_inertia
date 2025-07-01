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
use App\Pigmalion\StorageItem;


class CentrosController extends Controller
{
    public static $ITEMS_POR_PAGINA = 50;

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $query = Centro::select(['id', 'nombre', 'imagen', 'descripcion', 'pais']);

        if ($buscar) {
            $ids = Centro::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('centros.id', $ids);
        } else {
            $query->latest('created_at');
        }

        $resultados = $query->get();

        // Traducir el código ISO del país a su nombre correspondiente
        $paises = [];
        foreach ($resultados as $centro) {
            $codigo = $centro->pais;
            $centro->pais = Countries::getCountry($centro->pais);
            $paises[] = ["codigo"=>$codigo, "nombre"=>$centro->pais];
        }

        return Inertia::render('Centros/Index', [
            'centros' => $resultados,
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

        $loc = new StorageItem($centro->getCarpetaMedios());
        $imagenes = $loc->listImages();

        if (!in_array($centro->imagen, $imagenes))
            array_unshift($imagenes, $centro->imagen);

        $entradas = Entrada::select(['id', 'titulo', 'slug', 'descripcion', 'imagen'])->whereIn('slug', preg_split("/[\r\n\t\s,]+/", $centro->entradas, -1, PREG_SPLIT_NO_EMPTY))->where('visibilidad', 'P')->get();
        $libros = Libro::whereIn('slug', preg_split("/[\r\n\t\s,]+/", $centro->libros, -1, PREG_SPLIT_NO_EMPTY))->where('visibilidad', 'P')->get();

        $centro->pais = $centro->nombrePais; // Countries::getCountry($centro->pais);

        return Inertia::render('Centros/Centro', [
            'centro' => $centro,
            'imagenes' => $imagenes,
            'entradas' => $entradas,
            'libros'    => $libros
        ])
            ->withViewData(SEO::from($centro));
    }
}
