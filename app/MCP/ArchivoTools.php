<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class ArchivoTools extends BaseModelTools
{
    protected ?string $modelName = 'archivo';
    protected ?string $modelClass = null; // No hay modelo Eloquent directo, se gestiona por controlador
    protected ?string $controllerClass = 'App\\Http\\Controllers\\ArchivosController';

    // Mapear métodos base a métodos del controlador
    protected array $methods = [
        'listar' => 'list', // lista una carpeta
        'buscar' => 'buscar',
        'ver' => 'descargar', // descarga el archivo
        'crear' => 'uploadFile', // subir archivo
        'editar' => 'rename', // renombrar archivo
        'eliminar' => 'delete',
    ];
}
