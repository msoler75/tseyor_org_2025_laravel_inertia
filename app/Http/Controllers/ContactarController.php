<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FormularioContactoEmail;
use App\Mail\FormularioContactoConfirmacionEmail;
use Illuminate\Mail\Markdown;

class ContactarController extends Controller
{
    // enviar mensaje de contacto
    public function send(Request $request)
    {

        // Validar los datos
        $data = $request->validate([
            'nombre' => 'required|max:255',
            'pais' => 'required|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'max:255',
            'comentario' => 'required',
            'destinatario' => 'max:255',
        ]);

        $destinatario = $data['destinatario'] ?? 'secretaria@tseyor.org';

        // mensaje de confirmación al autor
        Mail::to($data['email'])
            ->cc('pigmalion@tseyor.org')
            ->queue(
                new FormularioContactoConfirmacionEmail(
                    $data['nombre'],
                    $data['pais'],
                    $data['email'],
                    $data['telefono'] ?? '',
                    $data['comentario'],
                )
            );

        // mensaje al destinatario
        Mail::to($destinatario)
            ->cc('pigmalion@tseyor.org')
            ->queue(
                new FormularioContactoEmail(
                    $data['nombre'],
                    $data['pais'],
                    $data['email'],
                    $data['telefono'] ?? '',
                    $data['comentario'],
                )
            );

        return redirect()->back()->with('success', 'Se ha enviado correctamente');
    }


    public function test()
    {
        $markdown = new Markdown(view(), config('mail.markdown'));

         return $markdown->render('emails.formulario-contacto-confirmacion', (
           ["nombre"=>"Juan Fernández",
            "pais"=>"España",
            "email"=>"jmsoler77@gmail.com",
            "telefono"=>"77-0343234321",
            "comentario"=>"Quiero unirme al grupo"]
        ));
    }
}
