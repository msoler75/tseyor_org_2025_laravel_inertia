<?php

namespace App\Http\Controllers;

use App\Models\Nodo;


class NodosController extends Controller
{

    public function show($id)
    {
        if (!is_numeric($id)) abort(400);

        $nodo = Nodo::findOrFail($id);

        return redirect($nodo->ubicacion);
    }
}
