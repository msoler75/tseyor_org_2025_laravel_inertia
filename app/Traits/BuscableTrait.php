<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Pigmalion\BusquedasHelper;

/**
 * Trait para añadir funcionalidad de búsqueda a modelos.
 * Incluye scopes Buscar y Buscar2 que utilizan TNTSearch para búsquedas avanzadas.
 */
trait BuscableTrait
{
    /**
     * Scope simple para buscar contenido ordenado por relevancia de TNTSearch (sin scores)
     */
    public function scopeBuscar($query, $buscar)
    {
        if (!$buscar) return $query;

        if(!method_exists($this, 'toSearchableArray') || !method_exists($this, 'shouldBeSearchable')) {
            // si el modelo no es searchable, hacer búsqueda LIKE en campos comunes
            $fields = array_intersect(['titulo', 'nombre', 'descripcion', 'texto'], $this->getFillable());
            BusquedasHelper::buscarQueryFields($buscar, $query, $fields);
            return $query;
        }

        if(empty($this->toSearchableArray()))
            Log::error(get_class($this) ." debe rellenar el método toSearchableArray() para que TNTSearch funcione correctamente.");

        $busqueda = BusquedasHelper::buscarIdsOrdenadosPorScore($buscar, $this::class);
        if (empty($busqueda['ids'])) return $query->whereRaw('1=0'); // si no hay resultados, devolver query vacía

        $table = $this->getTable();

        // truco de FIND_IN_SET para que se considere el orden de los Ids devueltos por TNTSearch
        return $query->whereIn($table . '.id', $busqueda['ids'])
                     ->orderByRaw("FIND_IN_SET($table.id, '" . implode(',', $busqueda['ids']) . "')");
    }

    /**
     * Scope para buscar cualquier contenido ordenado por relevancia de TNTSearch (con scores)
     */
    public function scopeBuscarConScores($query, $buscar)
    {
        if (!$buscar) return $query;

        if(!method_exists($this, 'toSearchableArray') || !method_exists($this, 'shouldBeSearchable')) {
            // si el modelo no es searchable, hacer búsqueda LIKE en campos comunes
            $fields = array_intersect(['titulo', 'nombre', 'descripcion', 'texto'], $this->getFillable());
            BusquedasHelper::buscarQueryFields($buscar, $query, $fields);
            return $query;
        }

        if(empty($this->toSearchableArray()))
            Log::error(get_class($this) ." debe rellenar el método toSearchableArray() para que TNTSearch funcione correctamente.");

        $busqueda = BusquedasHelper::buscarIdsOrdenadosPorScore($buscar, $this::class);
        if (empty($busqueda['ids'])) return $query->whereRaw('1=0'); // si no hay resultados, devolver query vacía

        $table = $this->getTable();
        $case = 'CASE ' . $table . '.id ';
        foreach ($busqueda['scores'] as $id => $score) {
            $case .= 'WHEN ' . $id . ' THEN ' . $score . ' ';
        }
        $case .= 'ELSE 0 END as __tntSearchScore__';

        // truco de FIND_IN_SET para que se considere el orden de los Ids devueltos por TNTSearch
        return $query->whereIn($table . '.id', $busqueda['ids'])
                     ->orderByRaw("FIND_IN_SET($table.id, '" . implode(',', $busqueda['ids']) . "')")
                     ->addSelect(DB::raw($case));
    }
}
