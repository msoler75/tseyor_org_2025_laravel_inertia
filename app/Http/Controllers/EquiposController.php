<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Equipo;
use App\Models\NodoCarpeta;
use App\Models\Invitacion;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Nodo;
use Illuminate\Support\Facades\Log;
use App\Models\Acl;
use App\Models\Informe;
use App\Pigmalion\SEO;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\InvitacionEquipoEmail;
use App\Mail\IncorporacionEquipoEmail;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use App\Notifications\SolicitudEquipo;
use App\Notifications\AbandonoEquipo;
use App\Notifications\DenegadoEquipo;
use Illuminate\Support\Facades\Notification;
use App\Pigmalion\StorageItem;

class EquiposController extends Controller
{
    /**
     * Lista de equipos
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        // obtenemos el listado de equipos y sus
        $query = Equipo::withCount('miembros')

        if ($categoria) {
            $query->where('categoria', '=', $categoria);
        } elseif ($buscar) {
            $query->where('nombre', 'like', '%' . $buscar . '%')
                ->orWhere('descripcion', 'like', '%' . $buscar . '%');
        }

        $resultados = $query->latest()->paginate(12);

        $resultados->getCollection()->transform(function ($equipo) use ($user) {
            $equipo->soy_miembro = $equipo->miembros->contains('id', optional($user)->id);
            $equipo->soy_coordinador = $equipo->coordinadores->contains('id', optional($user)->id);
            unset($equipo->miembros, $equipo->coordinadores);
            return $equipo;
        });

        if ($categoria) {
            $resultados->appends(['categoria' => $categoria]);
        } elseif ($buscar) {
            $resultados->appends(['buscar' => $buscar]);
        }

        $categorias = (new Equipo())->getCategorias();

        return Inertia::render('Equipos/Index', [
            'filtrado' => $buscar,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias
        ])->withViewData(SEO::get('equipos'));
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
        foreach ($carpetas as $carpeta) {
            $nodo = Nodo::desde($carpeta->ubicacion);
            if ($nodo && Gate::allows('ejecutar', $nodo)) {
                $loc = new StorageItem($carpeta->ubicacion);
                $archivos = $loc->lastFiles();
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
                if (Gate::allows('EsCoordinador', $equipo)) {
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
                    // ->whereNull('fecha_aceptacion')
                    // ->whereNull('fecha_denegacion')
                    ->orderBy('created_at', 'desc') // recientes primero
                    ->first();
            }
        }

        $equipo->solicitudesPendientes = $solicitudes;

        // informes

        $informes = Informe::where('equipo_id', $equipo->id)->where('visibilidad', 'P')->latest('updated_at')->take(3)->get()->toArray();


        return Inertia::render('Equipos/Equipo', [
            'equipo' => $equipo,
            'carpetas' => $carpetas,
            'ultimosArchivos' => array_slice($ultimosArchivos, 0, 10),
            'ultimosInformes' => $informes,
            'miSolicitud' => $solicitud,
            'soyMiembro' => $soyMiembro,
            'soyCoordinador' => $soyCoordinador,
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
        Log::info("Equipo.update");

        $equipo = Equipo::findOrFail($idEquipo);

        // Verificar si el usuario es un coordinador del equipo
        if (Gate::denies('esCoordinador', $equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Validar los datos
        $validatedData = $request->validate([
            'nombre' => 'required|max:32',
            'descripcion' => 'required|max:400',
            'imagen' => 'image|mimes:jpeg,png,jpg,gif|max:64000', // Ajustar las reglas de validación según tus necesidades
            'anuncio' => 'max:400',
            'reuniones' => 'max:255',
            'informacion' => 'max:65000'
        ]);

        Log::info(var_export($validatedData, true));

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
            $path = $newImage->store('medios/equipos', ['disk' => 'public']);
            $equipo->imagen = str_replace(url(''), "", Storage::disk('public')->url($path));
            Log::info("path Imagen: " . $path . " -> Equipo.imagen=" . $equipo->imagen);
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

        // lo quitamos del equipo
        $this->bajaUsuario($usuario, $equipo);

        // por si hubiera asignación de nuevo coordinador
        $idNuevoCoordinador = $equipo->asignarCoordinador($idUsuario);

        $message = 'El usuario fue removido del equipo';
        if ($idNuevoCoordinador)
            return response()->json([
                'message' => $message,
                'nuevoCoordinador' => $idNuevoCoordinador
            ]);

        return response()->json(['message' => $message], 200);
    }


    /**
     * Modificamos el estado o rol de un usuario en un equipo
     */
    public function updateMember($idEquipo, $idUsuario, $rol)
    {
        // Obtenemos el usuario y el equipo que vamos a gestionar
        $usuario = User::findOrFail($idUsuario);
        $equipo = Equipo::findOrFail($idEquipo);

        // solo podemos actualizar los miembros si somos coordinadores
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

        $idNuevoCoordinador = $equipo->asignarCoordinador($idUsuario);

        $message = 'El usuario fue actualizado dentro del equipo';
        if ($idNuevoCoordinador)
            return response()->json([
                'message' => $message,
                'nuevoCoordinador' => $idNuevoCoordinador
            ]);

        return response()->json(['message' => $message], 200);
    }




    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // INVITACIONES


    /**
     * Invitaciones pendientes
     */
    public function invitations($idEquipo)
    {
        // Marcar invitaciones caducadas (1 mes)
        Invitacion::where('equipo_id', $idEquipo)
            ->where('accepted_at', null)
            ->where('declined_at', null)
            ->where('created_at', '<=', Carbon::now()->subMonth())
            ->update(['declined_at' => Carbon::now()]);

        // seleccionamos invitaciones a este equipo que están pendientes
        $invitaciones = Invitacion::where('equipo_id', $idEquipo)
            ->where('accepted_at', null)
            ->where('declined_at', null)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(150)->get(); // máximo 150
        return response()->json(compact('invitaciones'), 200);
    }

    /**
     * Envia invitaciones a usuarios
     * retorna JSON
     */
    public function invite(Request $request, $idEquipo)
    {
        // Validar los datos
        $validatedData = $request->validate([
            'correos' => 'array',
            'correos.*' => 'email',
            'usuarios' => 'array'
        ]);

        $equipo = Equipo::findOrFail($idEquipo);

        // Verificar si el usuario es un coordinador del equipo
        if (Gate::denies('esCoordinador', $equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $yaSonMiembros = [];
        $invitacionReciente = [];
        $invitados = [];
        $noEncontrados = [];

        // mandamos invitaciones a los correos proporcionados
        foreach ($validatedData['correos'] as $correo) {
            // Verificar si el correo ya está asociado al equipo
            if ($equipo->miembros->where('email', $correo)->first()) {
                $yaSonMiembros[] = $correo;
                continue;
            }

            // Verificar si ya se envió una invitación a ese correo recientemente (ultimos dos días)
            if (
                Invitacion::where('equipo_id', $idEquipo)->where('email', $correo)
                ->whereDate('created_at', '>=', Carbon::now()->subDays(2))
                ->first()
            ) {
                $invitacionReciente[] = $correo;
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

            $invitados[] = $correo;

            // Enviar el correo de invitación
            try {
                Mail::to($correo)->send(new InvitacionEquipoEmail($equipo, $usuario, $aceptarUrl, $declinarUrl));
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error del servidor. No se ha podido enviar la invitación: ' . $e->getMessage()], 500);
            }
        }

        // mandamos invitaciones a los usuarios registrados
        foreach ($validatedData['usuarios'] as $id) {

            // Cargar el usuario
            $usuario = User::find($id);


            if (!$usuario) {
                $noEncontrados[] = $id;
                continue; // el usuario debería existir
            }

            $correo = $usuario->email;
            if (!$correo) {
                $noEncontrados[] = $id;
                continue;
            }

            // Verificar si el usuario ya es miembro del equipo
            if ($equipo->miembros->where('id', $usuario->id)->first()) {
                $yaSonMiembros[] = $id;
                continue;
            }

            // Verificar si ya se envió una invitación a ese correo
            if (Invitacion::where('equipo_id', $idEquipo)->where('email', $correo)
                ->whereDate('created_at', '>=', Carbon::now()->subDays(2))
                ->first()
            ) {
                $invitacionReciente[] = $id;
                continue;
            }



            // Verificar si ya se envió una invitación a ese usuario
            if (Invitacion::where('equipo_id', $idEquipo)
                ->whereDate('created_at', '>=', Carbon::now()->subDays(2))
                ->where('user_id', $id)->first()
            ) {
                $invitacionReciente[] = $id;
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


            $invitados[] = $usuario->id;

            // Enviar el correo de invitación
            try {
                Mail::to($correo)->send(new InvitacionEquipoEmail($equipo, $usuario, $aceptarUrl, $declinarUrl));
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error del servidor. No se ha podido enviar la invitación: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'Invitaciones enviadas correctamente.', ...compact('invitados', 'yaSonMiembros', 'noEncontrados', 'invitacionReciente')], 200);
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
            return redirect($urlEquipo)->with('message', 'La invitación ha caducado o ya ha sido procesada.');
        }

        // Verificar si el usuario ya tiene una cuenta
        $usuario = User::where('email', $invitacion->email)->first();
        if ($usuario) {
            // Asociar al usuario al equipo y marcar la invitación como aceptada
            $usuario->equipos()->attach($invitacion->equipo_id);

            // actualizamos posibles otras invitaciones al mismo usuario como declinadas
            $invitacionesPendientes = Invitacion::where('equipo_id', $invitacion->equipo_id)
                ->where('user_id', $usuario->id)
                ->where('accepted_at', null)
                ->where('declined_at', null)
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($invitacionesPendientes as $invitacion) {
                $invitacion->update(['declined_at' => Carbon::now()]);
            }

            // marcamos la invitación actual como aceptada
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
            return redirect($urlEquipo)->with('message', 'La invitación ha caducado o ya ha sido procesada.');
        }

        if ($invitacion->user_id) {
            // actualizamos posibles otras invitaciones al mismo usuario como declinadas
            $invitacionesPendientes = Invitacion::where('equipo_id', $invitacion->equipo_id)
                ->where('user_id', $invitacion->user_id)
                ->where('accepted_at', null)
                ->where('declined_at', null)
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($invitacionesPendientes as $invitacion) {
                $invitacion->update(['declined_at' => Carbon::now()]);
            }
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

        $equipo = Equipo::findOrFail($idEquipo);

        // comprueba si no tenía ya una solicitud previa
        if (
            Solicitud::where('user_id', $user->id)
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


        // notificamos a los coordinadores del equipo
        Notification::send($equipo->coordinadores, new SolicitudEquipo($equipo));

        return response()->json(['message' => 'Solicitud enviada', 'solicitud' => $solicitud], 200);
    }



    /**
     * método auxiliar
     */
    private function validarSolicitud($idSolicitud): Solicitud
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

        // el correo se envía en MembresiaObserver
        // ..

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

        $solicitante = $solicitud->usuario;

        // Enviar el correo informativo
        $solicitante->notify(new DenegadoEquipo($solicitud->equipo, $solicitante));

        return response()->json(['message' => 'Solicitud denegada'], 200);
    }



    /**
     * El usuario abandona un equipo
     */
    public function abandonar($idEquipo)
    {
        $user = auth()->user();

        // debe ser un usuario registrado e iniciada su sesión
        if (!$user)
            return response()->json(['error' => 'Debe iniciar sesión'], 401);

        $equipo = Equipo::findOrFail($idEquipo);

        $this->bajaUsuario($user, $equipo);

        $equipo->asignarCoordinador();

        // notificamos a los coordinadores del equipo
        Notification::send($equipo->coordinadores, new AbandonoEquipo($equipo, $user));

        return response()->json(['message' => 'Has abandonado el equipo'], 200);
    }


    /**
     * Da de baja al usuario del equipo y remueve sus permisos
     */
    private function bajaUsuario(User $user, Equipo $equipo)
    {
        // Verificar si el usuario ya es miembro del equipo
        if (!$equipo->esMiembro($user->id)) {
            return response()->json(['error' => 'No eres del equipo'], 400);
        }

        // removemos el usuario del equipo
        $equipo->miembros()->detach($user->id);

        // por si tuviera permisos, se los removemos
        $equipo->removerPermisosCarpetas($user->id);

        // eliminamos todas las solicitudes del usuario
        Solicitud::where('user_id', $user->id)
            ->where('equipo_id', $equipo->id)
            ->delete();
    }
}
