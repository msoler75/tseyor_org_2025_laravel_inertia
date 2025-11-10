<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contacto;
use App\Pigmalion\Countries;
use GuzzleHttp\Client;
use App\Pigmalion\SEO;

class ContactosController extends Controller
{
    public static $ITEMS_POR_PAGINA = 12;

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $pais = $request->input('pais');
        $page = $request->input('page', 1);
        $vista = $request->input('vista', 'mapa');

        $query = Contacto::select(['id', 'nombre', 'slug', 'imagen', 'poblacion', 'pais', 'latitud', 'longitud'])
            ->publicado();

        if ($pais)
            $query->where('pais', $pais);

        if ($buscar)
            $query->buscar($buscar);
        else
            $query->latest('updated_at');

        // dd(Countries::getFuzzyCountries($buscar));
        $resultados =$query->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        $paises = Contacto::selectRaw('pais as codigo, count(*) as total')
            ->publicado()
            ->groupBy('pais')
            ->get();

        // Traducir el código ISO del país a su nombre correspondiente
        foreach ($paises as $idx => $paisx) {
            $paises[$idx]["nombre"] = Countries::getCountry($paisx["codigo"]);
        }

        foreach ($resultados as $idx => $centro) {
            $centro->pais = Countries::getCountry($centro->pais);
        }

        return Inertia::render('Contactos/Index', [
            'filtrado' => $buscar,
            'paisActivo' => $pais,
            'listado' => $resultados,
            'paises' => $paises,
            'apiKey' => config('services.google_maps.apikey'),
            'vista' => $vista
        ])
            ->withViewData(SEO::get('contactos'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $contacto = Contacto::with(['centro', 'usuario'])->findOrFail($id);
        } else {
            $contacto = Contacto::with(['centro', 'usuario'])->where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $contacto->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar directorio');
        if (!$contacto || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        $contacto->pais = Countries::getCountry($contacto->pais);

        return Inertia::render('Contactos/Contacto', [
            'contacto' => $contacto
        ])
            ->withViewData(SEO::from($contacto));
    }


    public static function obtenerCoordenadas($direccion)
    {
        if (app()->environment('local') || app()->environment('testing')) {
            // En desarrollo o testing, no llamar a la API, devolver coordenadas dummy
            return ['latitud' => 0, 'longitud' => 0];
        }
        $apiKey = config('services.google_maps.apikey'); // Reemplaza con tu propia API key de Google Maps
        $client = new Client();
        // Realizar solicitud a la API de geocodificación de Google Maps
        $response = $client->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => $direccion,
                'key' => $apiKey,
            ],
        ]);
        // Obtener el cuerpo de la respuesta y decodificarlo como JSON
        $data = json_decode($response->getBody(), true);
        // Verificar si se obtuvo una respuesta válida
        if ($response->getStatusCode() === 200 && isset($data['results'][0]['geometry']['location'])) {
            $location = $data['results'][0]['geometry']['location'];
            $latitud = $location['lat'];
            $longitud = $location['lng'];
            return ['latitud' => $latitud, 'longitud' => $longitud];
        }
        return null;
    }

    public static function rellenarLatitudYLongitud($contacto)
    {
        $direccion = $contacto->direccion . ", "
            . $contacto->poblacion . ", "
            . $contacto->provincia . ", "
            . $contacto->pais;

        // Obtener las coordenadas de latitud y longitud
        $coordenadas = ContactosController::obtenerCoordenadas($direccion);

        if ($coordenadas !== null) {
            // Guardar las coordenadas en el modelo Contacto
            $contacto->latitud = $coordenadas['latitud'];
            $contacto->longitud = $coordenadas['longitud'];
        } else {
            // Manejar el caso en que no se puedan obtener las coordenadas
            // (por ejemplo, dirección inválida)
        }
    }
}
