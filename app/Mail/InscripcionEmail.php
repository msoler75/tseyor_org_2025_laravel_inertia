<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Email;

class InscripcionEmail extends Mailable implements ShouldQueue
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

    public $edad;

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
        $this->calculaEdad();
    }

    private function calculaEdad()
    {
        $fecha_actual = date("d/m/Y");
        $timestamp_fecha = strtotime($this->fecha);
        $timestamp_actual = strtotime($fecha_actual);

        $edad = date("Y", $timestamp_actual) - date("Y", $timestamp_fecha);

        // Verificar si aún no ha cumplido años en el año actual
        if (date("md", $timestamp_actual) < date("md", $timestamp_fecha)) {
            $edad--;
        }

        $this->edad = $edad;
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
            ]);
    }


}
