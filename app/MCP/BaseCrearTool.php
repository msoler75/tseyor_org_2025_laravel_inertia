<?php

namespace App\MCP;

use Illuminate\Support\Facades\Log;

abstract class BaseCrearTool extends BaseTool
{
    protected array $permisos = ['administrar_contenidos'];

    public function __construct()
    {
        parent::__construct('crear');
    }

    // Hook para procesamiento previo
    protected function onBeforeCreate(array $data, array $params): array
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
    protected function onAfterCreate($item, array $params)
    {
        return $item;
    }

    public function handle($params)
    {
        $this->checkMcpToken($params, $this->permisos);
        if (!$this->modelClass) {
            return ['error' => 'No se ha definido modelo'];
        }
        if (!isset($params['data'])) {
            return ['error' => 'No se han proporcionado datos para actualizar el elemento'];
        }
        try {
            $data = $params['data'];
            $data = $this->onBeforeCreate($data, $params);
            $item = ($this->modelClass)::create($data);
            $item = $this->onAfterCreate($item, $params);
            return $item ? [$this->modelNameSingle . '_creado' => $item->toArray()] : [];
        } catch (\Exception $e) {
            return ['error' => 'Error al crear: ' . $e->getMessage()];
        }
    }
}
