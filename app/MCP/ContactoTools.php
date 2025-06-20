<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class ContactoTools extends BaseModelTools
{
    protected ?string $modelName = 'contacto';
    protected ?string $modelClass = 'App\\Models\\Contacto';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\ContactosController';
    protected array $required = [
        'crear, editar, eliminar' => 'administrar directorio'
    ];
}
