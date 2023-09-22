<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactarController extends Controller
{
    // enviar mensaje de contacto
    public function send(Request $request) {

        // Validar los datos
        $validatedData = $request->validate([
            'nombre' => 'required|max:255',
            'pais' => 'required|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'max:255',
            'comentario' => 'required'
        ]);


        // To-DO : email

        return redirect()->back()->with('success', 'La inscripción se ha guardado correctamente');
    }
}
