<?php


namespace App\Traits;

use Illuminate\Support\Facades\Cache;


/**
 *  Para los modelos que tienen el campo 'categoria'
 *  Permite cachear un listado de todas las categorías del modelo y el nº de items para cada categoría.
 */
trait EsCategorizable
{

    /**
     * Elimina la cache de categorias cuando hay cambios en algun item
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            $model->clearCategories();
        });

        static::deleted(function ($model) {
            $model->clearCategories();
        });
    }

    /**
     * Obtiene un listado de categorías con sus contadores para este modelo
     */
    public function getCategorias($key = "", $items = null)
    {

        $cache_label = $this->getTable() . "_categorias".$key;

        $una_semana = 60 * 24 * 7; // tiempo de cache

        return Cache::remember($cache_label, $una_semana, function () use ($items) {

            $c = [];
            $items = $items?$items:$this->select('categoria')->get();

            /*      Una forma sencilla sería esta:

                    return $this->selectRaw('categoria as nombre, count(*) as total')
                    ->groupBy('categoria')
                    ->get();

                    Pero no sirve cuando las categorías son múltiples. Por eso aplicaremos el siguiente algoritmo:

                    1.- Recorrer todos los items, para cada uno separar las categorias por coma, y esas son contadas en $categorias
                    Ejemplo: si la columna categoria es 'Monografías, cuentos', pues tiene 2 categorías.
                    2.-  entonces hay que agregar en $categorias la clave 'Monografías' y el contador a 1, y la clave 'Cuentos' y el contador a 1
                    3.- si en siguienteitem es también una monografía, aumenta el contador de $categorias['Monografías']

                */

            foreach ($items as $item) {
                $categoriasItem = explode(',', $item->categoria);
                foreach ($categoriasItem as $categoria) {
                    $categoria = trim($categoria);
                    if (!empty($categoria)) {
                        if (isset($c[$categoria])) {
                            $c[$categoria]++;
                        } else {
                            $c[$categoria] = 1;
                        }
                    }
                }
            }

            ksort($c);

            $c = array_map(function ($nombre, $total) {
                return (object) ['nombre' => $nombre, 'total' => $total];
            }, array_keys($c), $c);

            return $c;
        });
    }


    /**
     * Elimina la cache de categorías
     */
    public function clearCategories()
    {

        $cache_label = $this->getTable() . "_categorias";

        Cache::forget($cache_label);
    }
}
