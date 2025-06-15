<?php
namespace App\MCP\Base;


abstract class BaseModelTools
{
    protected ?string $modelName = null; // Nombre singular del modelo (ej: 'comunicado')
    protected ?string $modelClass = null; // Clase del modelo Eloquent (ej: \App\Models\Comunicado)
    protected ?string $modelNamePlural = null; // Nombre plural del modelo (ej: 'comunicados')

    protected ?string $controllerClass = null; // Ejemplo: \App\Http\Controllers\EventosController
    protected array $methods = [
        'listar'=> 'index',
        'ver' => 'show'
    ];

    protected array $required = [
        'campos'=> [],
        'ver'=> [],
        'listar'=> [],
        'crear'=> ['administrar_contenidos'],
        'editar'=> ['administrar_contenidos'],
        'eliminar'=> ['administrar_contenidos'],
    ];


    public function __construct() {
        if(!$this->modelNamePlural)
            $this->modelNamePlural = $this->getModelNameSingle() . 's';
        if(!$this->modelClass) {
            throw new \InvalidArgumentException("La clase del modelo no está definida. Debes definir la propiedad 'modelClass' en la clase hija.");
        }
        if(!class_exists($this->modelClass)) {
            throw new \InvalidArgumentException("La clase del modelo '{$this->modelClass}' no existe.");
        }
        if($this->controllerClass && !class_exists($this->controllerClass)) {
            throw new \InvalidArgumentException("La clase del controlador '{$this->controllerClass}' no existe.");
        }
    }

    public function getModelClass(): ?string {
        return $this->modelClass;
    }

    public function getControllerClass(): ?string {
        return $this->controllerClass;
    }

    public function getModelNameSingle(): string {
        return $this->modelName;
    }

     public function getModelNamePlural(): string {
        return $this->modelNamePlural;
    }

    public function getMethod($verb): string {
        if(!array_key_exists($verb, $this->methods))
            throw new \InvalidArgumentException("El verbo '$verb' no está definido en los métodos requeridos.");
        return $this->methods[$verb];
    }

    public function getRequiredPermissions($verb): array {
        if(!array_key_exists($verb, $this->required))
            throw new \InvalidArgumentException("El verbo '$verb' no está definido en los requisitos.");
        return $this->required[$verb];
    }


      // Hook para procesamiento previo
    public function onBeforeCreate(array $data, array $params): array
    {
        if (isset($data['texto'])) {
            $carpeta = (new $this->modelClass())->getCarpetaMedios();
            if ($carpeta) {
                $data['texto'] = \App\Pigmalion\Markdown::extraerImagenes($data['texto'], $carpeta);
            }
        }
        return $data;
    }

    // Hook para procesamiento posterior
    public function onAfterCreate($item, array $params)
    {
        return $item;
    }

     // Hook para procesamiento previo
    public function onBeforeEdit(array $data, $item, array $params): array
    {
        if (isset($data['texto'])) {
            $carpeta = $item->getCarpetaMedios();
            if ($carpeta) {
                $data['texto'] = \App\Pigmalion\Markdown::extraerImagenes($data['texto'], $carpeta);
            }
        }
        return $data;
    }
    // Hook para procesamiento posterior
    public function onAfterEdit($item, array $params)
    {
        return $item;
    }

    /**
     * Hook para validación previa al borrado.
     * Lanza excepción o retorna false si no se puede borrar.
     * Por defecto permite el borrado.
     * Si no se puede borrar, se debe lanzar una excepción
     */
    public function checkDeleteable($item, array $params = [])
    {
        // Por defecto, permitir borrado
    }


}
