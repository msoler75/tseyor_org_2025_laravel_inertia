<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use App\Traits\EsCategorizable;

class Experiencia extends ContenidoBaseModel
{
    use CrudTrait;
    use EsCategorizable;
    protected $categoriaSimple = true;

    protected function incluyeCategoria($categoria_nombre_total): bool
    {
        if ($categoria_nombre_total['nombre'] != self::$CATEGORIA_INTERIORIZACION) return true;

        $user = auth()->user();
        if (!$user) return false;

        if ($user->can('administrar experiencias')) return true;

        return $user->esIniciado();
    }


    // PARA EL FORMULARIO
    // el valor es la descripción de la categoría
    public static $categorias = [
        'Sueños' => 'Experiencias oníricas',
        'Extrapolaciones' => 'Movimiento ondulatorio o visión estereoscópica',
        'Seiph' => 'Extrapolación a nuestro ordenador SEIPH',
        'Experiencia de campo (Grupal)' => 'Experiencia de campo (Grupal)',
        'Rescate adimensional (Grupal)' => 'Rescate adimensional (Grupal)',
        'Encuentros vis a vis' => 'Encuentro físico con algún hermano H1',
        'Cartas/Pictografías' => 'Experiencias con las cartas o láminas pictográficas',
        'Interiorización' => 'Experiencias de los talleres de interiorización',
        'Otras experiencias' => 'Cualquier otra experiencia que no sea una de las anteriores',
    ];

    public static $CATEGORIA_INTERIORIZACION = 'Interiorización';

    protected $table = 'experiencias';

    protected $fillable = [
        'nombre',
        'fecha',
        'lugar',
        'categoria',
        'texto',
        'visibilidad',
        'archivo',
        'user_id', // creador de la experiencia, en el caso de recopilación de experiencias grupales
    ];

    public static function search($term)
    {
        return static::query()
            ->where('nombre', 'LIKE', "%{$term}%")
            ->orWhere('texto', 'LIKE', "%{$term}%");
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
