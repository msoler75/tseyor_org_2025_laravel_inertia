<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorito;
use Illuminate\Support\Facades\Auth;

class FavoritosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Añadir contenido a favoritos
    public function store(Request $request, $coleccion = null, $id_ref = null)
    {
        $user = Auth::user();

        $favorito = Favorito::firstOrCreate([
            'user_id' => $user->id,
            'coleccion' => $coleccion,
            'id_ref' => $id_ref,
        ]);

        return response()->json(['success' => true, 'favorito' => $favorito], 201);
    }

    // Eliminar favorito (por id del contenido o id del favorito)
    public function destroy(Request $request, $coleccion = null, $id_ref = null)
    {
        $user = Auth::user();
        if ($coleccion && $id_ref) {
            $deleted = Favorito::where('user_id', $user->id)
                ->where('coleccion', $coleccion)
                ->where('id_ref', $id_ref)
                ->delete();
        } else {
            // si se pasa id de favorito por body
            $id = $request->input('id');
            $deleted = 0;
            if ($id) {
                $deleted = Favorito::where('id', $id)->where('user_id', $user->id)->delete();
            }
        }

        return response()->json(['success' => (bool) $deleted]);
    }
}
