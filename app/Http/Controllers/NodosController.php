<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Nodo;
use App\Pigmalion\DiskUtil;


class NodosController extends Controller
{

    public function show($id)
    {
        if (!is_numeric($id)) abort(400);

        $nodo = Nodo::findOrFail($id);

        if($nodo->es_carpeta) {
            abort(503);
        }

        list($disk, $ruta) = DiskUtil::obtenerDiscoRuta($nodo->ruta);

        return redirect(DiskUtil::normalizePath($ruta));
    }
}
