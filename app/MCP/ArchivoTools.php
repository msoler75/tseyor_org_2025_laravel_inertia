<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;
use InvalidArgumentException;

class ArchivoTools extends BaseModelTools
{
    protected ?string $modelName = 'archivo';
    protected ?string $modelClass = 'App\\Models\\Nodo';
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


    public function onBeforeCreate(array $data, array $params): array {
        // Si se pasa 'es_carpeta' o no hay 'contenido', se crea una carpeta
        $isFolder = isset($data['es_carpeta']) ? (bool)$data['es_carpeta'] : !isset($data['contenido']);
        $nodo = [
            'ubicacion' => $data['ruta'] ?? '',
            'permisos' => $data['permisos'] ?? '1755',
            'user_id' => $data['user_id'] ?? 1,
            'group_id' => $data['group_id'] ?? 1,
            'es_carpeta' => $isFolder ? 1 : 0,
            'oculto' => $data['oculto'] ?? 0,
        ];
        // Nunca incluir 'contenido' en el array resultante
        return $nodo;
    }

    public function onBeforeEdit(array $data, $item, array $params): array {
        $edit = [];
        // Ignorar 'contenido' si se recibe
        if (isset($data['nuevo_nombre'])) {
            $edit['nuevo_nombre'] = $data['nuevo_nombre'];
        }
        if (isset($data['permisos'])) {
            $edit['permisos'] = $data['permisos'];
        }
        if (isset($data['group_id'])) {
            $edit['group_id'] = $data['group_id'];
        }
        if (isset($data['user_id'])) {
            $edit['user_id'] = $data['user_id'];
        }
        if (isset($data['oculto'])) {
            $edit['oculto'] = $data['oculto'];
        }
        return $edit;
    }

    /*public function onPrepareRequest(Request $request, array $params) {

    }*/


    public function onVer(array $params, object $baseTool) {
        if(!isset($params['ruta'])) {
            throw new InvalidArgumentException('El parámetro "ruta" es obligatorio para la acción "ver".');
        }
        return parent::onVer($params, $baseTool);
    }
}
