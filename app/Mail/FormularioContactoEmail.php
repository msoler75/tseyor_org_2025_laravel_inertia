<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Email;

class FormularioContactoEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $nombre;
    public $pais;
    public $email;
    public $telefono;
    public $comentario;

    public function __construct(string $nombre, string $pais, string $email, string $telefono, string $comentario)
    {
        $this->nombre = $nombre;
        $this->pais = $pais;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->comentario = $comentario;
    }

    public function build()
    {
        return $this->markdown('emails.formulario-contacto')
            ->subject('Mensaje desde el formulario de contacto de ' . $this->nombre)
            ->replyTo($this->email)
            ->with([
                'nombre' => $this->nombre,
                'pais' => $this->pais,
                'email' => $this->email,
                'telefono' => $this->telefono,
                'comentario' => $this->comentario,
            ]);
    }

    public function __toString(): string
    {
        return "Contacto {$this->email}";
    }
}
