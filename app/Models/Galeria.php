<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use App\Models\Nodo;
use App\Models\GaleriaItem;
use Laravel\Scout\Searchable;
use App\Pigmalion\StorageItem;

class Galeria extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;

    protected $fillable = ['titulo', 'descripcion', 'ruta', 'imagen'];

    public function items()
    {
        return $this->hasMany(GaleriaItem::class);
    }

    /**
     * Obtener la imagen principal de la galería (primera imagen de los items)
     */
    public function getImagenPrincipalAttribute()
    {
        if ($this->imagen) {
            return $this->imagen;
        }

        $primerItem = $this->items()->whereHas('nodo', function($query) {
            $query->where('es_carpeta', 0);
        })->first();

        return $primerItem ? $primerItem->nodo->ubicacion : null;
    }

    public function releerCarpeta()
    {
        $path = $this->ruta;
        if (!$path) return;

        // Primero, indexar archivos físicos que no estén en la base de datos
        $this->indexarArchivosFisicos($path);

        // Luego buscar nodos en la base de datos
        $nodos = Nodo::where('ubicacion', 'like', $path . '/%')
                     ->where('es_carpeta', 0)
                     ->get();

        // Obtener el orden máximo actual para añadir nuevos al final
        $maxOrden = $this->items()->max('orden') ?? 0;
        $orden = $maxOrden + 1;
        foreach ($nodos as $nodo) {
            $existing = GaleriaItem::where('galeria_id', $this->id)
                                   ->where('nodo_id', $nodo->id)
                                   ->first();
            if (!$existing) {
                GaleriaItem::create([
                    'galeria_id' => $this->id,
                    'nodo_id' => $nodo->id,
                    'titulo' => pathinfo($nodo->ubicacion, PATHINFO_FILENAME),
                    'descripcion' => '',
                    'user_id' => null,
                    'orden' => $orden++,
                ]);
            }
        }

        // Asignar ordenes a items existentes que no los tengan
        $itemsSinOrden = $this->items()->whereNull('orden')->get();
        $ordenActual = $this->items()->whereNotNull('orden')->max('orden') ?? 0;
        foreach ($itemsSinOrden as $item) {
            $ordenActual++;
            $item->update(['orden' => $ordenActual]);
        }
    }

    private function indexarArchivosFisicos($basePath)
    {
        $fullPath = storage_path('app/' . $basePath);

        if (!is_dir($fullPath)) {
            return;
        }

        $files = $this->getFilesRecursively($fullPath, $basePath);

        foreach ($files as $file) {
            // Verificar si ya existe un nodo para este archivo
            $existing = Nodo::where('ubicacion', $file['ubicacion'])->first();
            if (!$existing) {
                Nodo::create([
                    'ubicacion' => $file['ubicacion'],
                    'permisos' => '0644',
                    'user_id' => 1,
                    'group_id' => 1,
                    'es_carpeta' => 0,
                    'oculto' => 0,
                ]);
            }
        }
    }

    private function getFilesRecursively($directory, $basePath, &$files = [])
    {
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $directory . DIRECTORY_SEPARATOR . $item;
            $relativePath = $basePath . '/' . $item;

            if (is_dir($fullPath)) {
                // Es una carpeta, procesar recursivamente
                $this->getFilesRecursively($fullPath, $relativePath, $files);
            } else {
                // Es un archivo
                $files[] = [
                    'ubicacion' => $relativePath,
                    'full_path' => $fullPath,
                ];
            }
        }

        return $files;
    }
}
