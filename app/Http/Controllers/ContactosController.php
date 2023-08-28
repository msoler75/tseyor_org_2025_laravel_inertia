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
            'paises' => $paises,
            'apiKey' => env('GOOGLE_MAPS_API_KEY', ''),
        ])
            ->withViewData(SEO::get('contactos'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $contacto = Contacto::findOrFail($id);
        } else {
            $contacto = Contacto::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $contacto->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar directorio');
        if (!$contacto || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Manejo de comunicado no encontrado o no autorizado
        }

        $contacto->pais = Countries::getCountry($contacto->pais);

        return Inertia::render('Contactos/Contacto', [
            'contacto' => $contacto
        ])
            ->withViewData(SEO::from($contacto));
    }


    public static function obtenerCoordenadas($direccion)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY', ''); // Reemplaza con tu propia API key de Google Maps

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
