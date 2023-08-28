<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Equipo;
use App\Models\Carpeta;
use App\Models\Invitacion;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Nodo;
use App\Models\Acl;
use App\Pigmalion\SEO;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\InvitacionEquipoEmail;
use App\Mail\IncorporacionEquipoEmail;
use Illuminate\Support\Facades\Gate;

class EquiposController extends Controller
{
    /**
     * Lista de equipos
     */
    public function index(Request $request)
    {

        //$urlEquipo = route('equipo', 15);
        // Verificar si el usuario ya tiene una cuenta
        //  return redirect($urlEquipo)->with('message', 'Invitación aceptada. Ya eres parte del equipo.');


        $filtro = $request->input('buscar');
        $categoria = $request->input('categoria');

        $resultados = $categoria ?
            Equipo::withCount('miembros')
            ->where('categoria', '=', $categoria)
            ->paginate(10)->appends(['categoria' => $categoria])
            : ($filtro ? Equipo::withCount('miembros')
                ->where('nombre', 'like', '%' . $filtro . '%')
                ->orWhere('descripcion', 'like', '%' . $filtro . '%')
                ->paginate(10)->appends(['buscar' => $filtro])
                :
                Equipo::withCount('miembros')->latest()->paginate(10)
            );

        $categorias = Equipo::selectRaw('categoria as nombre, count(*) as total')
            ->groupBy('categoria')
            ->get();

        return Inertia::render('Equipos/Index', [
            'filtrado' => $filtro,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('equipos'));
    }

    /**
     * Muestra un equipo o departamento
     */
    public function show($id)
    {
        $equipo = Equipo::with(['miembros' => function ($query) {
            $query->select('users.id', 'users.name as nombre', 'users.slug', 'profile_photo_path as avatar')
                ->orderByRaw("CASE WHEN equipo_user.rol = 'coordinador' THEN 0 ELSE 1 END") // Ordenar los coordinadores primero
                // ->take(30)
            ;
        }]);


        if (is_numeric($id)) {
            $equipo = $equipo->findOrFail($id);
        } else {
            $equipo = $equipo->where('slug', $id)->firstOrFail();
        }

        $carpetas = $equipo->carpetas()->get();

        $ultimosArchivos = [];

        $user = auth()->user();
        $acl = Acl::from($user, ['leer', 'ejecutar']);
        foreach ($carpetas as $carpeta) {
            $nodo = Nodo::desde($carpeta->ruta);
            if ($nodo && Gate::allows('ejecutar', $nodo, $acl)) {
                $archivos = $carpeta->ultimosArchivos();
                $ultimosArchivos = array_merge($ultimosArchivos, $archivos);
            }
        }

        // ordenamos con recientes primero
        usort($ultimosArchivos, function ($a, $b) {
            return $b['fecha_modificacion'] - $a['fecha_modificacion'];
        });


        $solicitud = null;
        $solicitudes = [];

        $soyMiembro = false;
        $soyCoordinador = false;

        if ($user) {

            $soyMiembro = $equipo->esMiembro($user->id);

            // Verificar si el usuario ya es miembro del equipo
            if ($soyMiembro) {

                // verificar si es coordinador del equipo
                if ($equipo->esCoordinador($user->id)) {
                    // soy coordinador de este equipo
                    $soyCoordinador = true;

                    // carga la lista de solicitudes pendientes
                    $solicitudes = Solicitud::with('usuario')
                        ->where('equipo_id', $equipo->id)
                        ->whereNull('fecha_aceptacion')
                        ->whereNull('fecha_denegacion')
                        ->get();
                }
            } else {
                // si no somos miembros, comprobamos la solicitud de ingreso
                // obtenemos la solicitud más reciente (si es que la hay)
                $solicitud = Solicitud::where('user_id', $user->id)
                    ->where('equipo_id', $equipo->id)
                    ->whereNull('fecha_aceptacion')
                    ->whereNull('fecha_denegacion')
                    ->orderBy('created_at', 'desc') // recientes primero
                    ->first();
            }
        }

        $equipo->solicitudesPendientes = $solicitudes;

        return Inertia::render('Equipos/Equipo', [
            'equipo' => $equipo,
            'carpetas' => $carpetas,
            'ultimosArchivos' =>  array_slice($ultimosArchivos, 0, 10),
            'miSolicitud' => $solicitud,
            'soyMiembro' => $soyMiembro,
            'soyCoordinador' => $soyCoordinador
            /*
            'usuarios' => Inertia::lazy(function () use ($id) {
                return Equipo::where('slug', $id)
                    ->orWhere('id', $id)
                    ->firstOrFail()->usuarios()
                    ->orderByRaw("CASE WHEN rol = 'coordinador' THEN 0 ELSE 1 END") // Ordenar los coordinadores primero
                    ->get();
                //return Equipo::findOrFail($id)->usuarios()->get();
            }) */
        ])
            ->withViewData(SEO::from($equipo));
    }


    /**
     * Página de la UTG
     */
    public function index_utg()
    {
        $categoria = 'utg';
        $departamentos = Equipo::where('categoria', '=', $categoria)
            ->take(6)->get();

        return Inertia::render(
            'Utg/Index',
            [
                'departamentos' => $departamentos
            ]
        )
            ->withViewData(SEO::get('utg'));
    }



    /**
     ** Crea un nuevo equipo
     */
    public function store(Request $request)
    {
        // Validar los datos
        $validatedData = $request->validate([
            'nombre' => 'required|max:32',
            'descripcion' => 'max:400',
            'imagen' => 'max:255',
        ]);

        $user = auth()->user();

        // Crear una nueva instancia de Inscripcion y guardarla en la base de datos
        $equipo = Equipo::create([
            'nombre' => $validatedData['nombre'],
            'slug' => Str::slug($validatedData['nombre']),
            'imagen' => $validatedData['imagen'],
            'descripcion' => $validatedData['descripcion'],
            'user_id' => $user->id ?? 1
        ]);

        if ($equipo) {
            return to_route('equipo', $equipo->slug);
        } else {
            // Devolver un objeto JSON con los errores de validación
            return redirect()->back()->withErrors(['msg', 'No se pudo crear el equipo, inténtalo de nuevo']);
        }
    }


    /**
     ** Guarda datos del equipo
     */
    public function update(Request $request, $idEquipo)
    {
        $equipo = Equipo::findOrFail($idEquipo);

        // Verificar si el usuario es un coordinador del equipo
        if (Gate::denies('esCoordinador', $equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Validar los datos
        $validatedData = $request->validate([
            'nombre' => 'required|max:32',
            'descripcion' => 'required|max:400',
            'anuncio' => 'max:400',
            'reuniones' => 'max:255',
            'imagen' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Ajustar las reglas de validación según tus necesidades
            'informacion' => 'max:65000'
        ]);


        // Actualizar los datos del equipo
        $equipo->nombre = trim($validatedData['nombre']);
        $equipo->slug = Str::slug($equipo->nombre);
        $equipo->descripcion = $validatedData['descripcion'];
        $equipo->anuncio = $validatedData['anuncio'];
        $equipo->reuniones = $validatedData['reuniones'];
        $equipo->informacion = $validatedData['informacion'];

        // Subir la nueva imagen (si se proporciona)
        $newImage = $request->file('imagen');
        if ($newImage) {
            $path = $newImage->store('public/imagenes/equipos');
            $equipo->imagen = Storage::url($path);
        }

        $equipo->save();

        return response()->json($equipo->toArray(), 200);
    }


    /**
     * Agrega un usuario a un equipo
     */
    public function addMember($idEquipo, $idUsuario)
    {
        // Obtenemos el usuario y el equipo
        $equipo = Equipo::findOrFail($idEquipo);
        $usuario = User::findOrFail($idUsuario);

        if (Gate::denies('esCoordinador', $equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // agregamos el usuario al equipo
        $equipo->miembros()->syncWithoutDetaching([$idUsuario]);

        return response()->json(['message' => 'El usuario fue añadido al equipo'], 200);
    }


    /**
     * Elimina un usuario de un equipo
     */
    public function removeMember($idEquipo, $idUsuario)
    {
        // Obtenemos el usuario y el equipo
        $equipo = Equipo::findOrFail($idEquipo);
        $usuario = User::findOrFail($idUsuario);

        if (Gate::denies('esCoordinador', $equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // removemos el usuario del equipo
        $equipo->miembros()->detach($idUsuario);

        // por si tuviera permisos, se los removemos
        $equipo->removerPermisosCarpetas($idUsuario);

        // por si hubiera asignación de nuevo coordinador
        $idNuevoCoordinador = $this->asignarNuevoCoordinador($equipo, $idUsuario);

        $message = 'El usuario fue removido del equipo';
        if ($idNuevoCoordinador)
            return response()->json([
                'message' => $message,
                'nuevoCoordinador' => $idNuevoCoordinador
            ]);

        return response()->json(['message' => $message], 200);
    }


    /**
     * Elimina un usuario de un equipo
     */
    public function updateMember($idEquipo, $idUsuario, $rol)
    {
        // Obtenemos el usuario y el equipo que vamos a gestionar
        $usuario = User::findOrFail($idUsuario);
        $equipo = Equipo::findOrFail($idEquipo);

        if (Gate::denies('esCoordinador', $equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if ($rol == 'miembro')
            $rol = NULL;

        // Actualizamos el rol del usuario en el equipo
        $equipo->miembros()->updateExistingPivot($idUsuario, ['rol' => $rol]);

        if ($rol == 'coordinador') {
            $equipo->otorgarPermisosCarpetas($idUsuario);
        } else {
            $equipo->removerPermisosCarpetas($idUsuario);
        }

        $idNuevoCoordinador = $this->asignarNuevoCoordinador($equipo, $idUsuario);

        $message = 'El usuario fue actualizado dentro del equipo';
        if ($idNuevoCoordinador)
            return response()->json([
                'message' => $message,
                'nuevoCoordinador' => $idNuevoCoordinador
            ]);

        return response()->json(['message' => $message], 200);
    }


    /**
     * Comprueba si el equipo no dispone de coordinadores, en tal caso asigna el miembro más antiguo como tal
     */
    private function asignarNuevoCoordinador($equipo, $idUsuarioExcluir = 0)
    {
        // si no quedan coordinadores, hemos de asignar alguno de entre los miembros del equipo, siendo los candidatos los más antiguos
        if (!$equipo->coordinadores()->count()) {
            $miembroAntiguo = $equipo->miembros()
                ->where('users.id', '!=', $idUsuarioExcluir) // filtramos los miembros con id distinto al usuario que se acaba de modificar
                ->oldest('created_at') // ordenamos los miembros por fecha de creación ascendente
                ->first(); // obtenemos el primer miembro de la lista, que será el más antiguo

            if ($miembroAntiguo) {
                // Actualizamos el rol del usuario en el equipo
                $equipo->miembros()->updateExistingPivot($miembroAntiguo->id, ['rol' => 'coordinador']);
                // le damos permisos
                $equipo->otorgarPermisosCarpetas($miembroAntiguo->id);
                return $miembroAntiguo->id;
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // INVITACIONES



    /**
     * Envia invitaciones a usuarios
     * retorna JSON
     */
    public function invite(Request $request, $idEquipo)
    {
        // Validar los datos
        $validatedData = $request->validate([
            'correos' => 'required|array',
            'correos.*' => 'email',
            'usuarios' => 'array'
        ]);

        $equipo = Equipo::findOrFail($idEquipo);


        // Verificar si el usuario es un coordinador del equipo
        if (Gate::denies('esCoordinador', $equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // mandamos invitaciones a los correos proporcionados
        foreach ($validatedData['correos'] as $correo) {
            // Verificar si el correo ya está asociado al equipo
            if ($equipo->miembros->where('email', $correo)->exists()) {
                continue;
            }

            // Verificar si ya se envió una invitación a ese correo
            if (Invitacion::where('equipo_id', $idEquipo)->where('email', $correo)->exists()) {
                continue;
            }

            // Generar el token para la invitación
            $token = sha1(time() . $correo);

            // Verificar si el usuario ya tiene una cuenta
            $usuario = User::where('email', $correo)->first();
            $user_id = optional($usuario)->id ?? null;

            // Crear la invitación en la base de datos
            Invitacion::create([
                'equipo_id' => $idEquipo,
                'email' => $correo,
                'token' => $token,
                'user_id' => $user_id
            ]);

            // Generar la URL firmada para aceptar la invitación
            $aceptarUrl = URL::signedRoute('invitacion.aceptar', ['token' => $token]);

            // Generar la URL firmada para declinar la invitación
            $declinarUrl = URL::signedRoute('invitacion.declinar', ['token' => $token]);

            // Enviar el correo de invitación
            Mail::to($correo)->send(new InvitacionEquipoEmail($equipo, $usuario, $aceptarUrl, $declinarUrl));
        }

        // mandamos invitaciones a los usuarios registrados
        foreach ($validatedData['usuarios'] as $id) {

            // Cargar el usuario
            $usuario = User::find($id);

            if (!$usuario) continue; // el usuario debería existir

            $correo = $usuario->email;
            if (!$correo) continue;

            // Verificar si el usuario ya es miembro del equipo
            if ($equipo->miembros->where('id', $usuario->id)->exists()) {
                continue;
            }

            // Verificar si ya se envió una invitación a ese correo
            if (Invitacion::where('equipo_id', $idEquipo)->where('email', $correo)->exists()) {
                continue;
            }

            // Verificar si ya se envió una invitación a ese usuario
            if (Invitacion::where('equipo_id', $idEquipo)->where('user_id', $id)->exists()) {
                continue;
            }

            // Generar el token para la invitación
            $token = sha1(time() . $id);

            // Crear la invitación en la base de datos
            Invitacion::create([
                'equipo_id' => $idEquipo,
                'email' => $correo,
                'token' => $token,
                'user_id' => $usuario->id
            ]);

            // Generar la URL firmada para aceptar la invitación
            $aceptarUrl = URL::signedRoute('invitacion.aceptar', ['token' => $token]);

            // Generar la URL firmada para declinar la invitación
            $declinarUrl = URL::signedRoute('invitacion.declinar', ['token' => $token]);

            // Enviar el correo de invitación
            Mail::to($correo)->send(new InvitacionEquipoEmail($equipo, $usuario, $aceptarUrl, $declinarUrl));
        }

        return response()->json(['message' => 'Invitaciones enviadas correctamente.'], 200);
    }


    /**
     * Enlace accedido por el usuario
     */
    public function acceptInvitation($token)
    {
        $invitacion = Invitacion::where('token', $token)->firstOrFail();

        $urlEquipo = route('equipo', $invitacion->equipo->slug);

        // Verificar si la invitación ya fue aceptada o declinada previamente
        if ($invitacion->accepted_at || $invitacion->declined_at) {
            return redirect($urlEquipo)->with('message', 'La invitación ya ha sido aceptada o declinada anteriormente.');
        }

        // Verificar si el usuario ya tiene una cuenta
        $usuario = User::where('email', $invitacion->email)->first();
        if ($usuario) {
            // Asociar al usuario al equipo y marcar la invitación como aceptada
            $usuario->equipos()->attach($invitacion->equipo_id);
            $invitacion->update(['accepted_at' => now()]);

            return redirect($urlEquipo)->with('message', 'Invitación aceptada. Ya eres parte del equipo.');
        }

        // Si el usuario no tiene una cuenta, redirigirlo a la página de registro
        $registroUrl = URL::signedRoute('register', ['email' => $invitacion->email]);

        return redirect($registroUrl)->with('message', 'Regístrese para aceptar la invitación.');
    }


    /**
     *  Enlace accedido por el usuario
     */
    public function declineInvitation($token)
    {
        $invitacion = Invitacion::where('token', $token)->firstOrFail();

        $urlEquipo = route('equipo', $invitacion->equipo->slug);

        // Verificar si la invitación ya fue aceptada o declinada previamente
        if ($invitacion->accepted_at || $invitacion->declined_at) {
            return redirect($urlEquipo)->with('message', 'La invitación ya ha sido aceptada o declinada anteriormente.');
        }

        // Marcar la invitación como declinada
        $invitacion->update(['declined_at' => now()]);

        return redirect($urlEquipo)->with('message', 'Invitación declinada.');
    }


    //////////////////////////////////////////////////////////////////////////////////////////
    // SOLICITUDES
    //////////////////////////////////////////////////////////////////////////////////////////


    /**
     * listar historial de solicitudes resueltas
     */
    public function solicitudes($idEquipo)
    {
        // carga el equipo
        $equipo = Equipo::findOrFail($idEquipo);

        if (Gate::denies('esCoordinador', $equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // carga la lista de solicitudes pendientes
        $solicitudes = Solicitud::with('usuario')
            ->with('coordinador')
            ->where('equipo_id', $idEquipo)
            ->whereRaw('(fecha_aceptacion IS NOT NULL OR fecha_denegacion IS NOT NULL)')
            ->orderByRaw("COALESCE(fecha_aceptacion, fecha_denegacion, created_at) DESC")
            ->take(150)->get();

        return response()->json($solicitudes, 200);
    }

    /**
     * Crea una solicitud de incorporación al equipo
     */
    public function solicitar($idEquipo)
    {
        $user = auth()->user();

        // debe ser un usuario registrado e iniciada su sesión
        if (!$user)
            return response()->json(['error' => 'Debe iniciar sesión'], 401);

        // comprueba si no tenía ya una solicitud previa
        if (Solicitud::where('user_id', $user->id)
            ->where('equipo_id', $idEquipo)
            ->whereNull('fecha_aceptacion')
            ->whereNull('fecha_denegacion')
            ->exists()
        )
            return response()->json(['error' => 'Ya tiene una solicitud previa'], 400);

        // crea la solicitud
        $solicitud = Solicitud::create([
            'user_id' => $user->id,
            'equipo_id' => $idEquipo
        ]);

        return response()->json(['message' => 'Solicitud enviada', 'solicitud' => $solicitud], 200);
    }


    /**
     * método auxiliar
     */
    private function validarSolicitud($idSolicitud)
    {
        // carga la solicitud
        $solicitud = Solicitud::findOrFail($idSolicitud);

        // carga el equipo
        $equipo = $solicitud->equipo;

        // verificar si es coordinador del equipo
        if (Gate::denies('esCoordinador', $equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // comprueba si estaba pendiente
        if ($solicitud->fecha_aceptacion)
            return response()->json(['message' => 'Ya fue aceptada previamente'], 200);

        // comprueba si estaba denegada
        if ($solicitud->fecha_denegacion)
            return response()->json(['message' => 'Ya fue denegada previamente'], 400);

        return $solicitud;
    }

    /**
     * Acepta una solicitud de incorporación al equipo
     */
    public function aceptarSolicitud($idSolicitud)
    {
        $solicitud = $this->validarSolicitud($idSolicitud);

        // la marca como aceptada
        $solicitud->update([
            'fecha_aceptacion' => now(),
            'por_user_id' => auth()->user()->id
        ]);

        $solicitante = $solicitud->usuario;

        // agregamos el usuario al equipo
        $solicitud->equipo->miembros()->syncWithoutDetaching([$solicitante->id]);

        // Enviar el correo informativo
        // Mail::to($solicitante->email)->send(new IncorporacionEquipoEmail($solicitud->equipo, $solicitante, true, true));

        return response()->json(['message' => 'Solicitud aceptada'], 200);
    }


    /**
     * Deniega una solicitud de incorporación al equipo
     */
    public function denegarSolicitud($idSolicitud)
    {
        $solicitud = $this->validarSolicitud($idSolicitud);

        // la marca como denegada
        $solicitud->update([
            'fecha_denegacion' => now(),
            'por_user_id' => auth()->user()->id
        ]);

        $solicitante = $solicitud->usuario();

        // Enviar el correo informativo
        // Mail::to($solicitante->email)->send(new IncorporacionEquipoEmail($solicitud->equipo, $solicitante, false, true));

        return response()->json(['message' => 'Solicitud denegada'], 200);
    }
}
