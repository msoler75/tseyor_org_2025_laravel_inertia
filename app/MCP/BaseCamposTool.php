<?php

namespace App\MCP;

abstract class BaseCamposTool extends BaseTool
{
    // protected string $name;

    public function __construct()
    {
        parent::__construct('campos');
    }

    public function handle($params = [])
    {
        $campos = include __DIR__ . '/campos.php';
        return [
            'fields' => $campos[$this->modelNameSingle] ?? []
        ];
    }

    public function description(): string
    {
        return 'Devuelve la información de los campos del modelo ' . ucfirst($this->modelNameSingle) . '.';
    }
}
