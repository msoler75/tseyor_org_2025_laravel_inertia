<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class Nodo extends Model
{
    use CrudTrait;
    use Searchable;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $revisionCreationsEnabled = true;

    protected $fillable = ['ubicacion', 'permisos', 'user_id', 'group_id', 'es_carpeta'];

    protected $attributes = [
        'ubicacion' => '',
        'permisos' => '1755',
        'user_id' => 1,
        // Por ejemplo, puedes usar 0 como valor predeterminado para el user_id
        'group_id' => 1,
        // O null si no tiene grupo por defecto
        'es_carpeta' => 1,
        // Por ejemplo, un valor booleano como valor predeterminado
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo(Grupo::class, 'group_id', 'id');
    }



    // accesors
    public function getNombreUsuarioAttribute()
    {
        return $this->user->name; // Reemplaza `name` por el nombre del atributo que contiene el nombre del usuario en tu modelo `User`
    }

    public function getNombreGrupoAttribute()
    {
        return $this->group->nombre; // Reemplaza `nombre` por el nombre del atributo que contiene el nombre del grupo en tu modelo `Grupo`
    }


    public function getStickyAttribute(): bool
    {
        $permisos = octdec($this->permisos);
        return $permisos & (0b001 << 9);
    }



    /**
     * Obtiene el nodo más cercano siendo el mismo o antecesor de la ubicacion
     */
    public static function desde($ubicacion): ?Nodo
    {
        if ($ubicacion == 'mis_archivos') return null;

        $nodo = Nodo::select(['nodos.*']) //, 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            //->leftJoin('users', 'users.id', '=', 'user_id')
            //->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->whereRaw("? LIKE CONCAT(nodos.ubicacion, '%')", [$ubicacion])
            ->orderByRaw('LENGTH(nodos.ubicacion) DESC')
            ->first();

        if (!$nodo) {
            // crea un nodo por con los permisos por defecto
            $nodo = new Nodo();
            $nodo->ubicacion = $ubicacion;
            //$nodo->propietario_usuario = "admin"; // valores por defecto
            //$nodo->propietario_grupo = "admin";
            // $nodo->save();
        }
        return $nodo;
    }


    /**
     * Obtiene todos los nodos de un usuario
     */
    public static function de($idUser)
    {
        return Nodo::select(['nodos.*']) //, 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            //->leftJoin('users', 'users.id', '=', 'user_id')
            //->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->where('nodos.user_id', '=', $idUser)
            ->orderByRaw('LENGTH(nodos.ubicacion) DESC')
            ->get();
    }


    /**
     * Obtiene todos los nodos de la carpeta, sin incluir el nodo de la carpeta
     */
    public static function hijos($ubicacion)
    {
        return Nodo::select(['nodos.*']) //, 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            //->leftJoin('users', 'users.id', '=', 'user_id')
            //->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->where('nodos.ubicacion', 'LIKE', $ubicacion . '/%')
            ->whereRaw("LENGTH(nodos.ubicacion) - LENGTH(REPLACE(nodos.ubicacion, '/', '')) = " . (substr_count($ubicacion, '/') + 1))
            ->orderByRaw('LENGTH(nodos.ubicacion) ASC')
            ->get();
    }




    /**
     * Renombra los nodos afectados por cambios en la ubicacion
     * Ejemplo: renombrar un archivo, o mover un archivo de una carpeta a otra
     */
    public static function mover($from, $to)
    {
        Nodo::where('ubicacion', 'like', "$from/%")
            ->orWhere('ubicacion', $from)
            ->update([
                'ubicacion' => DB::raw("CONCAT('$to', SUBSTRING(ubicacion, LENGTH('$from') + 1))")
            ]);
    }

    /**
     * Crea un nuevo nodo, indicando la ubicacion y opcionalmente el usuario propietario
     */
    public static function crear(string $ubicacion, bool $es_carpeta = false, ?User $user = null)
    {
        // Obtén el valor de umask desde el archivo de configuración
        $umask = Config::get('app.umask');

        // Convertir umask y permisos a octal
        $umask = octdec($umask);

        // aplicamos el umask, con el sticky bit 1
        $permisos_defecto = 01777 & ~$umask;

        /*Nodo::create([
            'ruta' => $ubicacion,
            'user_id' => optional($user)->id ?? 1,
            'group_id' => 1,
            'permisos' => decoct($permisos),
            'es_carpeta' => $es_carpeta
        ]);*/

        // tomará el mismo grupo que el nodo de la carpeta padre

        $padre = self::desde(dirname($ubicacion));

        Nodo::updateOrCreate(
            ['ubicacion' => $ubicacion],
            [
                'user_id' => optional($user)->id ?? 1,
                'group_id' => $padre ? $padre->group_id : 1,
                'permisos' => $padre ? $padre->permisos : decoct($permisos_defecto),
                'es_carpeta' => $es_carpeta
            ]
        );
    }




    /**
     * Comprueba con la ACL si tiene el acceso a un nodo en concreto
     */
    public function tieneAcceso(?User $user, string $verbo = null)
    {
        $aclist = optional($user)->accessControlList();
        if (!$aclist)
            return false;

        Log::info("Nodo::tieneAcceso " . $user->name . ", verbo: $verbo");

        // filtramos por verbo
        if ($verbo) {
            Log::info("filtramos por verbo: $verbo");
            $aclist = $aclist->filter(function ($acl) use ($verbo) {
                return strpos($acl->verbos, $verbo) !== false;
            });
        }
        $aclListArray = $aclist->toArray();
        Log::info("AclList de " . optional($user)->name . ":", $aclListArray);


        // tiene acceso global para todos los nodos?
        /*
        if (1) { // probamos diferentes implementaciones
            if ($aclist->whereNull('nodo_id')->count() > 0)
                return true;

            // tiene acceso a este nodo?
            if ($aclist->where('nodo_id', $this->id)->count() > 0)
                return true;
        } else {
            // Filtramos por verbo
            $aclListArray = array_filter($aclListArray, function ($nodo) use ($verbo) {
                return strpos($this->verbos, $verbo) !== false;
            });

            // Tiene acceso global para todos los nodos?
            $nodoIdIsNull = false;

            foreach ($aclListArray as $nodo) {
                if (!isset($nodo['nodo_id'])) {
                    $nodoIdIsNull = true;
                    break;
                }
            }

            if ($nodoIdIsNull) {
                return true;
            }
        }*/

        // tiene acceso a una carpeta padre?

        // parece que los LIKE no funcionan aquí:
        //if ($aclist->where("'$nodo->ubicacion'", 'LIKE', "CONCAT(ubicacion, '%')")->count() > 0)
        //  return true;

        Log::info("Estamos mirando el nodo:", $this->toArray());

        foreach ($aclListArray as $acl) {
            Log::info("Checando acceso para " . $acl['ubicacion']);
            if (strpos($this->ubicacion, $acl['ubicacion']) === 0) {
                Log::info("Acceso concedido para " . $acl['ubicacion']);
                return true;
            }
        }

        return false;
    }




    // SCOUT searchable

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            // <- Always include the primary key
            'title' => basename($this->ubicacion),
            'ubicacion' => $this->ubicacion
        ];
    }
}
