<?php
namespace App\MCP;

abstract class BaseCamposTool extends BaseTool {
    // protected string $name;

  public function __construct()
    {
        parent::__construct('campos_' . $this->modelNameSingle);
    }

    public function handle($params = []) {
        $campos = include __DIR__ . '/campos.php';
        return [
            'fields' => $campos[$this->name] ?? []
        ];
    }

    public function description(): string {
        return 'Devuelve la información de los campos del modelo ' . ucfirst($this->name) . '.';
    }
}
