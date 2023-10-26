<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Experiencia;
use App\Pigmalion\Busquedas;
use App\Pigmalion\SEO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// use App\Mail\ExperienciasNuevaEmail;

class ExperienciasController extends Controller
{
    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $resultados = Experiencia::search($buscar);
        } else {
            // obtiene los items sin busqueda
            $resultados = Experiencia::select(['id', 'nombre', 'fecha', 'lugar', 'texto', 'updated_at', 'categoria'])
                ->where('visibilidad', 'P')
                ->orderBy('updated_at', 'desc');
        }

        // parámetros
        if ($categoria)
            $resultados = $resultados->where('categoria', $categoria);

        $resultados = $resultados
            ->paginate(12)
            ->appends(['buscar' => $buscar, 'categoria' => $categoria]);

        if ($buscar)
            Busquedas::formatearResultados($resultados, $buscar);

        $categorias = (new Experiencia())->getCategorias();

        return Inertia::render('Experiencias/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('experiencias'));
    }



    public function show($id)
    {
        $experiencia = Experiencia::findOrFail($id);

        $borrador = request()->has('borrador');
        $publicado = $experiencia->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar experiencias');
        if (!$experiencia || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        $experiencia['titulo'] = $experiencia['nombre'];

        return Inertia::render('Experiencias/Experiencia', [
            'experiencia' => $experiencia,
        ])
            ->withViewData(SEO::from($experiencia));
    }

    //
    public function store(Request $request)
    {
        // Validar los datos
        $data = $request->validate([
            'nombre' => 'required|max:255',
            'fecha' => 'nullable',
            'lugar' => 'nullable',
            'user_id' => 'nullable',
            'categoria' => 'required',
            'texto' => 'required|max:65000',
        ]);

        if ($data['fecha'] == null)
            $data['fecha'] = date("Y-m-d");

        // Crear una nueva instancia de Inscripcion y guardarla en la base de datos
        $experiencia = Experiencia::create($data);
        $destinatario = 'secretaria@tseyor.org';

        // mensaje al destinatario
        /*Mail::to($destinatario)
            ->cc('pigmalion@tseyor.org')
            ->queue(
                new InscripcionEmail(
                    $data['nombre'],
                    $fecha_esp,
                    $data['ciudad'],
                    $data['region'],
                    $data['pais'],
                    $data['email'],
                    $data['telefono'],
                    $data['comentario'],
                )
            );
            */

        if ($experiencia) {
            // Redirigir al usuario a la página anterior con un mensaje de éxito
            return redirect()->back()->with('success', 'La experiencia se ha guardado correctamente');
        } else {
            // Devolver un objeto JSON con los errores de validación
            Log::error("Formulario de experiencias. No se pudo guardar la experiencia ", $data);
            return redirect()->back()->withErrors(['No se pudo guardar la experiencia, inténtalo de nuevo']);
        }
    }
}
