<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Experiencia;
use App\Pigmalion\BusquedasHelper;
use App\Pigmalion\SEO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
// use App\Mail\ExperienciasNuevaEmail;

class ExperienciasController extends Controller
{
    public static $ITEMS_POR_PAGINA = 12;

    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $page = $request->input('page', 1);

        // devuelve los items recientes segun la busqueda
        $resultados = $buscar ?
            Experiencia::search($buscar)->paginate(self::$ITEMS_POR_PAGINA, 'page', $page)
            :
            Experiencia::latest()->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page);

        // obtiene los items sin busqueda
        if (!$buscar) {
            $resultados = Experiencia::select(['id', 'nombre', 'fecha', 'lugar', 'texto', 'created_at', 'categoria'])
                ->where('visibilidad', 'P')
                ->orderBy('updated_at', 'desc');
        }

        // parámetros
        if ($categoria)
            $resultados = $resultados->where('categoria', $categoria);

        // ver si el usuario es iniciado
        $user = auth()->user();
        if (!$user || (!$user->esIniciado() && !$user->can('administrar experiencias')))
            $resultados->where('categoria', '!=', Experiencia::$CATEGORIA_INTERIORIZACION);

        $resultados = $resultados
            ->paginate(ExperienciasController::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends(['buscar' => $buscar, 'categoria' => $categoria]);

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        $categorias = (new Experiencia())->getCategorias();

        return Inertia::render('Experiencias/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('experiencias'));
    }

    /**
     * Muestra el formulario de nueva experiencia
     */

    public function nueva()
    {
        $categorias = Experiencia::$categorias;

        return Inertia::render('Experiencias/NuevaExperiencia', ['categorias' => $categorias])
            ->withViewData(SEO::get('experiencia.nueva'));
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

        if (!$editor && Gate::denies('view', $experiencia)) {
            abort(403, "No puedes ver esta experiencia");
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
            'nombre' => 'required|max:256',
            'fecha' => 'nullable|max:256',
            'lugar' => 'nullable|max:256',
            'user_id' => 'nullable',
            'categoria' => 'required',
            'texto' => 'required|min:64|max:65000',
            // solo archivos aceptables: pdf, word, doc, docx:
            'archivo' => 'nullable|mimes:txt,pdf,doc,docx'
        ]);

        if ($data['fecha'] == null) {
            $data['fecha'] = Carbon::now()->format('d M Y');
        }


        // se tiene que guardar el archivo, si lo hay, en la carpeta medios/experiencias/{año} del disco public de storage
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            // se ha de poner en la carpeta según el año actual
            $carpeta = "medios/experiencias/" . date("Y");
            $rutaArchivo = $archivo->store($carpeta, 'public');
            $data['archivo'] = $rutaArchivo;
        }

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
            // Redirigir al usuario a la página anterior with a success message
            return redirect()->back()->with('success', 'La experiencia se ha guardado correctamente');
        } else {
            // Devolver un objeto JSON con los errores de validación
            Log::error("Formulario de experiencias. No se pudo guardar la experiencia ", $data);
            return redirect()->back()->withErrors(['No se pudo guardar la experiencia, inténtalo de nuevo']);
        }
    }
}
