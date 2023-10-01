<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\FormularioContactoEmail;
use App\Mail\FormularioContactoEnviadoEmail;
use App\Models\Email;
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

        /* $email = new Email([
            'fromEmail' => $data['email'],
            'fromName' => $data['nombre'],
            'toEmail' => $destinatario,
            'toName' => '',
            // Puedes establecer un valor adecuado para el destinatario si lo tienes disponible
            'subject' => '',
            // Puedes establecer un valor adecuado para el asunto si lo tienes disponible
            'body' => '',
            // Puedes establecer un valor adecuado para el cuerpo del mensaje si lo tienes disponible
        ]); */

        // $email->save();

        //test
        /*$test = $request->has('test');

        if ($test)
            return $emailEnviado->render();*/

        // mensaje de confirmación al autor
        Mail::to($data['email'])
            ->cc('pigmalion@tseyor.org')
            ->queue(
                new FormularioContactoEnviadoEmail(
                    $data['nombre'],
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
                new FormularioContactoEmail(
                    $data['nombre'],
                    $data['pais'],
                    $data['email'],
                    $data['telefono'],
                    $data['comentario'],
                )
            );

        return redirect()->back()->with('success', 'Se ha enviado correctamente');
    }


    public function test()
    {
        $markdown = new Markdown(view(), config('mail.markdown'));

         return $markdown->render('emails.formulario-contacto-enviado', (
           ["nombre"=>"Juan Fernández",
            "pais"=>"España",
            "correo"=>"jmsoler77@gmail.com",
            "telefono"=>"77-0343234321",
            "comentario"=>"Quiero unirme al grupo"]
        ));
    }
}
