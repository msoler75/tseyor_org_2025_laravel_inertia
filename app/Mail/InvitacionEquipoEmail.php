<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Equipo;
use App\Models\User;

class InvitacionEquipoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Equipo $equipo;
    public string $aceptarUrl;
    public string $declinarUrl;
    public User $usuario;

    public function __construct(Equipo $equipo, ?User $usuario, string $aceptarUrl, string $declinarUrl)
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
