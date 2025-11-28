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
use App\Models\Job;
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

        $cambios_inscripciones = Revision::where('revisionable_type', 'App\\Models\\Inscripcion')
            ->whereIn('key', ['estado', 'user_id'])
            ->with('user')
            ->with(['revisionable' => function($query) {
                $query->with('usuarioAsignado');
            }])
            ->latest()
            ->take(10)
            ->get();

        // Enriquecer cada revisión con tutor antiguo/nuevo cuando aplique.
        // Para revisiones de `user_id` tomamos old_value/new_value; para revisiones de `estado` buscamos
        // la última revisión de `user_id` anterior o igual a la fecha de la revisión de estado.
        $tutorIds = [];
        foreach ($cambios_inscripciones as $c) {
            $c->tutor_old_id = null;
            $c->tutor_new_id = null;

            if ($c->key === 'user_id') {
                $old = $c->old_value;
                $new = $c->new_value;
                if ($old && is_numeric($old)) { $c->tutor_old_id = (int)$old; $tutorIds[] = (int)$old; }
                if ($new && is_numeric($new)) { $c->tutor_new_id = (int)$new; $tutorIds[] = (int)$new; }
            } else {
                // key === 'estado'
                $userRev = Revision::where('revisionable_type', 'App\\Models\\Inscripcion')
                    ->where('revisionable_id', $c->revisionable_id)
                    ->where('key', 'user_id')
                    ->where('created_at', '<=', $c->created_at)
                    ->orderBy('created_at', 'desc')
                    ->first();
                $tutorId = $userRev ? ($userRev->new_value ?? $userRev->old_value) : null;
                if ($tutorId && is_numeric($tutorId)) { $c->tutor_old_id = (int)$tutorId; $tutorIds[] = (int)$tutorId; }
            }
        }

        // Precargar usuarios para evitar N+1
        $usuarios = collect();
        if (!empty($tutorIds)) {
            $usuarios = User::whereIn('id', array_values(array_unique($tutorIds)))->get()->keyBy('id');
        }

        foreach ($cambios_inscripciones as $c) {
            $c->tutor_old_user = $c->tutor_old_id && $usuarios->has($c->tutor_old_id) ? $usuarios->get($c->tutor_old_id) : null;
            $c->tutor_new_user = $c->tutor_new_id && $usuarios->has($c->tutor_new_id) ? $usuarios->get($c->tutor_new_id) : null;
        }

        // Normalizar valores que pueden ser arrays (provienen de la librería de revisiones)
        foreach ($cambios_inscripciones as $c) {
            // Preparar etiquetas legibles para estado si aplica (antes de convertir arrays a JSON)
            $c->old_label_display = null;
            $c->new_label_display = null;
            if ($c->key === 'estado') {
                $oldv = $c->old_value;
                $newv = $c->new_value;

                // If values are JSON-encoded strings, try to decode to array
                if (is_string($oldv) && (str_starts_with($oldv, '{') || str_starts_with($oldv, '['))) {
                    $decoded = json_decode($oldv, true);
                    if (is_array($decoded)) $oldv = $decoded;
                }
                if (is_string($newv) && (str_starts_with($newv, '{') || str_starts_with($newv, '['))) {
                    $decoded = json_decode($newv, true);
                    if (is_array($decoded)) $newv = $decoded;
                }

                // Resolve old label
                if (is_array($oldv)) {
                    $c->old_label_display = $oldv['etiqueta'] ?? ($oldv['label'] ?? json_encode($oldv, JSON_UNESCAPED_UNICODE));
                } elseif (!empty($oldv) && is_string($oldv)) {
                    $estados = Inscripcion::getEstadosDisponibles();
                    $candidate = $estados[$oldv] ?? $oldv;
                    if (is_array($candidate)) {
                        $c->old_label_display = $candidate['etiqueta'] ?? ($candidate['label'] ?? json_encode($candidate, JSON_UNESCAPED_UNICODE));
                    } else {
                        $c->old_label_display = $candidate;
                    }
                }

                // Resolve new label
                if (is_array($newv)) {
                    $c->new_label_display = $newv['etiqueta'] ?? ($newv['label'] ?? json_encode($newv, JSON_UNESCAPED_UNICODE));
                } elseif (!empty($newv) && is_string($newv)) {
                    $estados = $estados ?? Inscripcion::getEstadosDisponibles();
                    $candidate = $estados[$newv] ?? $newv;
                    if (is_array($candidate)) {
                        $c->new_label_display = $candidate['etiqueta'] ?? ($candidate['label'] ?? json_encode($candidate, JSON_UNESCAPED_UNICODE));
                    } else {
                        $c->new_label_display = $candidate;
                    }
                }
            }

            // old_value/new_value a string
            if (is_array($c->old_value)) {
                $c->old_value = json_encode($c->old_value);
            }
            if (is_array($c->new_value)) {
                $c->new_value = json_encode($c->new_value);
            }

            // tutor_old_name / tutor_new_name como strings (si existen usuarios, usar nombre)
            $c->tutor_old_name = null;
            $c->tutor_new_name = null;
            if (!empty($c->tutor_old_user) && is_object($c->tutor_old_user)) {
                $c->tutor_old_name = $c->tutor_old_user->name ?? null;
            } elseif (!empty($c->tutor_old_id)) {
                $c->tutor_old_name = 'Usuario #' . $c->tutor_old_id;
            } elseif (!empty($c->old_value) && is_string($c->old_value)) {
                $c->tutor_old_name = $c->old_value;
            }

            if (!empty($c->tutor_new_user) && is_object($c->tutor_new_user)) {
                $c->tutor_new_name = $c->tutor_new_user->name ?? null;
            } elseif (!empty($c->tutor_new_id)) {
                $c->tutor_new_name = 'Usuario #' . $c->tutor_new_id;
            } elseif (!empty($c->new_value) && is_string($c->new_value)) {
                $c->tutor_new_name = $c->new_value;
            }

            // Asegurar que revisionable->nombre es string si es array
            if (isset($c->revisionable) && is_object($c->revisionable)) {
                if (isset($c->revisionable->nombre) && is_array($c->revisionable->nombre)) {
                    $c->revisionable->nombre = json_encode($c->revisionable->nombre);
                }
            }
            // actor_name como string
            try {
                $c->actor_name = $c->user?->name ?? '<sistema>';
            } catch (\Throwable $e) {
                $c->actor_name = '<sistema>';
            }
        }

        $busquedas = Busqueda::select(['query', 'created_at'])->latest()->take(10)->get();

        $archivos = Nodo::with('user')->latest()->take(12)->get();
        //dd($archivos->toArray()[0]);


        $inscripciones_nuevas = Inscripcion::where('estado', 'nuevo')->count();

        $tareas_pendientes = Job::count();

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
            'tareas_pendientes' => $tareas_pendientes,
            'tareas_fallidas' => $tareas_fallidas,
            'en_mantenimiento' => $en_mantenimiento,
            'revisiones' => $revisiones,
            'busquedas' => $busquedas,
            'archivos' => $archivos,
            'cambios_inscripciones' => $cambios_inscripciones,
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
            "sentimiento",
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
