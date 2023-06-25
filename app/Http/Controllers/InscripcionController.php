<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscripcion;

class InscripcionController extends Controller
{
    //
    public function store(Request $request)
    {
        // Validar los datos
        $validatedData = $request->validate([
            'nombre' => 'required|max:255',
            'dia' => 'required|integer|min:1|max:31',
            'mes' => 'required|integer|min:1|max:12',
            'anyo' => 'required|integer|min:1900|max:'.(date('Y')-17),
            'ciudad' => 'required|max:255',
            'region' => 'required|max:255',
            'pais' => 'required|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'required|max:255',
            'comentario' => 'nullable'
        ]);

        // Construir la fecha
        $fecha = $validatedData['anyo'] . "-" . $validatedData['mes'] . "-" . $validatedData['dia'];

        // Crear una nueva instancia de Inscripcion y guardarla en la base de datos
        $inscripcion = Inscripcion::create([
            'nombre' => $validatedData['nombre'],
            'fecha_nacimiento' => $fecha,
            'ciudad' => $validatedData['ciudad'],
            'region' => $validatedData['region'],
            'pais' => $validatedData['pais'],
            'email' => $validatedData['email'],
            'telefono' => $validatedData['telefono'],
            'comentario' => $validatedData['comentario']
        ]);

        if ($inscripcion) {
            // Redirigir al usuario a la página anterior con un mensaje de éxito
            return redirect()->back()->with('success', 'La inscripción se ha guardado correctamente');
        } else {
            // Devolver un objeto JSON con los errores de validación
            return redirect()->back()->withErrors(['msg', 'No se pudo guardar la inscripción, inténtalo de nuevo']);
        }
    }
}
