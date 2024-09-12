<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\InstantEmail;
use Illuminate\Mail\Mailables\Attachment;
use App\Pigmalion\SEO;

class TarjetaVisitaController extends Controller
{

    public function index()
    {

        $user = auth()->user();

        if (!$user)
            return redirect('/login');
        // abort(401, 'Acceso no autorizado');

        //mirar si pertenece al grupo "muul"
        $is_admin = $user->name=='admin';
        if (!$is_admin &&  !$user->grupos()->where('slug', 'muul')->exists())
            abort(403, 'Debes ser Muul para ver esta página');

        return Inertia::render('Muul/TarjetaVisita', [])
            ->withViewData(SEO::get('muul/tarjetavisita'));
    }

    // enviar mensaje de contacto
    public function send(Request $request)
    {
        $user = auth()->user();

        if (!$user)
            abort(401, 'Acceso no autorizado');

        // Validar los datos
        $data = $request->validate([
            'nombre_tseyor' => 'required|max:255',
            'email_tseyor' => 'required|email|max:255',
            // archivo de imagen
            'imagen' => 'required|image|mimes:jpeg,jpg,png|max:2048000',
        ]);

        $destinatario = $data['email_tseyor'];


        // Adjuntar la imagen al correo con un nombre personalizado
        $imagenPath = $request->file('imagen')->path();
        $nombreImagenOriginal = $request->file('imagen')->getClientOriginalName();
        $nombreImagenNuevo = 'tarjetaVisita_' . $data['nombre_tseyor'] . '_' . time() . '.' . $request->file('imagen')->getClientOriginalExtension();

        Mail::to($destinatario)
            ->bcc('msgp753@gmail.com')
            ->send(
                new InstantEmail(
                    "emails.tarjeta-visita",
                    [
                        'subject' => 'Tu tarjeta de visita TSEYOR',
                        'nombre' => $data['nombre_tseyor'],
                        'correo_tseyor' => $data['email_tseyor'],
                        'attachments' => [
                            Attachment::fromPath($imagenPath)
                                ->as($nombreImagenNuevo)
                                ->withMime($request->file('imagen')->getMimeType())
                        ]
                    ]
                )
            );

        return redirect()->back()->with('success', 'Se ha enviado correctamente');

    }

}
