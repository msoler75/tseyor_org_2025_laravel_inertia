<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Access control List
 *
 * Sirve para permisos a Nodos de los usuarios
 */
class Revision extends Model
{
    use CrudTrait;

    protected $table = 'revisions';
    protected $fillable = [''];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function revisionable()
    {
        return $this->morphTo();
    }

    // ACCESSORS

    public function getColeccionAttribute()
    {
        $coleccion = str_replace("App\\Models\\", "", $this->revisionable_type);
        $coleccion = strtolower($coleccion);
        // si $coleccion termina en "n", añadimos "es", y si no, añadimos "s":
        $coleccion .= substr($coleccion, -1) == 'n' ? 'es' : 's';
        return $coleccion;
    }

    public function getContenidoUrlAttribute()
    {
        return '/' . $this->coleccion . "/" . $this->revisionable_id;
    }


    public function getAutorAttribute()
    {
        $user = $this->user;
        if ($user)
            return $user->name;
        return "<sistema>";
    }

    public function getTituloContenidoAttribute()
    {
        // Carga dinámica de la clase del modelo
        $modelClass = app()->make($this->revisionable_type);

        // si el modelo tiene softdelete
        if (method_exists($modelClass, 'withTrashed'))
            // Carga del registro específico con el ID dado
            $contenido = $modelClass::withTrashed()->find($this->revisionable_id);
        else
            // Carga del registro específico con el ID dado
            $contenido = $modelClass::find($this->revisionable_id);

        if ($contenido)
            return $contenido->titulo ?? $contenido->nombre ?? $contenido->ruta ?? "";
        return "";
    }

    public function getOperacionAttribute()
    {
        if ($this->key == 'created_at')
            return "🔨 Creado";
        if ($this->key == 'deleted_at')
            return "🗑️ Borrado";
        return "Modificado";
    }

    public function getRevisionUrlAttribute()
    {
        $coleccion = str_replace("App\\Models\\", "", $this->revisionable_type);
        $coleccion = strtolower($coleccion);
        return "/admin/" . $coleccion . "/{$this->revisionable_id}/revise";
    }
}
