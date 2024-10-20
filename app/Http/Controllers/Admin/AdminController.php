<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Informe;
use App\Models\User;
use App\Models\Comentario;
use App\Models\Nodo;
use App\Models\Contenido;
use App\Models\Revision;
use App\Models\Busqueda;
use App\Models\Inscripcion;
use App\Models\JobFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Pigmalion\StorageItem;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Notifications\CambioPassword;

class AdminController // extends Controller
{

    public function dashboard()
    {

        // $ultimos_informes = Informe::whereIn('equipo_id', backpack_user()->equiposQueCoordina->pluck('id'))->with('equipo')->latest()->take(10)->get();

        $users_creados = User::select()->latest()->take(10)->get();

        $users_activos = DB::table('sessions')
            ->select('users.name as name', 'users.slug as slug', 'user_id', 'last_activity')
            ->join('users', 'users.id', '=', 'sessions.user_id')
            ->orderBy('last_activity', 'desc')
            ->take(10)
            ->get()->toArray();

        $comentarios = Comentario::select()->with('user')->latest()->take(10)->get();

        $contenidos_creados = Contenido::select()->orderBy('created_at', 'desc')->take(10)->get();

        $contenidos_modificados = Contenido::select()->orderBy('updated_at', 'desc')->take(10)->get();

        $revisiones = Revision::select()->latest()->take(10)->get();

        $busquedas = Busqueda::select(['query', 'created_at'])->latest()->take(10)->get();

        $archivos = Nodo::with('user')->latest()->take(12)->get();
        //dd($archivos->toArray()[0]);


        $inscripciones_nuevas = Inscripcion::where('estado', 'nuevo')->count();

        $tareas_fallidas = JobFailed::count();

        // está la app en modo mantenimiento? (down/up)
        $en_mantenimiento = app()->isDownForMaintenance();

        $data = [
            //'ultimos_informes' => $ultimos_informes,
            'users_creados' => $users_creados,
            'users_activos' => $users_activos,
            'comentarios' => $comentarios,
            'contenidos_creados' => $contenidos_creados,
            'contenidos_modificados' => $contenidos_modificados,
            'inscripciones_nuevas' => $inscripciones_nuevas,
            'tareas_fallidas' => $tareas_fallidas,
            'en_mantenimiento' => $en_mantenimiento,
            'revisiones' => $revisiones,
            'busquedas' => $busquedas,
            'archivos' => $archivos,
        ];
        // dd($revisiones);

        // dd($comentarios);
        return view('admin.dashboard', $data);
    }


    /**
     * Devuelve el contenido de un archivo de log
     */
    public function getLog($log)
    { // retorna json
        $logsFolder = storage_path('logs');

        // evitar hacker
        if (strpos($log, "..") !== false || strpos($log, "/") !== false || strpos($log, "\\") !== false)
            return response()->json(['content' => '']);

        $file = "$logsFolder/$log";

        // si no existe
        if (!file_exists($file))
            return response()->json(['content' => '']);

        // si el archivo es más grande de 5 mb, solo leemos los ultimos 5 mb del archivo
        $size = filesize($file);
        if ($size  > 5 * 1024 * 1024)
            $content = file_get_contents($file, false, null, $size - 5 * 1024 * 1024);
        else
            $content = file_get_contents($file);
        return response()->json(['content' => $content]);
    }


    /**
     * Lista las imagenes de una carpeta
     * @param mixed $ruta
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function listImages($ruta)
    {
        $loc = new StorageItem($ruta);

        if (!$loc->directoryExists())
            return response()->json([], 400);

        $imagenes = $loc->listImages();
        // return json response
        return response()->json($imagenes);
    }


    /**
     * Se inicia sesión con el usuario deseado.
     * Para desarrollo y administración
     * @param mixed $idUser
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function loginAs($idUser)
    {
        $user = Auth::user();
        if (!$user || $user->id !== 1)
            return response()->json(['message' => 'Acceso no permitido'], 403);

        // cambiamos a nuevo usuario
        $user = User::find($idUser);
        if (!$user)
            return response()->json(['message' => 'usuario no encontrado'], 404);

        Auth::login($user, true); // Autenticar al usuario

        session()->regenerate(); // Regenerar la sesión

        return response()->json(['message' => 'usuario cambiado'], 200);
    }


    /**
     * Genera una nueva contraseña para el usuario
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function newPassword(Request $request)
    {

        $user_id = $request->user_id;
        \Log::info("newPassword", $request->all());
        \Log::info("user_id: " . $user_id);
        if (!$user_id) abort(400);
        $user = User::findOrFail($user_id);

        $words = array(
            "amor",
            "mente",
            "observar",
            "trascendente",
            "unidad",
            "cambio",
            "divulgar",
            "armonizar",
            "equilibrio",
            "muul",
            "baksaj",
            "diversidad",
            "celeste",
            "kundalini",
            "grupal",
            "cielo",
            "ritmo",
            "equidad",
            "infinito",
            "trinidad",
            "estrella",
            "plasma",
            "salud",
            "ong",
            "mundo",
            "utg",
            "universidad",
            "sandalia",
            "baston",
            "protege",
            "manto",
            "movimiento",
            "claridad",
            "humildad",
            "hermandad",
            "confianza",
            "camino",
            "predica",
            "corazon",
            "estelar",
            "cayado",
            "baculo",
            "ancestral",
            "libertad",
            "libre",
            "uno",
            "dos",
            "tres",
            "cuatro",
            "cinco",
            "seis",
            "siete",
            "ocho",
            "nueve",
            "diez",
            "once",
            "doce",
            "trece",
            "intruso",
            "dispersion",
            "cyborg",
            "crea",
            "crear",
            "voluntario",
            "forzado",
            "auto",
            "autoctono",
            "oriundo",
            "primigenio",
            "aguila",
            "holograma",
            "ilusion",
            "fantasia",
            "apego",
            "desapego",
            "sombra",
            "sombras",
            "piensa",
            "pancreas",
            "pan",
            "vino",
            "sangre",
            "tierra",
            "linfatico",
            "reconocer",
            "cristo",
            "cosmico",
            "interior",
            "proteccion",
            "alcanzar",
            "tutelar",
            "replica",
            "replicas",
            "realidad",
            "mundos",
            "h1",
            "h2",
            "h3",
            "aium",
            "rasbek",
            "shilcars",
            "melcor",
            "orjain",
            "noiwanak",
            "jalied",
            "melinus",
            "mo",
            "rhaum",
            "seiph",
            "orsil",
            "aumnor",
            "leer",
            "asumir",
            "vaciar",
            "odres",
            "fractales",
            "mezclar",
            "lodo",
            "agua",
            "limpiar",
            "ejemplo",
            "peques",
            "sanar",
            "agregado",
            "transformar",
            "transformarse",
            "cambiar",
            "monje",
            "pensamiento",
            "espejo",
            "testo",
            "transmutar",
            "luz",
            "rompui",
            "om",
            "pedir",
            "neent",
            "aum",
            "retro",
            "retroalimenta",
            "sinhio",
            "paraguas",
            "protector",
            "cafe",
            "prometeo",
            "fractal",
            "xendra",
            "orbe",
            "esfera",
            "arte",
            "ciencia",
            "espiritual",
            "espiritualidad",
            "ondulatorio",
            "terapia",
            "retiro",
            "guerrero",
            "prior",
            "norte",
            "este",
            "oeste",
            "sur",
            "",
            "trascendente",
            "abiotica",
            "norte",
            "oscuridad",
            "entropia",
            "feliz",
            "romper",
            "beh",
            "sayab",
            "tseek",
            "suut",
            "kat",
            "oksah",
            "ich",
            "grihal"
        );

        $index = mt_rand(0, count($words) - 1);

        $password = $words[$index] . '.' . mt_rand(1000, 9999);
        \Log::info("nueva contraseña para {$user->name}: $password");

        $user->update(['password' => bcrypt($password)]);

        // notificamos al usuario
        $user->notify(new CambioPassword($password));

        return response()->json(['user' => $user->name, 'password' => $password]);
    }
}
