<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Carbon\Carbon;
use App\Pigmalion\ContenidoHelper;
use App\Pigmalion\StorageItem;
use App\Models\Favorito;


/*
 ContenidoBaseModel es un modelo básico que sirve para:
 - SEO
 */

class ContenidoBaseModel extends Model
{
    use HasSEO;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;


    /**
     * Casts para atributos virtuales como 'favorito' (0/1 -> boolean)
     */
    protected $casts = [
        'favorito' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            Log::info("ContenidoBaseModel::saving");
            // Acciones antes de guardar el modelo
            ContenidoHelper::rellenarSlugImagenYDescripcion($model);
        });

        static::saved(function ($model) {
            Log::info("ContenidoBaseModel::saved" . get_class($model));
            // Log::info("ContenidoBaseModel saved: ". substr($model->texto, 0, 1024));
            // si mueve alguna imagen, guardamos los cambios y salimos
            if (ContenidoHelper::moverImagenesContenido($model)) {
                $model->saveQuietly();
                Log::info("Se han movido imagenes de carpeta temp a destino para " . $model->getMorphClass() . "/" . $model->id);
            }

            // Acciones después de que el modelo se haya guardado
            ContenidoHelper::guardarContenido($model);

            if(method_exists($model, 'afterSave')) {
                $model->afterSave();
            }
        });


        static::deleted(function ($model) {
            Log::info("ContenidoBaseModel::deleted");
            ContenidoHelper::removerContenido($model);
        });


    }

    // revisionable se aplica a nuevos contenidos
    protected $revisionCreationsEnabled = true;

    // revisionable identifier
    public function identifiableName()
    {
        return $this->titulo ?? $this->nombre ?? $this->name;
    }

    /**
     * Obtiene los datos del SEO
     * https://github.com/ralphjsmit/laravel-seo
     */
    public function getDynamicSEOData(): SEOData
    {
        $image = $this->imagen ? url($this->imagen) : config('seo.image.fallback');
        return new SEOData(
            title: $this->titulo ?? $this->nombre ?? $this->name && null,
            description: $this->descripcion ?? mb_substr(strip_tags($this->texto ?? ""), 0, 400 - 3),
            image: str_replace(" ", "%20", $image),
            author: $this->autor ?? 'tseyor',
            published_time: Carbon::createFromFormat('Y-m-d H:i:s', $this->published_at ?? $this->created_at) ?? null,
            section: $this->categoria ?? ''
            // tags:
            // schema:
        );
    }


    /**
     * TNTSearch
     * Función heredable para cada modelo, sirve para indicarle al buscador global qué texto o palabras clave indexan este contenido
     * (además de los campos de título o nombre)
     */
    public function getTextoContenidoBuscador()
    {
        return null;
    }

    /**
     * @param bool ruta define si queremos el resultado en formato Ruta (relativa)
     * Carpeta temporal para medios (imágenes)
     */
    public static function getCarpetaMediosTemp(bool $formatoRutaRelativa = false) : string
    {
        $folderCompleto = '/almacen/temp';
        StorageItem::ensureDirExists($folderCompleto);
        if( $formatoRutaRelativa )
            return (new StorageItem($folderCompleto))->relativeLocation;
        return $folderCompleto;
    }

    /**
     * Carpeta para los medios del contenido (imágenes)
     */
    public function getCarpetaMedios(bool $formatoRutaRelativa= false) : string
    {
        $coleccion = $this->getTable();
        $folderCompleto = $this->id ? "/almacen/medios/$coleccion/$this->id" : self::getCarpetaMediosTemp();
        StorageItem::ensureDirExists($folderCompleto);
        if($formatoRutaRelativa)
            return (new StorageItem($folderCompleto))->relativeLocation;
        return $folderCompleto;
    }



    /**
    * Para PDF
    */

    public function getPdfFilenameAttribute()
    {
        return $this->identifiableName() . ' - TSEYOR.pdf';
    }

    public function getPdfPathAttribute()
    {
        return 'pdf/' . $this->getTable() . '/' . $this->pdf_filename;
    }

    public function generatePdf() {
        return \App\Services\PDFGenerator::generatePdf($this);
    }

    /**
     * Comprueba si este contenido está marcado como favorito por un usuario.
     *
     * @param int|null $userId Id del usuario (por defecto auth()->id())
     * @return bool
     */
    public function isFavorito($userId = null): bool
    {
        $userId = $userId ?: auth()->id();
        if (!$userId) return false;

        // Usar siempre el nombre de la tabla como 'coleccion' y la clave primaria como 'id_ref'
        $coleccion = $this->getTable();
        $id_ref = $this->getKey();

        return Favorito::where('user_id', $userId)
            ->where('coleccion', $coleccion)
            ->where('id_ref', $id_ref)
            ->exists();
    }

    /**
     * Scope para incluir el campo 'favorito' en la consulta principal para un usuario.
     * Añade un LEFT JOIN con la tabla favoritos filtrando por coleccion y user_id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithFavorito($query)
    {
        $userId = auth()->id();

        $table = $query->getModel()->getTable();
        // Si no hay columnas seleccionadas aún, añadimos <table>.* para mantener comportamiento previo
        $columns = $query->getQuery()->columns;
        if ($columns === null) {
            $query->select("$table.*");
            $columns = $query->getQuery()->columns;
        } else {
            // Si hay una selección explícita, normalizamos columnas ambiguas: 'id' -> 'tabla.id'
            $normalized = [];
            foreach ($columns as $col) {
                if (is_string($col)) {
                    // si la columna es exactamente 'id' (sin calificar) la reemplazamos
                    if ($col === 'id') {
                        $normalized[] = "$table.id";
                        continue;
                    }
                }
                $normalized[] = $col;
            }
            // reasignar columnas normalizadas
            $query->getQuery()->columns = $normalized;
            // refrescar variable
            $columns = $query->getQuery()->columns;
        }

        if (!$userId) {
            // Añadimos solo la columna fija 0 como favorito (no sobrescribimos selects existentes)
            return $query->selectRaw('0 as favorito');
        }

        // LEFT JOIN favoritos ON favoritos.id_ref = <table>.id AND favoritos.coleccion = '<table>' AND favoritos.user_id = <userId>
        $query = $query->leftJoin('favoritos', function ($join) use ($table, $userId) {
                $join->on('favoritos.id_ref', '=', "$table.id")
                     ->where('favoritos.coleccion', '=', $table)
                     ->where('favoritos.user_id', '=', $userId);
            });

        // Añadimos solo la columna calculada favorito, preservando cualquier select previo
        return $query->selectRaw('CASE WHEN favoritos.id IS NULL THEN 0 ELSE 1 END as favorito');
    }

    /**
     * Lógica común para filtrar contenido con visibilidad 'P'
     * Solo aplica el filtro si el modelo tiene el campo 'visibilidad'
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function scopeContenidoVisible($query)
    {
        $model = $query->getModel();
        $table = $model->getTable();

        // Si el modelo tiene 'visibilidad' en fillable, aplicamos el filtro
        if (in_array('visibilidad', $model->getFillable())) {
            return $query->where($table . '.visibilidad', 'P');
        }

        // Si no tiene el campo visibilidad, no aplicamos filtro
        return $query;
    }

    /**
     * Scope para filtrar contenido publicado (masculino)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublicado($query)
    {
        return $this->scopeContenidoVisible($query);
    }

    /**
     * Scope para filtrar contenido publicada (femenino)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublicada($query)
    {
        return $this->scopeContenidoVisible($query);
    }


    /**
     * Scope para filtrar contenido en borrador
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBorrador($query)
    {
        $model = $query->getModel();
        $table = $model->getTable();

        // Si el modelo tiene 'visibilidad' en fillable, aplicamos el filtro
        if (in_array('visibilidad', $model->getFillable())) {
            return $query->where($table . '.visibilidad', 'B');
        }

        // Si no tiene el campo visibilidad, no aplicamos filtro
        return $query;
    }
}
