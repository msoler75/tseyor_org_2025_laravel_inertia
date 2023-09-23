<?php


namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait EsCategorizable {

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
    public function getCategorias() {

            $cache_label = $this->getTable() . "_categorias";

            $una_semana = 60 * 24 * 7; // tiempo de cache

            $categorias = Cache::remember($cache_label, $una_semana, function ()  {

                $c = [];
                $items = $this->select('categoria')->get();
                // Recorrer todos los libros, para cada uno separar las categorias por coma, y esas son contadas en $categorias
                // ejemplo: si la categoria es 'Monografías, cuentos', pues tiene 2 categorías.
                // entonces hay que agregar en $categorias la clave 'Monografías' y el contador a 1, y la clave 'Cuentos' y el contador a 1
                // si en siguiente libro es también una monografía, aumenta el contador de $categorias['Monografías']
                //

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

                return $c;
            });

            ksort($categorias);


            $categorias = array_map(function ($nombre, $total) {
                return (object) ['nombre' => $nombre, 'total' => $total];
            }, array_keys($categorias), $categorias);

            return $categorias;
        }


        /**
         * Elimina la cache de categorías
         */
        public function clearCategories() {

                $cache_label = $this->getTable() . "_categorias";

                Cache::forget($cache_label);
        }

}
