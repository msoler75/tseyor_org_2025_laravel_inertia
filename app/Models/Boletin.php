<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\Suscriptor;
use App\Mail\BoletinEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\Email;
use App\Traits\EsCategorizable;

class Boletin extends Model
{
    use CrudTrait;
    use EsCategorizable;

    protected $campoCategoria = 'tipo';

    protected $table = "boletines";

    protected $fillable = ['titulo', 'texto', 'dia', 'mes', 'anyo', 'semana', 'tipo', 'enviado'];
    // tipo: enum: semanal, bisemanal, mensual, bimensual, trimestral, semestral, anual

    const TIPOS = [
        'semanal' => 'Semanal',
        'bisemanal' => 'Bisemanal',
        'mensual' => 'Mensual',
        'bimensual' => 'Bimensual',
        'trimestral' => 'Trimestral',
        'semestral' => 'Semestral',
        'anual' => 'Anual',
    ];

    public function enviarBoletin(): bool
    {
        if ($this->enviado) {
            // Verificar si el boletín ya fue enviado a estos destinatarios
            return false; // Ya se envió el boletín a estos destinatarios
        }

        // solo los suscriptores que tienen el servicio de boletín segun su tipo
        // ejemplo: 'boletin:semanal', 'boletin:bisemanal', 'boletin:mensual', etc.
        $suscriptores = Suscriptor::where('servicio', 'boletin:' . $this->tipo)->get();

        $destinatarios = $suscriptores->pluck('email')->toArray();

        // Verificar si ya se envió el boletín

        // Crear un único registro en la tabla Email
        Email::create([
            'from' => config('mail.from.address'),
            'to' => json_encode($destinatarios),
            'subject' => "[Boletin ID: {$this->id}] {$this->titulo}",
            'body' => $this->texto,
        ]);

        // Encolar el envío del boletín
        foreach ($suscriptores as $suscriptor) {
            Mail::to($suscriptor->email)->queue(new BoletinEmail($this->id, $suscriptor->id));
        }

        // Actualizar el estado del boletín como enviado
        $this->enviado = 1;
        $this->save();

        return true; // El boletín se envió correctamente
    }
}
