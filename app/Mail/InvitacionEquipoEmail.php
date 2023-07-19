<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Equipo;

class InvitacionEquipoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $equipo;
    public $aceptarUrl;
    public $declinarUrl;
    public $usuario;

    public function __construct(Equipo $equipo, $usuario, $aceptarUrl, $declinarUrl)
    {
        $this->equipo = $equipo;
        $this->usuario = $usuario;
        $this->aceptarUrl = $aceptarUrl;
        $this->declinarUrl = $declinarUrl;
    }

    public function build()
    {
        return $this->markdown('emails.invitacion-equipo-email')
            ->subject('¡Has sido invitado al equipo ' . $this->equipo->nombre . '!')
            ->with([
                'nombreUsuario'=> optional($this->usuario)-> name ?? '',
                'aceptarUrl' => $this->aceptarUrl,
                'declinarUrl' => $this->declinarUrl
            ]);
    }
}
