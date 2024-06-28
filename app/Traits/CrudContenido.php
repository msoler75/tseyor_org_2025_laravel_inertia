<?php

namespace App\Traits;

use App\Models\ContenidoBaseModel;

/**
 *  Para CRUD de backpack
 */
trait CrudContenido
{
    public function getMediaFolder($model = null) : string
    {
        if($model!=null)
            return $model->getCarpetaMedios();

        // Verificar si se está editando un registro existente
        $entry = $this->crud->getCurrentEntry();
        if ($entry) {
            // Se está editando un registro existente
            return $entry->getCarpetaMedios();
        } else {
            // Se está creando un nuevo registro
            return ContenidoBaseModel::getCarpetaMediosTemp();
        }
    }


    public function getMediaTempFolder() : string
    {
        return ContenidoBaseModel::getCarpetaMediosTemp();
    }

}
