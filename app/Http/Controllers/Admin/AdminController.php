<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Comentario;
use App\Models\Contenido;
use App\Models\Revision;
use App\Models\Busqueda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Pigmalion\DiskUtil;
use Illuminate\Support\Facades\Auth;


class AdminController
{

    public function dashboard()
    {
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

        $data = [
            'users_creados' => $users_creados,
            'users_activos' => $users_activos,
            'comentarios' => $comentarios,
            'contenidos_creados' => $contenidos_creados,
            'contenidos_modificados' => $contenidos_modificados,
            'revisiones' => $revisiones,
            'busquedas' => $busquedas
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

        $content = file_get_contents($file);
        return response()->json(['content' => $content]);
    }


    public function listImages($ruta)
    {

        $imagenes = DiskUtil::obtenerImagenes($ruta);

        // return json response
        return response()->json($imagenes);
    }


    public function loginAs($idUser)
    {
        $user = Auth::user();
        if (!$user || $user->id !== 1)
            return response()->json(['message' => 'Acceso no permitido'], 403);

        // cambiamos a nuevo usuario
        $user = User::find($idUser);
        if (!$user)
            return response()->json(['message' => 'usuario no encontrado'], 404);

        Auth::login($user); // Autenticar al usuario

        return response()->json(['message' => 'usuario cambiado'], 200);
    }
}
