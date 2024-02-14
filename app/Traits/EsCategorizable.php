<?php


namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 *  Para los modelos que tienen el campo 'categoria'
 *  Permite cachear un listado de todas las categorías del modelo y el nº de items para cada categoría.
 */
trait EsCategorizable
{

    // si queremos que el modelo sea de tipo simple en las categorías, hay que poner
    // protected $categoriaSimple = true;

    // si queremos agregar una categoría 'todos' o 'todas' en el modelo, hay que poner
    // protected $incluyeCategoriaTodos = "Todos";


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
    public function getCategorias($items = null)
    {
        $startTime = microtime(true);

        if ($items)
            return $this->obtenerContadoresCategoriasMultiples($items);

        $cache_label = $this->getTable() . "_categorias";

        $un_año = 60 * 24 * 365; // tiempo de cache: 1 año

        // return Cache::remember($cache_label, $un_año, function () {

            if (!$this->categoriaSimple) {
                $items = $this->select('categoria')->get();
                $c = $this->obtenerContadoresCategoriasMultiples($items);
            } else {
                $c = $this->obtenerContadoresCategoriasSimples();
            }

            if ($this->incluyeCategoriaTodos)
                array_unshift($c, ['nombre' => $this->incluyeCategoriaTodos, 'valor' => '_', 'total' => count($items)]);

//            return $c;
        // });

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        Log::info("Experiencia::getCategorias: ". $duration. " ms");


        return $c;
    }


    public function obtenerContadoresCategoriasSimples()
    {
        return $this->selectRaw('categoria as nombre, count(*) as total')
            ->groupBy('categoria')
            ->get()->toArray();
    }

    public function obtenerContadoresCategoriasMultiples($items)
    {
        /*
            Cuando las categorías son o pueden ser múltiples aplicaremos el siguiente algoritmo:

            1.- Recorrer todos los items, para cada uno separar las categorias por coma, y esas son contadas en $categorias
            Ejemplo: si la columna categoria es 'Monografías, cuentos', pues tiene 2 categorías.
            2.-  entonces hay que agregar en $categorias la clave 'Monografías' y el contador a 1, y la clave 'Cuentos' y el contador a 1
            3.- si en siguienteitem es también una monografía, aumenta el contador de $categorias['Monografías']

        */

        $c = [];
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
