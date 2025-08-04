<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\InscripcionEmail;
use App\Mail\InscripcionConfirmacionEmail;
use App\Notifications\InscripcionAsignada;
use Inertia\Inertia;


class InscripcionesController extends Controller
{

    public static $ITEMS_POR_PAGINA = 20;

    // nueva inscripción
    public function store(Request $request)
    {
        // Validar los datos
        $dataValidated = $request->validate([
            'nombre' => 'required|max:255',
            'dia' => 'nullable|integer|min:1|max:31',
            'mes' => 'nullable|integer|min:1|max:12',
            'anyo' => 'required|integer|min:1900|max:' . (date('Y') - 10),
            'ciudad' => 'required|max:255',
            'region' => 'required|max:255',
            'pais' => 'required|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'required|nullable|max:255',
            'comentario' => 'nullable'
        ]);

        if(!$dataValidated['dia'])
        $dataValidated['dia'] = 1;

        if(!$dataValidated['mes'])
        $dataValidated['mes'] = 1;

        // Construir la fecha
        $fecha_sql = $dataValidated['anyo'] . "-" . $dataValidated['mes'] . "-" . $dataValidated['dia'];

        $data = [
            'nombre' => $dataValidated['nombre'],
            'fecha_nacimiento' => $fecha_sql,
            'ciudad' => $dataValidated['ciudad'],
            'region' => $dataValidated['region'],
            'pais' => $dataValidated['pais'],
            'email' => $dataValidated['email'],
            'telefono' => $dataValidated['telefono'] ?? "",
            'comentario' => $dataValidated['comentario'] ?? "",
            'ultima_actividad' => now() // Establecer fecha inicial de actividad
        ];

        // Crear una nueva instancia de Inscripcion y guardarla en la base de datos
        $inscripcion = Inscripcion::create($data);
        $destinatario = 'secretaria@tseyor.org';

        // mensaje de confirmación al autor
        Mail::to($data['email'])
            ->bcc('msgp753@gmail.com')
            ->queue(
                new InscripcionConfirmacionEmail(
                    $data['nombre'],
                    $dataValidated['dia'],
                    $dataValidated['mes'],
                    $dataValidated['anyo'],
                    $data['ciudad'],
                    $data['region'],
                    $data['pais'],
                    $data['email'],
                    $data['telefono'],
                    $data['comentario'],
                )
            );

        // mensaje al destinatario
        Mail::to($destinatario)
            ->bcc('msgp753@gmail.com')
            ->queue(
                new InscripcionEmail($inscripcion)
            );

        if ($inscripcion) {
            // Redirigir al usuario a la página anterior con un mensaje de éxito
            return redirect()->back()->with('success', 'La inscripción se ha guardado correctamente');
        } else {
            // Devolver un objeto JSON con los errores de validación
            Log::error("Inscripción. No se pudo guardar la inscripción ", $data);
            return redirect()->back()->withErrors(['No se pudo guardar la inscripción, inténtalo de nuevo']);
        }
    }





    /**
     * Muestra formulario de gestión para una inscripción específica
     */
    /*public function gestion(Inscripcion $inscripcion)
    {
        // Verificar que el usuario puede gestionar esta inscripción
        if (!Auth::user()->hasRole('admin') && $inscripcion->user_id !== Auth::id()) {
            abort(403, 'No tienes permisos para gestionar esta inscripción');
        }

        $inscripcion->load('usuarioAsignado');

        return Inertia::render('Inscripciones/Gestion', [
            'inscripcion' => $inscripcion,
            'estadosElegibles' => Inscripcion::getEstadosElegibles(),
            'puedeRebotar' => $inscripcion->user_id === Auth::id() || Auth::user()->hasRole('admin')
        ]);
    }*/

    /**
     * Asigna una inscripción a un usuario
     */
    public function asignar(Request $request, Inscripcion $inscripcion)
    {
        $this->authorize('admin');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'motivo' => 'nullable|string|max:500'
        ]);

        $usuario = User::findOrFail($request->user_id);

        // Verificar límite de inscripciones por usuario
        $inscripcionesActivas = Inscripcion::where('user_id', $usuario->id)
            ->whereIn('estado', config('inscripciones.asignacion.estados_activos'))
            ->count();

        if ($inscripcionesActivas >= config('inscripciones.asignacion.max_inscripciones_por_usuario')) {
            return back()->withErrors([
                'user_id' => 'El usuario ya tiene el máximo de inscripciones activas permitidas.'
            ]);
        }

        $inscripcion->asignarA($usuario, $request->motivo ?? 'Asignación manual');

        return back()->with('success', 'Inscripción asignada correctamente');
    }

    /**
     * Actualiza el estado de una inscripción
     */
    public function actualizarEstado(Request $request, Inscripcion $inscripcion)
    {
        // Verificar permisos
        if (!Auth::user()->hasRole('admin') && $inscripcion->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'estado' => 'required|in:' . implode(',', array_keys(Inscripcion::getEstadosDisponibles())),
            'comentario' => 'nullable|string|max:1000'
        ]);

        $inscripcion->actualizarEstado($request->estado, $request->comentario ?? '');

        return back()->with('success', 'Estado actualizado correctamente');
    }

    /**
     * Rebota una inscripción
     */
    public function rebotar(Request $request, Inscripcion $inscripcion)
    {
        // Solo el usuario asignado puede rebotar
        if ($inscripcion->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'motivo' => 'required|string|min:10|max:500'
        ]);

        $inscripcion->rebotar($request->motivo);

        return response()->json([
            'message' => 'Inscripción rebotada correctamente',
            'inscripcion' => $inscripcion->fresh()
        ]);
    }

    /**
     * Lista inscripciones asignadas al usuario actual
     */
    public function misAsignaciones(Request $request)
    {
        $buscar = $request->input('buscar');
        $page = $request->input('page', 1);

        $query = Inscripcion::where('user_id', Auth::id());

         if ($buscar) {
            $ids = Inscripcion::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('inscripciones.id', $ids);
        }

        // aquí ya aplicamos criterios de ordenación, siendo primeras las inscripciones "abiertas" (no tienen estado finalizado)
        $estadosFinalizados = ['finalizado', 'duplicada', 'nointeresado', 'abandonado', 'nocontesta'];
        $query->orderByRaw('CASE WHEN estado IN ("' . implode('","', $estadosFinalizados) . '") THEN 1 ELSE 0 END')
              ->orderByDesc('fecha_asignacion');

        $resultados = $query->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        //$inscripciones = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return Inertia::render('Inscripciones/MisAsignaciones', [
            'listado' => $resultados,
            'estadosDisponibles' => $this->getEstadosDisponiblesParaMisAsignaciones(),
            'estadosNoElegibles' => Inscripcion::getEstadosNoElegibles(),
            'umbralesdias' => [
                'asignado_urgente' => config('inscripciones.umbrales.asignado_urgente', 3),
                'contactado_urgente' => config('inscripciones.umbrales.contactado_urgente', 7),
                'encurso_seguimiento' => config('inscripciones.umbrales.encurso_seguimiento', 30)
            ],
            'filtrado' => $buscar,
        ]);
    }

    /**
     * Busca usuarios disponibles para asignación
     */
    public function buscarUsuarios(Request $request)
    {
        $query = $request->get('q', '');

        $usuarios = User:://role(config('inscripciones.asignacion.rol_elegible'))
            where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($usuarios);
    }

    /**
     * Asignación masiva de inscripciones
     */
    public function asignarMasiva(Request $request)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'inscripciones_ids' => 'required|array',
            'inscripciones_ids.*' => 'exists:inscripciones,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $usuario = User::find($validated['user_id']);
        $inscripciones = Inscripcion::whereIn('id', $validated['inscripciones_ids'])->get();

        $asignadas = 0;
        foreach ($inscripciones as $inscripcion) {
            if (!$inscripcion->user_id) { // Solo asignar si no está ya asignada
                $inscripcion->asignarA($usuario, 'Asignación masiva desde dashboard');
                $asignadas++;
            }
        }

        return redirect()->back()->with('success', "{$asignadas} inscripciones asignadas a {$usuario->name}");
    }

     /**
     * Añade un comentario rápido a la inscripción usando Inscripcion::comentar
     */
    public function agregarComentario(Request $request, Inscripcion $inscripcion)
    {
        // Solo el usuario asignado o admin puede comentar
        if (!Auth::user()->hasRole('admin') && $inscripcion->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'comentario' => 'required|string|max:1000',
        ]);

        // Centraliza la lógica de nota en el modelo
        $inscripcion->comentar($request->comentario, true); // true indica que es actividad del tutor
        $inscripcion->save();

        // Opcional: devolver la inscripción actualizada o solo mensaje
        return response()->json([
            'message' => 'Comentario añadido correctamente',
            'inscripcion' => $inscripcion->fresh(),
        ]);
    }

    /**
     * Actualiza las notas de una inscripción
     */
    public function actualizarNotas(Request $request, Inscripcion $inscripcion)
    {
        // Verificar que el usuario puede gestionar esta inscripción
        if (!Auth::user()->hasRole('admin') && $inscripcion->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'notas' => 'nullable|string'
        ]);

        $inscripcion->notas = $request->notas;
        $inscripcion->ultima_actividad = now(); // Marcar actividad del tutor
        $inscripcion->save();

        return back()->with('success', 'Notas actualizadas correctamente');
    }

    /**
     * Obtiene los estados disponibles personalizados para la vista "Mis Asignaciones"
     */
    private function getEstadosDisponiblesParaMisAsignaciones()
    {
        $estados = Inscripcion::getEstadosDisponibles();

        // Personalizar el estado "asignada" para esta vista específica
        if (isset($estados['asignada'])) {
            $estados['asignada'] = [
                'etiqueta' => 'Asignada a ti',
                'descripcion' => 'Asignada a ti. Pendiente de contactar',
            ];
        }

        return $estados;
    }
}
