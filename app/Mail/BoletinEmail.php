<?php

namespace App\Mail;

use App\Models\Boletin;
use App\Models\Suscriptor;
use Illuminate\Support\Facades\Log;
use App\Pigmalion\Markdown;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\EmailRateLimited;

class BoletinEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 500; // Número máximo de intentos
    public $backoff = 60; // Tiempo en segundos entre intentos

     public function middleware()
    {
        return [new EmailRateLimited];
    }

    protected string $jobType = 'boletin';
    protected $boletin_id;
    protected $suscriptor_id;

    public function __construct(int $boletin_id, int $suscriptor_id)
    {
        Log::channel("smtp")->info("Iniciando el constructor de BoletinEmail con boletin_id={$boletin_id} y suscriptor_id={$suscriptor_id}");
        $this->boletin_id = $boletin_id;
        $this->suscriptor_id = $suscriptor_id;

        $boletin = Boletin::findOrFail($this->boletin_id);
        $suscriptor = Suscriptor::findOrFail($this->suscriptor_id);

        if (!$boletin || !$suscriptor) {
            Log::channel("smtp")->error("Boletin o Suscriptor no encontrado: boletin_id={$this->boletin_id}, suscriptor_id={$this->suscriptor_id}");
            throw new \Exception("Boletin o Suscriptor no encontrado: boletin_id={$this->boletin_id}, suscriptor_id={$this->suscriptor_id}");
        }
    }

    public function build()
    {
        Log::channel("smtp")->info("Enviando boletín {$this->boletin_id} a {$this->suscriptor_id}");

        $boletin = Boletin::find($this->boletin_id);
        $suscriptor = Suscriptor::find($this->suscriptor_id);

        if (!$boletin || !$suscriptor) {
            Log::channel("smtp")->error("Boletin o Suscriptor no encontrado: boletin_id={$this->boletin_id}, suscriptor_id={$this->suscriptor_id}");
            throw new \Exception("Boletin o Suscriptor no encontrado: boletin_id={$this->boletin_id}, suscriptor_id={$this->suscriptor_id}");
        }

        Log::channel("smtp")->info("Datos del boletin: ", [
            'titulo' => $boletin->titulo ?? 'N/A',
            'texto' => $boletin->texto ?? 'N/A'
        ]);
        Log::channel("smtp")->info("Datos del suscriptor: ", [
            'email' => $suscriptor->email ?? 'N/A',
            'token' => $suscriptor->token ?? 'N/A'
        ]);

        return $this->markdown('emails.boletin')
            ->subject($boletin->titulo)
            ->with([
                'titulo' => $boletin->titulo,
                'texto' => $boletin->texto,
                'token' => $suscriptor->token,
            ]);
    }

    public function failed()
    {
        Log::channel("smtp")->error("Error al enviar el boletín {$this->boletin_id} a {$this->suscriptor_id}");
        $suscriptor = Suscriptor::find($this->suscriptor_id);
        if ($suscriptor) {
            Log::channel("smtp")->error("Guardando estado error para el suscriptor {$this->suscriptor_id}");
            $suscriptor->update(['estado' => 'error']);
        }
    }

    // antes marcamos 'error' si fallaba (failed). Pero si funciona, marca el estado del suscriptor como 'ok':
    public function sent()
    {
        Log::channel("smtp")->info("Boletín {$this->boletin_id} enviado a {$this->suscriptor_id}");
        $suscriptor = Suscriptor::find($this->suscriptor_id);
        if ($suscriptor) {
            Log::channel("smtp")->info("Guardando estado ok para el suscriptor {$this->suscriptor_id}");
            $suscriptor->update(['estado' => 'ok']);
        }
    }
}
