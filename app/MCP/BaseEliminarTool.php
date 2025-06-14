<?php

namespace App\MCP;

abstract class BaseEliminarTool extends BaseTool
{
    protected array $permisos = ['administrar_contenidos'];

    public function __construct()
    {
        parent::__construct('eliminar');
    }

    public function handle($params)
    {
        $this->checkMcpToken($params, $this->permisos);
        try {
            $id = $params['id'] ?? $params['slug'] ?? null;
            if (! $id) {
                return ['error' => 'No se ha proporcionado un ID o slug para eliminar el elemento'];
            }
            if (!$this->modelClass) {
                return ['error' => 'No se ha definido modelo'];
            }
            if (is_numeric($id)) {
                $item = ($this->modelClass)::findOrFail($id);
            } else {
                $item = ($this->modelClass)::where('slug', $id)->firstOrFail();
            }
            if (!empty($params['force']) && method_exists($item, 'forceDelete')) {
                $item->forceDelete();
                return [$this->modelNameSingle . '_borrado' => true, 'id' => $id];
            }
            $item->delete();
            return [$this->modelNameSingle . '_borrado' => true, 'id' => $id];
        } catch (\Exception $e) {
            return ['error' => 'Error al eliminar: ' . $e->getMessage()];
        }
    }
}
