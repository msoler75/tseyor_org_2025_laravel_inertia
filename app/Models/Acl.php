<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;


/**
 * Access control List
 *
 * Sirve para permisos a Nodos de los usuarios
 */
class Acl extends Model
{
    use CrudTrait;

    protected $table = 'nodos_acl';


    protected $fillable = ['nodo_id', 'user_id', 'group_id', 'verbos'];


    public function nodo()
    {
        return $this->belongsTo(Nodo::class, 'nodo_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group()
    {
        return $this->belongsTo(Grupo::class, 'group_id', 'id');
    }


    // accesors
    public function getRutaNodoAttribute()
    {
        return $this->nodo->ruta;
    }


    public function getNombreUsuarioAttribute()
    {
        return optional($this->user)->name;
    }

    public function getNombreGrupoAttribute()
    {
        return optional($this->group)->nombre;
    }



    /**
     * Access Control List. Devuelve un listado de permisos para este usuario
     * $verbos es opcional, permite preseleccionar los permisos segun ciertos verbos específicos, se usa para mejorar rendimiento
     */
    public static function from(?User $user, array $verbos = null)
    {
        $user_id = $user ? $user->id : -1;
        $grupos_ids = $user ? $user->grupos()->pluck('grupos.id') : [];

        return Acl::select('nodos_acl.*', 'nodos.ruta')
            ->leftJoin('nodos', 'nodos_acl.nodo_id', '=', 'nodos.id')
            ->where(function ($query) use ($grupos_ids, $user_id) {
                $query
                    ->where('nodos_acl.user_id', $user_id)
                    ->orWhereIn('nodos_acl.group_id', $grupos_ids);
            })
            ->where(function ($query) use ($verbos) {
                if ($verbos)
                    foreach ($verbos as $verbo) {
                        $query->orWhere('nodos_acl.verbos', 'LIKE', '%' . $verbo . '%');
                    }
            })
            ->get();
    }
}
