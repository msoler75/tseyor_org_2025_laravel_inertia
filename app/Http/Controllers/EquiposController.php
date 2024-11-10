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
use App\Notifications\InvitacionDeclinada;
use Illuminate\Support\Facades\Notification;
use App\Pigmalion\StorageItem;
use Illuminate\Support\Facades\Cache;

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

        // obtenemos el listado de equipos y el nº de miembros
        $query = Equipo::withCount('miembros');

        // si el usuario tiene permisos de gestionar equipos
        $ocultarEquipos = $categoria != 'Mis equipos' && Gate::denies('administrar equipos');

        if ($categoria) {
            if ($categoria == 'Mis equipos')
                //obtener los equipos de los que soy miembro
                $query->whereIn('id', $user->equipos()->pluck('equipo_id'));
            else
                $query->where('categoria', '=', $categoria);
        } elseif ($buscar) {
            $query->whereRaw('CONCAT(nombre, " ", descripcion) like \'%' . $buscar . '%\'');
        }

        if ($ocultarEquipos)
            $query->where(function ($q) use ($user) {
                $q->whereNull('oculto')
                    ->orWhere('oculto', 0)
                    // no ocultar si eres miembro
                    ->orWhereHas('miembros', function ($subQuery) use ($user) {
                        $subQuery->where('users.id', optional($user)->id);
                    });
            });

        //mostramos la consulta SQL final:
        // dd($query->toSql());

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

        if ($user) {
            // cuenta el nº de equipos de los cuales eres miembro
            $mis_equipos = $user->equipos()->count();
            if ($mis_equipos)
                array_unshift($categorias, ['nombre' => 'Mis equipos', 'total' => $mis_equipos]);
        }

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

        $user = auth()->user();

        $solicitud = null;
        $solicitudes = [];

        $puedoAdministrar = Gate::allows('administrar equipos');

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

        // carga las solicitudes que hay pendientes

        if ($puedoAdministrar || $soyCoordinador) {
            // carga la lista de solicitudes pendientes
            $solicitudes = Solicitud::with('usuario')
                ->where('equipo_id', $equipo->id)
                ->whereNull('fecha_aceptacion')
                ->whereNull('fecha_denegacion')
                ->get();
        }

        // si el usuario tiene permisos de gestionar equipos
        $permisoVerEquipo = !$equipo->oculto || $soyMiembro || $puedoAdministrar;

        if (!$permisoVerEquipo)
            abort(404, 'No tienes permisos para ver este equipo');

        $equipo->solicitudesPendientes = $solicitudes;

        // informes

        $informes = Informe::where('equipo_id', $equipo->id)->where('visibilidad', 'P')->latest('updated_at')->take(3)->get()->toArray();


        return Inertia::render('Equipos/Equipo', [
            'equipo' => $equipo,
            'carpetas' => $carpetas,
            'ultimosArchivos' => // Inertia::lazy(function () use ($equipo) {
            //return
            $this->ultimosArchivos($equipo)
            // ;
            //})
            ,
            'ultimosInformes' => $informes,
            'miSolicitud' => $solicitud,
            'soyMiembro' => $soyMiembro,
            'soyCoordinador' => $soyCoordinador,
            'puedoAdministrar' => $puedoAdministrar,
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
     * Últimos archivos del equipo
     */
    private function ultimosArchivos($equipo)
    {
        // el numero de archivos a listar
        $NUM_ARCHIVOS_ULTIMOS = 10;
        $DIAS_CACHE = 1;

        $cacheKey = "equipo_ultimos_archivos_" . $equipo->id;
        $archivos = Cache::remember($cacheKey, now()->addDays($DIAS_CACHE), function () use ($equipo) {
            $carpetas = $equipo->carpetas()->get();
            $ultimosArchivosEquipo = [];

            foreach ($carpetas as $carpeta) {
                $nodo = Nodo::desde($carpeta->ubicacion);
                if ($nodo && Gate::allows('ejecutar', $nodo)) {
                    $loc = new StorageItem($carpeta->ubicacion);
                    $archivos = $loc->lastFiles();
                    $ultimosArchivosEquipo = array_merge($ultimosArchivosEquipo, $archivos);
                }
            }

            // ordenamos con recientes primero
            usort($ultimosArchivosEquipo, function ($a, $b) {
                return $b['fecha_modificacion'] - $a['fecha_modificacion'];
            });

            return $ultimosArchivosEquipo;
        });

        $archivos_final = [];
        // dd($archivos);
        foreach ($archivos as $archivo) {
            // miramos si tenemos permisos con este usuario para acceder al archivo
            $nodo = Nodo::desde($archivo['url']);
            if (Gate::allows('leer', $nodo)) {
                $archivos_final[] = $archivo;
                if (count($archivos_final) >= $NUM_ARCHIVOS_ULTIMOS)
                    break;
            }
        }
        return $archivos_final;
    }

    /**
     * Página de la UTG
     */
    public function index_utg()
    {
        $categoria = 'utg';
        // toma los 6 primeros equipos, con más miembros
        $departamentos = Equipo::where('categoria', '=', $categoria)
            ->withCount('miembros')
            ->orderBy('miembros_count', 'desc')
            ->take(6)
            ->get();

        return Inertia::render(
            'Utg/Index',
            [
                'estatutosUrl' => '/normativas/estatutos-universidad-tseyor-de-granada-utg-agosto-2023',
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
            'nombre' => 'required|max:64',
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

        $puedoAdministrar = Gate::allows('administrar equipos');

        // Verificar si el usuario es un coordinador del equipo
        if (!$puedoAdministrar && Gate::denies('esCoordinador', $equipo)) {
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

        $puedoAdministrar = Gate::allows('administrar equipos');

        if (!$puedoAdministrar && Gate::denies('esCoordinador', $equipo)) {
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

        $puedoAdministrar = Gate::allows('administrar equipos');

        if (!$puedoAdministrar && Gate::denies('esCoordinador', $equipo)) {
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

        $puedoAdministrar = Gate::allows('administrar equipos');

        // solo podemos actualizar los miembros si somos coordinadores
        if (!$puedoAdministrar && Gate::denies('esCoordinador', $equipo)) {
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
     * Historial de invitaciones pendientes o con error
     */
    public function invitations($idEquipo)
    {
        // Marcar invitaciones caducadas (1 mes)
        $diasCaducidad = config('app.invitaciones.dias_caducidad', 30);
        $diasAntiguedad = config('app.invitaciones.mostrar_dias_antiguedad', 90);

        Invitacion::where('equipo_id', $idEquipo)
            ->whereNull('accepted_at')
            ->whereNull('declined_at')
            ->where(function ($query) use ($diasCaducidad) {
                $query->whereRaw('COALESCE(sent_at, created_at) <= ?', [Carbon::now()->subDays($diasCaducidad)]);
            })
            ->whereNot('estado', 'cancelada')
            ->update(['declined_at' => Carbon::now(), 'estado' => 'caducada']);

        Invitacion::where('equipo_id', $idEquipo)
            ->whereIn('estado', ['pendiente', 'registro'])
            ->where(function ($query) use ($diasCaducidad) {
                $query->whereRaw('COALESCE(sent_at, created_at) <= ?', [Carbon::now()->subDays($diasCaducidad)]);
            })
            ->update(['declined_at' => Carbon::now(), 'estado' => 'caducada']);

        // seleccionamos invitaciones a este equipo que están pendientes
        // 3 meses atrás máximo
        $invitaciones = Invitacion::where('equipo_id', $idEquipo)
            ->where(function ($query) use ($diasAntiguedad) {
                $query->whereRaw('COALESCE(sent_at, created_at) >= ?', [Carbon::now()->subDays($diasAntiguedad)]);
            })
            ->whereNotIn('estado', ['aceptada'])
            ->with('user')
            ->orderByRaw('COALESCE(sent_at, created_at) DESC')
            // ->take(200)
            ->get();

        //
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

        $puedoAdministrar = Gate::allows('administrar equipos');

        // Verificar si el usuario es un coordinador del equipo
        if (!$puedoAdministrar && Gate::denies('esCoordinador', $equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $yaSonMiembros = [];
        $invitacionReciente = [];
        $invitados = [];
        $noEncontrados = [];

        $diasAntiguedad = config('app.invitaciones.mostrar_dias_antiguedad', 90);

        // mandamos invitaciones a los correos proporcionados
        foreach ($validatedData['correos'] as $correo) {
            // Verificar si el correo ya está asociado al equipo
            if ($equipo->miembros->where('email', $correo)->first()) {
                $yaSonMiembros[] = $correo;
                continue;
            }

            // Verificar si ya se envió una invitación a ese correo
            // dentro de los días de antiguedad
            if (
                Invitacion::where('equipo_id', $idEquipo)->where('email', $correo)
                ->whereRaw('COALESCE(sent_at, created_at) >= ?', [Carbon::now()->subDays($diasAntiguedad)])
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
            $invitacion = Invitacion::create([
                'equipo_id' => $idEquipo,
                'email' => $correo,
                'token' => $token,
                'user_id' => $user_id
            ]);

            $invitados[] = $correo;

            // Enviar el correo de invitación
            try {
                Mail::to($correo)->send(new InvitacionEquipoEmail($invitacion));
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
            // dentro de los días de antiguedad
            if (Invitacion::where('equipo_id', $idEquipo)->where('email', $correo)
                ->whereRaw('COALESCE(sent_at, created_at) >= ?', [Carbon::now()->subDays($diasAntiguedad)])
                ->first()
            ) {
                $invitacionReciente[] = $id;
                continue;
            }


            // Verificar si ya se envió una invitación a ese usuario
            if (Invitacion::where('equipo_id', $idEquipo)
                ->whereRaw('COALESCE(sent_at, created_at) >= ?', [Carbon::now()->subDays($diasAntiguedad)])
                ->where('user_id', $id)->first()
            ) {
                $invitacionReciente[] = $id;
                continue;
            }

            // Generar el token para la invitación
            $token = sha1(time() . $id);

            // Crear la invitación en la base de datos
            $invitacion = Invitacion::create([
                'equipo_id' => $idEquipo,
                'email' => $correo,
                'token' => $token,
                'user_id' => $usuario->id
            ]);

            $invitados[] = $usuario->id;

            // Enviar el correo de invitación
            try {
                Mail::to($correo)->send(new InvitacionEquipoEmail($invitacion));
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error del servidor. No se ha podido enviar la invitación: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'Invitaciones realizadas.', ...compact('invitados', 'yaSonMiembros', 'noEncontrados', 'invitacionReciente')], 200);
    }

    public function resendInvitation($id)
    {
        // Crear la invitación en la base de datos
        $invitacion = Invitacion::findOrFail($id);

        $puedoAdministrar = Gate::allows('administrar equipos');

        // Verificar si el usuario es un coordinador del equipo
        if (!$puedoAdministrar && Gate::denies('esCoordinador', $invitacion->equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $invitacion->update(['estado' => 'pendiente', 'accepted_at' => null, 'declined_at' => null]);

        // $invitacion->update(['pendiente']);

        // Enviar el correo de invitación
        try {
            Mail::to($invitacion->email)->send(new InvitacionEquipoEmail($invitacion));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error del servidor. No se ha podido reenviar la invitación: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Invitación reenviada.'], 200);
    }


    public function cancelInvitation($id)
    {
        // Crear la invitación en la base de datos
        $invitacion = Invitacion::findOrFail($id);

        $puedoAdministrar = Gate::allows('administrar equipos');

        // Verificar si el usuario es un coordinador del equipo
        if (!$puedoAdministrar && Gate::denies('esCoordinador', $invitacion->equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $invitacion->update(['estado' => 'cancelada']);

        return response()->json(['message' => 'Invitación cancelada.'], 200);
    }

    /**
     * BORRA una invitación
     */
    public function deleteInvitation($id)
    {
        // Crear la invitación en la base de datos
        $invitacion = Invitacion::findOrFail($id);

        $puedoAdministrar = Gate::allows('administrar equipos');

        // Verificar si el usuario es un coordinador del equipo
        if (!$puedoAdministrar && Gate::denies('esCoordinador', $invitacion->equipo)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $invitacion->delete();

        return response()->json(['message' => 'Invitación eliminada.'], 200);
    }


    /**
     * Enlace accedido por el usuario
     */
    public function acceptInvitation($token)
    {
        $invitacion = Invitacion::where('token', $token)->first();

        // invitación no válida
        if (!$invitacion)
            return redirect("/equipos")->with('message', 'La invitación ha caducado o ya ha sido procesada.');

        $urlEquipo = route('equipo', $invitacion->equipo->slug);

        // a donde va el usuario
        $urlDestino = $invitacion->equipo->oculto ? "/equipos" : $urlEquipo;

        // verificamos si el destinatario tiene su cuenta creada
        $usuarioInvitado = User::where('email', $invitacion->email)->first();

        // Si el usuario no tiene una cuenta, redirigirlo a la página de registro
        $registroUrl = URL::signedRoute('register', ['email' => $invitacion->email]);

        // miramos el tiempo trascurrido desde la invitacion->sent_at
        // la invitación caduca a los 30 días
        $daysElapsed = Carbon::now()->diffInDays($invitacion->sent_at);

        $diasValidez = config('app.invitaciones.dias_caducidad', 30);

        // Comprobamos si ha iniciado sesión con otra cuenta
        $user = auth()->user();
        if ($user && $user->email != $invitacion->email) {
            return redirect($urlDestino)->with('message', 'Para aceptar la invitación, antes debe cerrar la sesión actual en tseyor.org');
        }

        // el usuario ya había aceptado y está pulsando otra vez el enlace, pero tal vez aun no ha creado su cuenta
        if ($daysElapsed <= $diasValidez && $invitacion->accepted_at && !$invitacion->user_id && !$usuarioInvitado) {
            // el usuario invitado aun no ha creado su cuenta
            return redirect($registroUrl)->with('message', 'Regístrese para aceptar la invitación.');
        }

        // Verificar si la invitación ya fue aceptada o declinada previamente, o si ha caducado
        if ($invitacion->accepted_at || $invitacion->declined_at || $invitacion->estado == 'caducada' || $invitacion->estado == 'cancelada' || $daysElapsed > $diasValidez) {
            // en cualquier otro caso (ya fue gestionada la invitación, o ha caducado), el enlace está caducado
            return redirect($urlDestino)->with('message', 'La invitación ha caducado o ya ha sido procesada.');
        }

        // El usuario ya aceptó la invitación, por tanto ya marcamos la invitación como aceptada
        $invitacion->update(['estado' => $usuarioInvitado ? 'aceptada' : 'registro', 'accepted_at' => now()]);

        // Esto implica que cuando el usuario se registre, habrá que verificar si tiene invitaciones a equipos que haya aceptado

        // marcamos invitaciones previas al mismo destinatario como declinadas
        $invitacionesPendientes = Invitacion
            ::where('equipo_id', $invitacion->equipo_id)
            ->where('email', $invitacion->email)
            ->whereNull('accepted_at')
            ->whereNull('declined_at')
            // ->whereNotIn('id', [$invitacion->id])
            // ->orderBy('sent_at', 'desc')
            ->get();
        foreach ($invitacionesPendientes as $invitacionOld) {
            $invitacionOld->update(['estado' => 'declinada', 'declined_at' => Carbon::now()]);
        }


        if ($usuarioInvitado) {
            // Asociar al usuario al equipo y marcar la invitación como aceptada
            $usuarioInvitado->equipos()->attach($invitacion->equipo_id);

            $mensaje = "Invitación aceptada. ¡Ya eres parte del equipo '{$invitacion->equipo->nombre}'!";

            if (!$user) $mensaje .= " Recuerda iniciar sesión con tu cuenta.";

            $user = auth()->user();

            if ($invitacion->equipo->oculto && !$user)
                $mensaje = " Para ver tu equipo debes iniciar sesión con tu cuenta.";

            return redirect($urlDestino)->with('message', $mensaje);
        }



        return redirect($registroUrl)->with('message', 'Regístrese para aceptar la invitación.');
    }


    /**
     *  Enlace accedido por el usuario
     */
    public function declineInvitation($token)
    {
        $invitacion = Invitacion::where('token', $token)->first();

        // invitación no válida
        if (!$invitacion)
            return redirect("/equipos")->with('message', 'La invitación ha caducado o ya ha sido procesada.');

        $urlEquipo = route('equipo', $invitacion->equipo->slug);

        // a donde va el usuario
        $urlDestino = $invitacion->equipo->oculto ? "/equipos" : $urlEquipo;

        // Verificar si la invitación ya fue aceptada o declinada previamente
        if ($invitacion->accepted_at || $invitacion->declined_at) {
            return redirect($urlDestino)->with('message', 'La invitación ha caducado o ya ha sido procesada.');
        }

        // Marcar la invitación como declinada
        $invitacion->update(attributes: ['estado' => 'declinada', 'declined_at' => now()]);

        if ($invitacion->user_id) {
            // actualizamos posibles otras invitaciones al mismo usuario como declinadas
            $invitacionesPendientes = Invitacion::where('equipo_id', $invitacion->equipo_id)
                ->where('user_id', $invitacion->user_id)
                ->whereNull('accepted_at')
                ->whereNull('declined_at')
                ->orderBy('sent_at', 'desc')
                ->get();
            foreach ($invitacionesPendientes as $invitacion) {
                $invitacion->update(['estado' => 'declinada', 'declined_at' => Carbon::now()]);
            }
        }

        // Notificamos a los coordinadores
        Notification::send($invitacion->equipo->coordinadores, new InvitacionDeclinada($invitacion));

        return redirect($urlDestino)->with('message', 'Invitación declinada.');
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

        $puedoAdministrar = Gate::allows('administrar equipos');

        if (!$puedoAdministrar && Gate::denies('esCoordinador', $equipo)) {
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

        $puedoAdministrar = Gate::allows('administrar equipos');

        // verificar si es coordinador del equipo
        if (!$puedoAdministrar && Gate::denies('esCoordinador', $equipo)) {
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
