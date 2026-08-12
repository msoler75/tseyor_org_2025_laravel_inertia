<?php

namespace App\Traits;

use App\Pigmalion\StorageItem;

/**
 * Trait compartido para modelos de comunicados que manejan audios y medios.
 * Usado por Comunicado y ComunicadoInteriorizacion.
 */
trait ComunicadoMediaTrait
{
    /**
     * Genera el nombre del archivo de audio basado en la fecha y número.
     * Sobrescribir en el modelo si se necesita un formato diferente.
     */
    public function generarNombreAudio($index)
    {
        $fecha = date('ymd', strtotime($this->fecha_comunicado));
        $audios = $this->audios;
        $multiple = count($audios) > 1;
        $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        $suffix = $letters[$index] ?? '';
        $name = "TSEYOR $fecha ({$this->numero})";

        if ($multiple) {
            $name .= ' '.$suffix;
        }

        return $name.'.mp3';
    }

    /**
     * Carpeta para los medios del contenido (imágenes).
     * Formato: /almacen/medios/{tabla}/{ano}/{id}
     */
    public function getCarpetaMedios(bool $formatoRutaRelativa = false): string
    {
        $coleccion = $this->getTable();
        $folderCompleto = $this->id
            ? "/almacen/medios/$coleccion/{$this->ano}/{$this->id}"
            : self::getCarpetaMediosTemp();
        StorageItem::ensureDirExists($folderCompleto);

        if ($formatoRutaRelativa) {
            return (new StorageItem($folderCompleto))->relativeLocation;
        }

        return $folderCompleto;
    }

    /**
     * Carpeta temporal para medios antes de tener ID.
     */
    public static function getCarpetaMediosTemp(bool $formatoRutaRelativa = false): string
    {
        $folderCompleto = '/almacen/temp';
        StorageItem::ensureDirExists($folderCompleto);

        if ($formatoRutaRelativa) {
            return (new StorageItem($folderCompleto))->relativeLocation;
        }

        return $folderCompleto;
    }
}
