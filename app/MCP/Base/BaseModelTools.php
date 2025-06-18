<?php

namespace App\MCP\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        'crear, editar, eliminar' => 'administrar_todo'
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

    protected $info = []; // Información adicional sobre la herramienta, como descripción

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




    public function getInfo()
    {
        return $this->info;
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

    // HOOKS DE TOOLS ESPECIFICAS

    public function onVer(array $params, object $baseTool) {
        $modelo = $params['entidad'] ?? null;
        $modelClass = $this->getModelClass();
        $controller = $this->getControllerClass();
        $controllerMethod = $this->getMethod($baseTool->name());
        $modelNameSingle = $this->getModelNameSingle();
        $id = $params['id'] ?? $params['slug'] ?? null;
        Log::channel('mcp')->debug('[BaseVerTool] handle', ['params' => $params, 'modelo' => $modelo, 'controller' => $controller, 'modelClass' => $modelClass]);
        if ($controller && $controllerMethod) {
            if (!class_exists($controller)) return ['error' => 'Clase no encontrada: ' . $controller];
            $response = $this->callControllerMethod($baseTool->name(), $baseTool->inertiaRequest, $params);
            $data = $baseTool->fromInertiaToArray($response);
            return $data;
        } elseif ($modelClass) {
            if (is_numeric($id)) {
                $item = ($modelClass)::findOrFail($id);
            } else {
                $item = ($modelClass)::where('slug', $id)->firstOrFail();
            }
            return [$modelNameSingle => $item->toArray()];
        }
        return ['error' => 'No se ha definido controller ni modelo'];
    }

    public function onListar(array $params, object $baseTool) {
        $modelo = $params['entidad'] ?? null;
        $modelClass = $this->getModelClass();
        $controller = $this->getControllerClass();
        $controllerMethod = $this->getMethod($baseTool->name());
        $modelNamePlural = $this->getModelNamePlural();
        if ($controller && $controllerMethod) {
            if (!class_exists($controller)) return ['error' => 'Clase no encontrada: ' . $controller];
            $baseTool->inertiaRequest->query->replace($params);
            $response = $this->callControllerMethod($baseTool->name(), $baseTool->inertiaRequest, $params);
            $data = $baseTool->fromInertiaToArray($response);
            return $data;
        } elseif ($modelClass) {
            $query = ($modelClass)::query();
            $buscar = $params['buscar'] ?? null;
            $categoria = $params['categoria'] ?? null;
            $fillable = (new $modelClass())->getFillable();
            if ($buscar) {
                if (method_exists($modelClass, 'shouldBeSearchable')) {
                    $ids = $modelClass::search($buscar);
                    $query->whereIn('id', $ids);
                } else {
                    $searchableFields = ['titulo', 'nombre', 'descripcion', 'slug'];
                    $foundFields = array_intersect($searchableFields, $fillable);
                    foreach ($foundFields as $field)
                        $query->orWhere($field, 'LIKE', "%$buscar%");
                }
            }
            if ($categoria && in_array('categoria', $fillable))
                $query->where('categoria', $categoria);
            $result = $query->get();
            return [$modelNamePlural => $result->toArray()];
        }
        return ['error' => 'No se ha definido controller ni modelo'];
    }

    public function onCrear(array $params, object $baseTool) {
        $modelClass = $this->getModelClass();
        $modelNameSingle = $this->getModelNameSingle();
        $controller = $this->getControllerClass();
        $controllerMethod = null;
        try {
            $controllerMethod = $this->getMethod($baseTool->name());
        } catch (\Throwable $e) {}
        if ($controller && $controllerMethod) {
            $response = $this->callControllerMethod($baseTool->name(), $baseTool->inertiaRequest, $params);
            $result = $baseTool->fromInertiaToArray($response);
            return $result;
        }
        if (!isset($params['data'])) {
            return ['error' => 'No se han proporcionado datos para crear el elemento'];
        }
        $data = $params['data'];
        $data = $this->onBeforeCreate($data, $params);
        $item = ($modelClass)::create($data);
        $item = $this->onAfterCreate($item, $params);
        return $item ? [$modelNameSingle . '_creado' => $item->toArray()] : [];
    }

    public function onEditar(array $params, object $baseTool) {
        $modelClass = $this->getModelClass();
        $modelNameSingle = $this->getModelNameSingle();
        $controller = $this->getControllerClass();
        $controllerMethod = null;
        try {
            $controllerMethod = $this->getMethod($baseTool->name());
        } catch (\Throwable $e) {}
        $id = $params['id'] ?? $params['slug'] ?? null;
        if (!$modelClass) {
            return ['error' => 'No se ha definido modelo'];
        }
        if ($controller && $controllerMethod) {
            $response = $this->callControllerMethod($baseTool->name(), $baseTool->inertiaRequest, $params);
            $result = $baseTool->fromInertiaToArray($response);
            return $result;
        }
        if (!isset($params['data'])) {
            return ['error' => 'No se han proporcionado datos para actualizar el elemento'];
        }
        if (is_numeric($id)) {
            $item = ($modelClass)::findOrFail($id);
        } else {
            $item = ($modelClass)::where('slug', $id)->firstOrFail();
        }
        $data = $params['data'];
        $data = $this->onBeforeEdit($data, $item, $params);
        $item->update($data);
        $item = $this->onAfterEdit($item, $params);
        return [$modelNameSingle . '_modificado' => $item->toArray()];
    }

    public function onEliminar(array $params, object $baseTool) {
        $modelClass = $this->getModelClass();
        $modelNameSingle = $this->getModelNameSingle();
        $id = $params['id'] ?? $params['slug'] ?? null;
        if (!$id) {
            return ['error' => 'No se ha proporcionado un ID o slug para eliminar el elemento'];
        }
        if (!$modelClass) {
            return ['error' => 'No se ha definido modelo'];
        }
        if (is_numeric($id)) {
            $item = ($modelClass)::findOrFail($id);
        } else {
            $item = ($modelClass)::where('slug', $id)->firstOrFail();
        }
        $this->checkDeleteable($item, $params);
        if (!empty($params['force']) && method_exists($item, 'forceDelete')) {
            $item->forceDelete();
            return [$modelNameSingle . '_borrado' => true, 'id' => $id];
        }
        $item->delete();
        return [$modelNameSingle . '_borrado' => true, 'id' => $id];
    }

    // HOOKS PARA PROCESAMIENTO DE DATOS se invoca el controlador

    public function onPrepareRequest(Request $request, array $params) {
        // do nothing by default
    }

    // HOOKS PARA PROCESAMIENTO DE DATOS si no se invoca el controlador

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
        $this->onPrepareRequest($request, $params);

        $id = $params['id'] ?? $params['slug'] ?? null;

        if ($toolName == 'listar' || $toolName == 'buscar') {
            $page = $params['num_pagina'] ?? $params['page'] ?? $params['pagina'] ?? 1;
            $request->request->add(['page' => $page]);
        }

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

    public function onBuscar(array $params, object $baseTool) {
        // DRY: reutiliza la lógica de onListar
        return $this->onListar($params, $baseTool);
    }

}
