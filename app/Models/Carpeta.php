<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


/**
 * No tiene tabla propia, es un wrapper para acceder a los nodos de tipo carpeta
 */
class Carpeta extends Model
{

    protected $table = 'nodos';

    protected $esCarpeta = true;

    protected $fillable = ['ruta', 'permisos', 'user_id', 'group_id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('es_carpeta', function ($query) {
            $query->where('es_carpeta', true);
        });

        static::creating(function ($carpeta) {
            $carpeta->es_carpeta = true;
        });
    }


    /**
     * Dada una carpeta, busca los 10 archivos más recientes en esta o subcarpetas
     *
     * retorna un array de archivos
     * */
    private function listarArchivosRecursivo($directorio, &$todos)
    {
        $patron = $directorio . '/*';
        $archivos = glob($patron);

        foreach ($archivos as $archivo) {
            if (is_file($archivo)) {
                $fecha_modificacion = filemtime($archivo);
                $ruta_completa = $archivo;
                $ruta_relativa = str_replace(Storage::disk('public')->path(''), '', $ruta_completa);
                $ruta_publica = Storage::disk('public')->url($ruta_relativa);
                $todos[] = ['archivo' => basename($ruta_publica), 'url' => $ruta_publica, 'fecha_modificacion' => $fecha_modificacion];
            } else {
                $this->listarArchivosRecursivo($archivo, $todos);
            }
        }
    }

    public function ultimosArchivos()
    {
        $todos = [];
        $ruta_completa = Storage::disk('public')->path($this->ruta);

        $this->listarArchivosRecursivo($ruta_completa, $todos);

        usort($todos, function ($a, $b) {
            return $b['fecha_modificacion'] - $a['fecha_modificacion'];
        });

        $ultimos = array_slice($todos, 0, 10);

        return $ultimos;
    }
}
