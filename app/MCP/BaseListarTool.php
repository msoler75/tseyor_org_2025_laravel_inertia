<?php
namespace App\MCP;

abstract class BaseListarTool extends BaseTool {

    protected ?string $controllerMethod = 'index';

    public function __construct() {
        parent::__construct('listar', true);
    }

    public function handle($params = []) {
        try {
            if ($this->controller) {
                $this->inertiaRequest->query->replace($params);
                $response = app($this->controller)->{$this->controllerMethod}($this->inertiaRequest);
                $data = $this->fromInertiaToArray($response);
                return $data;
            } elseif ($this->modelClass) {
                $query = ($this->modelClass)::query();
                if (!empty($params['buscar'])) {
                    $buscar = $params['buscar'];
                    $query->where(function($q) use ($buscar) {
                        $q->where('titulo', 'like', "%$buscar%")
                          ->orWhere('descripcion', 'like', "%$buscar%") ;
                    });
                }
                if (!empty($params['categoria'])) {
                    $query->where('categoria', $params['categoria']);
                }
                $result = $query->get();
                return [$this->modelNamePlural => $result->toArray()];
            }
            return ['error' => 'No se ha definido controller ni modelo'];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
