<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use CrudTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'url',
        'texto',
        'user_id',
        'respuesta_a',
        'eliminado'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ACCESSORS

    public function getShortUrlAttribute() {
        $base = url("");
        return str_replace($base, "", $this->url);
    }


    public function getAutorAttribute() {
        $user = $this->user;
        return $user->name;
    }


    public function getTituloContenidoAttribute() {
        /* la url tiene este patrón:
         /noticias/3    ó   /comunicados/6
         entonces podemos extraer la primera palabra y será la colección, y la segunda es el id
         */
        $base = url("");
        $url = str_replace($base, "", $this->url);
        $parts= preg_split("/\//", $url, -1, PREG_SPLIT_NO_EMPTY);
        $coleccion = $parts[0];
        $id = $parts[1];
        $contenido = Contenido::select(['titulo', 'coleccion', 'id_ref'])->where('coleccion', $coleccion)->where('id_ref', $id)->first();
        if($contenido)
        return  $contenido->titulo;
        return "";
    }
}
