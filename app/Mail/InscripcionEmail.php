<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Email;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Log;

class InscripcionEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $inscripcion;
    public $enlaceGestion;


    public function __construct(Inscripcion $inscripcion)
    {
        $this->inscripcion = $inscripcion;
        $this->enlaceGestion = rtrim(config('app.url'),'/') . '/admin/inscripcion/' . $inscripcion->id . '/edit';
    }


    public function build()
    {
        return $this->markdown('emails.formulario-inscripcion')
            ->subject('Inscripción al Curso Holístico de ' . $this->inscripcion->nombre)
            ->replyTo($this->inscripcion->email)
            ->with([
                'inscripcion' => $this->inscripcion,
                'enlace_gestion' => $this->enlaceGestion
            ]);
    }

    public function __toString(): string
    {
        return "Inscripcion {$this->inscripcion->email}";
    }

}
