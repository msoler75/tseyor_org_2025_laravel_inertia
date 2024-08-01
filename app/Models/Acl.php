<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Access control List
 *
 * Sirve para permisos a Nodos de los usuarios
 */
class Acl extends Model
{
    use CrudTrait;
    use \Venturecraft\Revisionable\RevisionableTrait;
//     use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $revisionCreationsEnabled = true;

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
        return $this->nodo->ubicacion;
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
        $cacheKey = 'acl_' . $user_id . ($verbos? '_' . implode('_', $verbos) : ''); // Clave para identificar la cache
        $cacheTime = 10; // Tiempo en segundos para mantener la cache

        return Cache::remember($cacheKey, $cacheTime, function () use ($user, $user_id, $verbos) {

            $grupos_ids = $user ? $user->grupos()->pluck('grupos.id') : [];

            $query = Acl::select('nodos_acl.*', 'nodos.ubicacion')
                ->leftJoin('nodos', 'nodos_acl.nodo_id', '=', 'nodos.id')
                ->where(function ($query) use ($grupos_ids, $user_id) {
                    $query
                        ->where('nodos_acl.user_id', $user_id)
                        ->orWhereIn('nodos_acl.group_id', $grupos_ids);
                })
                ->where(function ($query) use ($verbos) {
                    if ($verbos) {
                        //foreach ($verbos as $verbo) {
                            //  $query->orWhere('nodos_acl.verbos', 'LIKE', '%' . $verbo . '%');
                            //}
                            $query->where('nodos_acl.verbos', 'LIKE', '%'.$verbos[0].'%');
                            if(count($verbos)>1)
                                $query->orWhere('nodos_acl.verbos', 'LIKE', '%'.$verbos[1].'%');
                            if(count($verbos)>2)
                                $query->orWhere('nodos_acl.verbos', 'LIKE', '%'.$verbos[2].'%');
                        }
                });
                \Log::info("query: ".$query->toSql());

                return $query->get();
        });
    }

    public static function clearCache($user) {
        $cacheKey ='acl_' . $user->id;
        Cache::forget($cacheKey);
    }




    /**
     * Devuelve un listado de permisos acl para un array de nodos
     */
    public static function inNodes(array $nodo_ids)
    {
        return Acl::select('nodos_acl.*', 'grupos.nombre as grupo', 'users.name as usuario')
            ->leftJoin('users', 'nodos_acl.user_id', '=', 'users.id')
            ->leftJoin('grupos', 'nodos_acl.group_id', '=', 'grupos.id')
            ->whereIn('nodo_id', $nodo_ids)
            ->get();
    }
}
