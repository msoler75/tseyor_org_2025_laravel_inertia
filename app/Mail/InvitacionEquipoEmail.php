<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Models\Equipo;
use App\Models\User;
use App\Models\Invitacion;
use Illuminate\Support\Facades\URL;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Jobs\Middleware\EmailRateLimited;

class InvitacionEquipoEmail  extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function middleware()
    {
        return [new EmailRateLimited];
    }


    public $tries = 500; // Número máximo de intentos
    public $backoff = 600; // Tiempo en segundos entre intentos

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
        $info = [
            'invitacion_id' => $this->invitacion->id,
            'email' => $this->invitacion->email,
            'usuario' => $this->invitacion->user_id ? ['id' => $this->invitacion->user_id, 'nombre' => $this->invitacion->user->name] : [],
            'equipo' => ['id' => $this->invitacion->equipo_id, 'nombre' => $this->invitacion->equipo->nombre],
        ];

        try {
            $invitacion = Invitacion::find($this->invitacion->id);

            if ($invitacion->estado === 'cancelada') {
                Log::channel('smtp')->info("InvitacionEquipoMail #{$invitacion->id} cancelada. No se envía correo");
                return;
            }

            if (in_array($invitacion->estado, ['aceptada', 'registro', 'declinada'])) {
                Log::channel('smtp')->info("InvitacionEquipoMail #{$invitacion->id} {$invitacion->estado}. No se envía correo");
                return;
            }

            Log::channel('smtp')->info("Enviando invitación a equipo: ", $info);

            parent::send($mailer);


            $this->invitacion->update([
                'sent_at' => Carbon::now('Europe/Madrid'),
                'estado' => $this->invitacion->estado === 'registro' ? 'registro' : 'enviada',
                'error' => null
            ]);
        } catch (\Throwable $e) {
            $this->invitacion->update([
                'sent_at' => Carbon::now('Europe/Madrid'),
                'estado' => 'fallida',
                'error' => $e->getMessage()
            ]);

            Log::channel('smtp')->error("Error al enviar invitación: {$e->getMessage()}", $info);

            throw $e;
        }
    }

    public function __toString(): string
    {
        return "InvitaciónEquipo {$this->equipo->slug}";
    }
}
