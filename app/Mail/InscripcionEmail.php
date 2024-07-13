<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Email;
use Illuminate\Support\Facades\Log;

class InscripcionEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $nombre;
    public $fecha;
    public $edad;
    public $ciudad;
    public $region;
    public $pais;
    public $email;
    public $telefono;
    public $comentario;


    public function __construct(string $nombre, string $dia, string $mes, string $anyo, string $ciudad, string $region, string $pais, string $email, string $telefono, string $comentario)
    {
        $this->nombre = $nombre;
        $fechaNacimiento = \Carbon\Carbon::create($anyo, $mes, $dia);
        $this->fecha = $fechaNacimiento->format('d/m/Y');
        $this->edad = $fechaNacimiento->age;
        $this->ciudad = $ciudad;
        $this->region = $region;
        $this->pais = $pais;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->comentario = $comentario;
    }


    public function build()
    {
        return $this->markdown('emails.formulario-inscripcion')
            ->subject('Inscripción al Curso Holístico de ' . $this->nombre)
            ->replyTo($this->email)
            ->with([
                'nombre' => $this->nombre,
                'fecha_nacimiento' => $this->fecha,
                'ciudad' => $this->ciudad,
                'region' => $this->region,
                'pais' => $this->pais,
                'email' => $this->email,
                'telefono' => $this->telefono,
                'comentario' => $this->comentario,
                'edad' => $this->edad
            ]);
    }

    public function __toString(): string
    {
        return "Inscripcion {$this->email}";
    }

}
