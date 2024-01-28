<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Email;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class EmailsController extends Controller
{

    public function index(Request $request)
    {

        $user = auth()->user();

        $esAdministrador = optional($user)->hasPermissionTo('administrar archivos');

        if (!$esAdministrador) {
            abort(403, 'No tienes permisos');
        }

        $id = $request->id;

        $email = null;

        if ($id)
            $email = Email::findOrFail($id);

        if ($email) {
            // determinamos en qué pagina está el email:
            $page = $email->id / 40 + 1;
            $page = 1;
            // cargamos esa página en los resultados
            $resultados = Email::select()->orderBy('created_at', 'desc')->paginate(40, ['*'], 'page', $page);
        } else
            $resultados = Email::latest()->paginate(40);

        return Inertia::render('Emails/Index', [
            'listado' => $resultados,
            'email' => $email
        ]);
    }


}
