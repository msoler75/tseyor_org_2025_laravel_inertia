<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use Illuminate\Support\Facades\Log;

class BuscarTool extends BaseTool
{

    protected ?string $controllerMethod = 'index';

    public function __construct()
    {
        parent::__construct('buscar');
    }

    public function handle($params = [])
    {
        Log::channel('mcp')->info('[MCP] Listar', ['params' => $params]);
        // determinar el modelo a partir de los parámetros
        $modelo = $params['entidad'] ?? null;
        if (!$modelo) return ['error' => 'Falta el parámetro entidad'];

        Log::channel('mcp')->info('[MCP] entidad: ' . $modelo);

        $toolsClass = $this->getModelToolsClass($modelo);
        if (!class_exists($toolsClass)) return ['error' => 'Clase no encontrada: ' . $toolsClass];

        $modelTools = new $toolsClass();
        $modelClass = $modelTools->getModelClass();
        $controller = $modelTools->getControllerClass();
        $controllerMethod = $modelTools->getMethod($this->name());
        $modelNamePlural = $modelTools->getModelNamePlural();

        if ($controller && $controllerMethod) {
            $this->inertiaRequest->query->replace($params);
            $response = $modelTools->callControllerMethod($this->name(), $this->inertiaRequest, $params);
            $data = $this->fromInertiaToArray($response);
            return $data;
        } elseif ($modelClass) {
            $query = ($modelClass)::query();
            $buscar = $params['buscar'] ?? null;
            $categoria = $params['categoria'] ?? null;
            $fillable = (new $modelClass())->getFillable();
            if ($buscar) {
                // si es "searchable"
                if (method_exists($modelClass, 'shouldBeSearchable')) {
                    $ids = $modelClass::search($buscar);
                    $query->whereIn('id', $ids);
                } else {
                    // obtener un array de los campos posibles de busqueda: titulo, nombre, descripcion, slug  desde fillable
                    $searchableFields = ['titulo', 'nombre', 'descripcion', 'slug'];
                    $foundFields = array_intersect($searchableFields, $fillable);
                    //para  cada campo, agregar una condición where
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
}
