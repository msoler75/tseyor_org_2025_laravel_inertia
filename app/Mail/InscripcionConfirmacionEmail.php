<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Email;

class InscripcionConfirmacionEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $nombre;
    public $fecha;
    public $ciudad;
    public $region;
    public $pais;
    public $email;
    public $telefono;
    public $comentario;

    public function __construct(string $nombre, string $fecha, string $ciudad, string $region, string $pais, string $email, string $telefono, string $comentario)
    {
        $this->nombre = $nombre;
        $this->fecha = $fecha;
        $this->ciudad = $ciudad;
        $this->region = $region;
        $this->pais = $pais;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->comentario = $comentario;
    }

    public function build()
    {
        return $this->markdown('emails.formulario-inscripcion-confirmacion')
            ->subject('Te has inscrito al Curso Holístico de Tseyor')
            ->with([
                'nombre' => $this->nombre,
                'fecha_nacimiento' => $this->fecha,
                'ciudad' => $this->ciudad,
                'region' => $this->region,
                'pais' => $this->pais,
                'email' => $this->email,
                'telefono' => $this->telefono,
                'comentario' => $this->comentario,
            ]);
    }


}
