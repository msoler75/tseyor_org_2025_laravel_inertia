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
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    private function superaLimite()
    {
        // contamos cuantas invitaciones globales hay enviadas (sent_at) en la ultima hora
        $invitaciones_ultima_hora = Invitacion::where('sent_at', '>=', Carbon::now()->subHour())->count();
        $max_por_hora = config('app.invitaciones.max_por_hora', 50);
        Log::channel('smtp')->info("invitaciones en la ultima hora : $invitaciones_ultima_hora. Máximo: $max_por_hora");
        return $invitaciones_ultima_hora >= $max_por_hora;
    }


    public function send($mailer)
    {
        $info = [
            'invitacion_id' => $this->invitacion->id,
            'email' => $this->invitacion->email,
            'usuario' => $this->invitacion->user_id ? ['id'=>$this->invitacion->user_id, 'nombre' => $this->invitacion->user->name] : [],
            'equipo' => ['id'=>$this->invitacion->equipo_id, 'nombre' => $this->invitacion->equipo->nombre],
            // 'stack' => $e->getTraceAsString(),
        ];


        try {

            $invitacion = Invitacion::find($this->invitacion->id);
            if($invitacion->estado == 'cancelada')
            {
                Log::channel('smtp')->info("InvitacionEquipoMail #{$invitacion->id} cancelada. No se envía correo");
                return;
            }

            if($invitacion->estado == 'aceptada' || $invitacion->estado == 'registro' || $invitacion->estado == 'declinada')
            {
                Log::channel('smtp')->info("InvitacionEquipoMail #{$invitacion->id} {$invitacion->estado}. No se envía correo" );
                return;
            }

            $limiteEncontrado = $this->superaLimite();
            if ($limiteEncontrado) {
                $minutosEspera = config("app.invitaciones.minutos_espera", 30);

                Log::channel('smtp')->info("InvitacionEquipoMail #{$invitacion->id}. Limite encontrado");

                // Si es la primera vez que encontramos el límite o ya pasó el tiempo de espera
                $nuevoTiempoEjecucion = now()->addMinutes($minutosEspera);

                // Reencolamos el envío, pero con un tiempo de espera
                Mail::to($this->invitacion->email)->later($nuevoTiempoEjecucion, new self($this->invitacion));

                Log::channel('smtp')->info("Límite alcanzado. Reencolando invitación para equipo: ", $info);

                $this->invitacion->update([
                    'estado' => 'pendiente',
                    'error' => 'Límite de envío alcanzado. Tarea reencolada.'
                ]);

                return; // Salimos del método sin enviar el correo
            }

            Log::channel('smtp')->info("Enviando invitación a equipo: ", $info);

            // simularemos el envío con un sleep
            // sleep(2);
            parent::send($mailer);

            $this->invitacion->update([
                'sent_at' =>  Carbon::now('Europe/Madrid'),
                'estado' => $this->invitacion->estado == 'registro'? 'registro' : 'enviada',
                'error' => null
            ]);
        } catch (\Throwable $e) {
            $this->invitacion->update([
                'sent_at' =>  Carbon::now('Europe/Madrid'),
                'estado' => 'falli  da',
                'error' => $e->getMessage()
            ]);

            Log::channel('smtp')->error("Error al enviar invitación: {$e->getMessage()}", $info);

            throw $e; // Re-lanzamos la excepción para que Laravel sepa que el envío falló
        }
    }


    public function __toString(): string
    {
        return "InvitaciónEquipo {$this->equipo->slug}";
    }
}
