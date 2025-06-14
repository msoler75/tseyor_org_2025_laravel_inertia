<?php

namespace App\MCP;

abstract class BaseEditarTool extends BaseTool
{

    protected array $permisos = ['administrar_contenidos'];

    public function __construct()
    {
        parent::__construct('editar');
    }

    // Hook para procesamiento previo
    protected function onBeforeEdit(array $data, $item, array $params): array
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
    protected function onAfterEdit($item, array $params)
    {
        return $item;
    }

    public function handle($params)
    {
        try {
            $id = $params['id'] ?? $params['slug'] ?? null;
            if (!$this->modelClass) {
                return ['error' => 'No se ha definido modelo'];
            }
            if (!isset($params['data'])) {
                return ['error' => 'No se han proporcionado datos para actualizar el elemento'];
            }
            if (is_numeric($id)) {
                $item = ($this->modelClass)::findOrFail($id);
            } else {
                $item = ($this->modelClass)::where('slug', $id)->firstOrFail();
            }
            $data = $params['data'];
            $data = $this->onBeforeEdit($data, $item, $params);
            $item->update($data);
            $item = $this->onAfterEdit($item, $params);
            return [$this->modelNameSingle . '_modificado' => $item->toArray()];
        } catch (\Exception $e) {
            return ['error' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }
}
