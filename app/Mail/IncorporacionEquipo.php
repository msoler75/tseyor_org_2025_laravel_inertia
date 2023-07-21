<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Equipo;
use App\Models\User;

class IncorporacionEquipoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Equipo $equipo;
    public User $usuario;
    public bool $incorporacion;
    public bool $fuePorSolicitud;

    public function __construct(Equipo $equipo, User $usuario, bool $incorporacion, bool $fuePorSolicitud = false)
    {
        $this->equipo = $equipo;
        $this->usuario = $usuario;
        $this->incorporacion = $incorporacion;
        $this->fuePorSolicitud = $fuePorSolicitud;
    }

    public function build()
    {
        return $this->markdown('emails.incorporacion-equipo')
            ->subject($this->incorporacion?'¡Has sido incorporado a "' . $this->equipo->nombre . '"!': 'Tu solicitud a "' . $this->equipo->nombre . '" ha sido denegada')
            ->with([
                'equipo' => $this->equipo,
                'nombreUsuario'=> $this->usuario-> name ?? '',
                'incorporacion' => $this->incorporacion,
                'fuePorSolicitud'=>  $this->fuePorSolicitud
            ]);
    }
}
