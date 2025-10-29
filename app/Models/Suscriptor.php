<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Suscriptor extends Model
{
    use CrudTrait;
    protected $table = "suscriptores";
    protected $fillable = ['servicio', 'email', 'token', 'estado'];


    /* servicio puede ser :
    boletin:semanal
    boletin:quincenal
    boletin:mensual
    boletin:bimensual
    boletin:trimestral
    */
}
