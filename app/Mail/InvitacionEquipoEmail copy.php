<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Equipo;
use App\Models\User;
use App\Models\Invitacion;
use Illuminate\Support\Facades\URL;

class InvitacionEquipoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Equipo $equipo;
    public string $aceptarUrl;
    public string $declinarUrl;
    public ?User $usuario;
    protected $invitacion;

    public function __construct(Invitacion $invitacion)
    {
        // Generar la URL firmada para aceptar la invitación
        $this->aceptarUrl = URL::signedRoute('invitacion.aceptar', ['token' => $invitacion->token]);

        // Generar la URL firmada para declinar la invitación
        $this->declinarUrl = URL::signedRoute('invitacion.declinar', ['token' => $invitacion->token]);

        $this->equipo = Equipo::find($invitacion->equipo_id);
        $this->usuario = $invitacion->user_id? User::find($invitacion->user_id) : null;
        $this->invitacion = $invitacion;
    }

    public function build()
    {
        return $this->markdown('emails.invitacion-equipo')
            ->subject('¡Has sido invitado al equipo ' . $this->equipo->nombre . '!')
            ->with([
                'equipo' => $this->equipo,
                'nombreUsuario' => optional($this->usuario)->name ?? '',
                'aceptarUrl' => $this->aceptarUrl,
                'declinarUrl' => $this->declinarUrl
            ]);
    }


    public function send($mailer)
    {
        try {
            parent::send($mailer);

            $this->invitacion->update([
                'estado' => 'enviada',
                'error' => null
            ]);
        } catch (\Throwable $e) {
            $this->invitacion->update([
                'estado' => 'fallida',
                'error' => $e->getMessage()
            ]);

            \Log::channel('smtp')->error("Error al enviar invitación: {$e->getMessage()}", [
                'invitacion_id' => $this->invitacion->id,
                'equipo' => ['id'=>$this->invitacion->equipo_id, 'nombre' => $this->invitacion->equipo->nombre],
                'email' => $this->invitacion->email,
                'usuario' => $this->invitacion->user_id ? ['id'=>$this->invitacion->user_id, 'nombre' => $this->invitacion->user->name] : [],
                // 'stack' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-lanzamos la excepción para que Laravel sepa que el envío falló
        }
    }


    public function __toString(): string
    {
        return "InvitaciónEquipo {$this->equipo->slug}";
    }
}
