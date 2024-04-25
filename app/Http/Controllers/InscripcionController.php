<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InscripcionEmail;
use App\Mail\InscripcionConfirmacionEmail;

class InscripcionController extends Controller
{
    //
    public function store(Request $request)
    {
        // Validar los datos
        $dataValidated = $request->validate([
            'nombre' => 'required|max:255',
            'dia' => 'required|integer|min:1|max:31',
            'mes' => 'required|integer|min:1|max:12',
            'anyo' => 'required|integer|min:1900|max:' . (date('Y') - 17),
            'ciudad' => 'required|max:255',
            'region' => 'required|max:255',
            'pais' => 'required|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|max:255',
            'comentario' => 'nullable'
        ]);

        // Construir la fecha
        $fecha_sql = $dataValidated['anyo'] . "-" . $dataValidated['mes'] . "-" . $dataValidated['dia'];

        $data = [
            'nombre' => $dataValidated['nombre'],
            'fecha_nacimiento' => $fecha_sql,
            'ciudad' => $dataValidated['ciudad'],
            'region' => $dataValidated['region'],
            'pais' => $dataValidated['pais'],
            'email' => $dataValidated['email'],
            'telefono' => $dataValidated['telefono'] ?? "",
            'comentario' => $dataValidated['comentario'] ?? ""
        ];

        // Crear una nueva instancia de Inscripcion y guardarla en la base de datos
        $inscripcion = Inscripcion::create($data);
        $destinatario = 'secretaria@tseyor.org';

        // mensaje de confirmación al autor
        Mail::to($data['email'])
            ->cc('pigmalion@tseyor.org')
            ->queue(
                new InscripcionConfirmacionEmail(
                    $data['nombre'],
                    $dataValidated['dia'],
                    $dataValidated['mes'],
                    $dataValidated['anyo'],
                    $data['ciudad'],
                    $data['region'],
                    $data['pais'],
                    $data['email'],
                    $data['telefono'],
                    $data['comentario'],
                )
            );

        // mensaje al destinatario
        Mail::to($destinatario)
            ->cc('pigmalion@tseyor.org')
            ->queue(
                new InscripcionEmail(
                    $data['nombre'],
                    $dataValidated['dia'],
                    $dataValidated['mes'],
                    $dataValidated['anyo'],
                    $data['ciudad'],
                    $data['region'],
                    $data['pais'],
                    $data['email'],
                    $data['telefono'],
                    $data['comentario'],
                )
            );

        if ($inscripcion) {
            // Redirigir al usuario a la página anterior con un mensaje de éxito
            return redirect()->back()->with('success', 'La inscripción se ha guardado correctamente');
        } else {
            // Devolver un objeto JSON con los errores de validación
            Log::error("Inscripción. No se pudo guardar la inscripción ", $data);
            return redirect()->back()->withErrors(['No se pudo guardar la inscripción, inténtalo de nuevo']);
        }
    }
}
