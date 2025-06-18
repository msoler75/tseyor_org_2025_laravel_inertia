<?php

namespace App\MCP\Base;

use Illuminate\Http\Request;
abstract class BaseModelTools
{
    protected ?string $modelName = null; // Nombre singular del modelo (ej: 'comunicado')
    protected ?string $modelClass = null; // Clase del modelo Eloquent (ej: \App\Models\Comunicado)
    protected ?string $modelNamePlural = null; // Nombre plural del modelo (ej: 'comunicados')

    protected ?string $controllerClass = null; // Ejemplo: \App\Http\Controllers\EventosController
    protected array $methods = [
        'listar' => 'index',
        'buscar' => 'index',
        'ver' => 'show'
    ];

    // Formato sintético recomendado:
    protected array $required = [
        'crear, editar, eliminar' => 'administrar_todo',
    ];
    /*
    // También se aceptan estos otros formatos:
    // 1. Arrays de strings:
    // protected array $required = [
    //     'crear' => ['administrar_social', 'administrar_contenidos'],
    //     'editar' => 'administrar_contenidos',
    //     'eliminar' => 'administrar_contenidos',
    // ];
    // 2. Formato clásico (arrays vacíos para acciones públicas):
    // protected array $required = [
    //     'crear' => ['administrar_contenidos'],
    //     'editar' => ['administrar_contenidos'],
    //     'eliminar' => ['administrar_contenidos'],
    // ];
    // 3. Mezcla de sintaxis:
    // protected array $required = [
    //     'crear, editar' => 'administrar_contenidos',
    //     'eliminar' => ['administrar_contenidos', 'administrar_social'],
    // ];
    */


    public function __construct()
    {
        if (!$this->modelNamePlural)
            $this->modelNamePlural = $this->getModelNameSingle() . 's';
        if (!$this->modelClass) {
            throw new \InvalidArgumentException("La clase del modelo no está definida. Debes definir la propiedad 'modelClass' en la clase hija.");
        }
        if (!class_exists($this->modelClass)) {
            throw new \InvalidArgumentException("La clase del modelo '{$this->modelClass}' no existe.");
        }
        if ($this->controllerClass && !class_exists($this->controllerClass)) {
            throw new \InvalidArgumentException("La clase del controlador '{$this->controllerClass}' no existe.");
        }
    }

    public function getModelClass(): ?string
    {
        return $this->modelClass;
    }

    public function getControllerClass(): ?string
    {
        return $this->controllerClass;
    }

    public function getModelNameSingle(): string
    {
        return $this->modelName;
    }

    public function getModelNamePlural(): string
    {
        return $this->modelNamePlural;
    }

    public function getMethod($verb): string
    {
        if (!array_key_exists($verb, $this->methods))
            throw new \InvalidArgumentException("El verbo '$verb' no está definido en los métodos requeridos.");
        return $this->methods[$verb];
    }

    public function getRequiredPermissions($verb): array
    {
        // Si existe la clave exacta
        if (array_key_exists($verb, $this->required)) {
            return is_array($this->required[$verb]) ? $this->required[$verb] : [$this->required[$verb]];
        }
        // Buscar en claves combinadas (ej: 'crear, editar, eliminar')
        foreach ($this->required as $key => $value) {
            $acciones = array_map('trim', explode(',', $key));
            if (in_array($verb, $acciones)) {
                return is_array($value) ? $value : [$value];
            }
        }
        // Si no hay requisitos, devolver array vacío
        return [];
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


    // HOOK para llamada al método del controlador
    public function callControllerMethod(string $toolName, Request $request, array $params)
    {
        $id = $params['id'] ?? $params['slug'] ?? null;

        $page = $params['page'] ?? 1;
        $request->request->add(['page' => $page]);

        $controller = $this->getControllerClass();
        $controllerMethod = $this->getMethod($toolName);

        $reflection = new \ReflectionMethod($controller, $controllerMethod);
        $paramsReflection = $reflection->getParameters();
        if (count($paramsReflection) === 2 && $paramsReflection[0]->getType() && $paramsReflection[0]->getType()->getName() === Request::class) {
            $response = app($controller)->{$controllerMethod}($request, $id);
        } else if (count($paramsReflection) === 1 && $paramsReflection[0]->getType() && $paramsReflection[0]->getType()->getName() === Request::class) {
            // revisar si el parámetro es de tipo Request
            $response = app($controller)->{$controllerMethod}($request);
        } else {
            $response = app($controller)->{$controllerMethod}($id);
        }
        return $response;
    }
}
