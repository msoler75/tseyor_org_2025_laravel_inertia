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

        if ($id) {
            $email = Email::findOrFail($id);
        }

        $resultados = Email::latest()->paginate(50);

        return Inertia::render('Emails/Index', [
            'listado' => $resultados,
            'email' => $email
        ]);
    }

}
