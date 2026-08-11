<?php

namespace App\MCP\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Pigmalion\BusquedasHelper;

abstract class BaseModelTools
{
    protected ?string $modelName = null; // Nombre singular del modelo (ej: 'comunicado')
    protected ?string $modelClass = null; // Clase del modelo Eloquent (ej: \App\Models\Comunicado)
    protected ?string $modelNamePlural = null; // Nombre plural del modelo (ej: 'comunicados')

    protected ?string $controllerClass = null; // Ejemplo: \App\Http\Controllers\EventosController
    protected array $methods = [
        'listar' => ['method' => 'index', 'args' => ['request']],
        'buscar' => ['method' => 'index', 'args' => ['request']],
        'ver' => ['method' => 'show', 'args' => ['request', 'id']],
    ];

    // Formato recomendado:
    // Para requerir un permiso en varias acciones, usa:
    // protected array $required = [
    //     'crear, editar, eliminar' => 'administrar contenidos'
    // ];
    // O para una sola acción:
    // protected array $required = [
    //     'eliminar' => 'administrar contenidos'
    // ];
    // Si no se requiere permiso especial, puedes dejarlo vacío o no definirlo.
    protected array $required = [
        'crear, editar, eliminar' => 'administrar contenidos'
    ];

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
        $def = $this->methods[$verb];
        return is_array($def) ? $def['method'] : $def;
    }

    public function getMethodArgs($verb): array
    {
        if (!array_key_exists($verb, $this->methods))
            return ['request'];
        $def = $this->methods[$verb];
        return is_array($def) ? ($def['args'] ?? ['request']) : ['request'];
    }

    public function getRequiredPermissions($verb): ?string
    {
        // Si existe la clave exacta
        if (array_key_exists($verb, $this->required)) {
            return $this->required[$verb] ?: null;
        }
        // Buscar en claves combinadas (ej: 'crear, editar, eliminar')
        foreach ($this->required as $key => $value) {
            $acciones = array_map('trim', explode(',', $key));
            if (in_array($verb, $acciones)) {
                return $value ?: null;
            }
        }
        // Si no hay requisitos, devolver null
        return null;
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
            $response = $this->callControllerMethod($baseTool->name(), $baseTool->getRequest(), $params);
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
            $request = $baseTool->getRequest();
            $request->query->replace($params);
            $response = $this->callControllerMethod($baseTool->name(), $request, $params);
            $data = $baseTool->fromInertiaToArray($response);
            return $data;
        } elseif ($modelClass) {
            $query = ($modelClass)::query();
            $buscar = $params['buscar'] ?? null;
            $categoria = $params['categoria'] ?? null;
            $fillable = (new $modelClass())->getFillable();
            if ($buscar) {
                if (method_exists($modelClass, 'buscar')) {
                    $query->buscar($buscar);
                } else {
                    $searchableFields = ['titulo', 'nombre', 'descripcion', 'slug'];
                    $foundFields = array_intersect($searchableFields, $fillable);
                    BusquedasHelper::buscarQueryFields($buscar, $query, $foundFields);
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
            $response = $this->callControllerMethod($baseTool->name(), $baseTool->getRequest(), $params);
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
            $response = $this->callControllerMethod($baseTool->name(), $baseTool->getRequest(), $params);
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

        if ($toolName == 'buscar') {
            $page = $params['num_pagina'] ?? $params['page'] ?? $params['pagina'] ?? 1;
            $request->request->add(['page' => $page]);
        }

        $controller = $this->getControllerClass();
        $controllerMethod = $this->getMethod($toolName);

        $resolvedArgs = $this->resolveControllerArgs($controller, $controllerMethod, $request, $id, $params);

        return app($controller)->{$controllerMethod}(...$resolvedArgs);
    }

    /**
     * Resuelve los argumentos para llamar al método del controlador según su
     * firma real (Reflection), en lugar de un mapeo posicional ciego.
     *
     * Esto evita que un método como show($id) reciba el Request como primer
     * argumento cuando la definición de $methods declara ['request', 'id'].
     */
    protected function resolveControllerArgs(string $controller, string $method, Request $request, $id, array $params): array
    {
        $reflection = new \ReflectionMethod($controller, $method);
        $args = [];

        foreach ($reflection->getParameters() as $parameter) {
            $type = $parameter->getType();
            $name = $parameter->getName();

            // Parámetro tipado como Request (o subtipo) → inyectar el request
            if ($type && !$type->isBuiltin() && is_a($type->getName(), Request::class, true)) {
                $args[] = $request;
                continue;
            }

            // Parámetro booleano 'json' → true
            if ($name === 'json') {
                $args[] = true;
                continue;
            }

            // Parámetro de ruta ('ruta', 'rutaReq', etc.) → la ruta del params (o cadena vacía)
            if (str_starts_with($name, 'ruta')) {
                $args[] = $params['ruta'] ?? '';
                continue;
            }

            // Parámetro de id (id, idEquipo, slug, etc.) → el id o slug del params
            if ($name === 'id' || $name === 'slug' || str_ends_with($name, 'Id')) {
                $args[] = $id;
                continue;
            }

            // Fallback: usar el valor por defecto del parámetro si es opcional,
            // o el id en caso contrario
            $args[] = $parameter->isOptional() ? $parameter->getDefaultValue() : $id;
        }

        return $args;
    }

    public function onBuscar(array $params, object $baseTool) {
        // DRY: reutiliza la lógica de onListar
        return $this->onListar($params, $baseTool);
    }

}
