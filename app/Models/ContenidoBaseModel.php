<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Carbon\Carbon;
// use App\Traits\EsContenido;
use App\Pigmalion\ContenidoHelper;
use Illuminate\Support\Facades\Storage;
use App\Pigmalion\DiskUtil;


/*
 ContenidoBaseModel es un modelo básico que sirve para:
 - SEO
 - Gestionar una base para los contenidos con el trait EsContenido
 */

class ContenidoBaseModel extends Model
{
    use HasSEO;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            Log::info("ContenidoBaseModel::saving");
            // Acciones antes de guardar el modelo
            ContenidoHelper::rellenarSlugImagenYDescripcion($model);
        });

        static::saved(function ($model) {
            Log::info("ContenidoBaseModel::saved");
            // Log::info("ContenidoBaseModel saved: ". substr($model->texto, 0, 1024));
            // si mueve alguna imagen, guardamos los cambios y salimos
            if (ContenidoHelper::moverImagenesContenido($model)) {
                $model->save();
                Log::info("Se han movido imagenes de carpeta temp a destino para " . $model->getMorphClass() . "/" . $model->id);
                return;
            }

            // Acciones después de que el modelo se haya guardado
            ContenidoHelper::guardarContenido($model->getTable(), $model);

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
            image: $image,
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
        DiskUtil::ensureDirExists($folderCompleto);
        if( $formatoRutaRelativa )
            return DiskUtil::getRutaRelativa($folderCompleto);
        return $folderCompleto;
    }

    /**
     * Carpeta para los medios del contenido (imágenes)
     */
    public function getCarpetaMedios(bool $formatoRutaRelativa= false) : string
    {
        $coleccion = $this->getTable();
        $folderCompleto = $this->id ? "/almacen/medios/$coleccion/$this->id" : self::getCarpetaMediosTemp();
        DiskUtil::ensureDirExists($folderCompleto);
        if($formatoRutaRelativa)
            return DiskUtil::getRutaRelativa($folderCompleto);
        return $folderCompleto;
    }


    /**
    * Para PDF
    */

    public function getPdfFilenameAttribute()
    {
        return $this->titulo . ' - TSEYOR.pdf';
    }

    public function getPdfPathAttribute()
    {
        return 'pdf/' . $this->getTable() . '/' . $this->pdf_filename;
    }

    public function generatePdf() {
        return \App\Services\PDFGenerator::generatePdf($this);
    }
}
