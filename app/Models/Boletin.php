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
        'quincenal' => 'Quincenal',
        'mensual' => 'Mensual',
        // 'bimensual' => 'Bimensual',
        // 'trimestral' => 'Trimestral',
        // 'semestral' => 'Semestral',
        // 'anual' => 'Anual',
        'todos' => 'Todos',
    ];

    public function enviarBoletin(): bool
    {
        if ($this->enviado) {
            return false; // Ya se envió el boletín
        }

        // Si el tipo es "todos", selecciona todos los suscriptores
        if ($this->tipo === 'todos') {
            $suscriptores = Suscriptor::all();
        } else {
            $suscriptores = Suscriptor::where('servicio', 'boletin:' . $this->tipo)->get();
        }

        $destinatarios = $suscriptores->pluck('email')->toArray();

        $chunkSize = 2048; // Tamaño máximo del campo "to"
        $currentChunk = [];
        $currentSize = 2; // Inicializamos con 2 para incluir los corchetes de JSON

        foreach ($destinatarios as $email) {
            $emailSize = strlen($email) + 3; // +3 para incluir las comillas y la coma separadora

            if ($currentSize + $emailSize > $chunkSize) {
                // Crear un registro en la tabla Email para el chunk actual
                Email::create([
                    'from' => config('mail.from.address'),
                    'to' => json_encode($currentChunk),
                    'subject' => "[Boletin ID: {$this->id}] {$this->titulo}",
                    'body' => $this->texto,
                ]);

                // Reiniciar el chunk
                $currentChunk = [];
                $currentSize = 2; // Reiniciar con 2 para los corchetes de JSON
            }

            $currentChunk[] = $email;
            $currentSize += $emailSize;
        }

        // Crear un registro para el último chunk si no está vacío
        if (!empty($currentChunk)) {
            Email::create([
                'from' => config('mail.from.address'),
                'to' => json_encode($currentChunk),
                'subject' => "[Boletin ID: {$this->id}] {$this->titulo}",
                'body' => $this->texto,
            ]);
        }

        foreach ($suscriptores as $suscriptor) {
            Mail::to($suscriptor->email)->queue(new BoletinEmail($this->id, $suscriptor->id));
        }

        // actualizar el estado del boletín
        $this->enviado = true;
        $this->save();

        return true; // El boletín se envió correctamente
    }


    public function getNumeroSuscriptoresAttribute()
    {
        // Si el tipo es 'todos', contar todos los suscriptores
        if ($this->tipo === 'todos') {
            return Suscriptor::count();
        }
        // Obtener el numero de suscriptores relacionados con este boletín
        // el servicio de suscriptor es 'boletin:{tipo}'
        return Suscriptor::where('servicio', 'boletin:' . $this->tipo)->count();
    }
}
