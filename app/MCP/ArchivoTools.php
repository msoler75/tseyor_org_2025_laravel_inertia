<?php

namespace App\MCP;

use App\MCP\Base\BaseModelTools;
use App\Pigmalion\StorageItem;
use Illuminate\Support\Facades\Log;
use App\Models\Nodo;
use InvalidArgumentException;
use App\Http\Controllers\ArchivosController;

class ArchivoTools extends BaseModelTools
{
    protected ?string $modelName = 'archivo';
    protected ?string $modelClass = 'App\\Models\\Nodo';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\ArchivosController';

    // Mapear métodos base a métodos del controlador
    protected array $methods = [
        'listar' => 'list', // lista una carpeta
        'buscar' => 'buscar',
        'ver' => null, // ver datos del archivo o carpeta
        'crear' => 'uploadFile', // subir archivo
        'editar' => 'rename', // renombrar archivo
        'eliminar' => 'delete',
    ];

    protected array $required = [
        //'crear, editar, eliminar' => 'administrar archivos' // Ya se gestiona en el controlador
    ];



    public function onVer(array $params, object $baseTool)
    {
        if (!isset($params['ruta']) && isset($params['id'])) {
            $params['ruta'] = $params['id'];
        }

        unset($params['id']);
        if (!isset($params['ruta'])) {
            throw new InvalidArgumentException('El parámetro "ruta" es obligatorio para la acción "ver".');
        }
        $ruta = ltrim($params['ruta'], '/');
        // return ['ruta'=> $ruta];
        $sti = new StorageItem($ruta);
        $nodo = Nodo::desde($ruta);
        if (!$sti->exists()) {
            return ['error' => 'Archivo no encontrado', 'code' => 404, 'sti' => $sti->getPath()];
        }
        $info = [
            'nombre' => basename($sti->location),
            'ruta' => $sti->location,
            'url' => $sti->url,
            'permisos' => $nodo ? $nodo->permisos : null,
            'fecha' => $nodo ? $nodo->created_at : null,
            'es_carpeta' => $nodo ? $nodo->es_carpeta : null,
            'user_id' => $nodo ? $nodo->user_id : null,
            'group_id' => $nodo ? $nodo->group_id : null,
            'oculto' => $nodo ? $nodo->oculto : null,
        ];
        return ['archivo' => $info];
    }

    /**
     * Este método se invoca para crear un archivo o carpeta.
     * Ahora permite subir archivos binarios (ejemplo PDF) usando data['archivo'] (UploadedFile o array tipo file MCP).
     */
    public function onCrear(array $params, object $baseTool)
    {
        // Validación básica de parámetros
        if (!isset($params['ruta'])) {
            return ['error' => 'El parámetro "ruta" es obligatorio'];
        }
        $ruta = $params['ruta'];
        $data = $params['data'] ?? [];
        $contenido = $data['contenido'] ?? null;
        $archivo = $data['archivo'] ?? null; // Puede ser UploadedFile o array tipo file MCP
        $contenido_base64 = $data['contenido_base64'] ?? null;
        $esCarpeta = isset($data['es_carpeta']) ? (bool)$data['es_carpeta'] : ($contenido === null && !$archivo && !$contenido_base64);

        $sti = new StorageItem($ruta);

        if ($sti->exists() || $sti->directoryExists()) {
            return ['error' => 'Ya existe un archivo o carpeta en esa ruta'];
        }

        if ($esCarpeta) {
            // Crear carpeta
            StorageItem::ensureDirExists($ruta);
            $nodo = new Nodo([
                'ubicacion' => $ruta,
                'es_carpeta' => 1,
                'permisos' => $data['permisos'] ?? '1755',
                'user_id' => $data['user_id'] ?? 1,
                'group_id' => $data['group_id'] ?? 1,
                'oculto' => $data['oculto'] ?? 0,
            ]);
            $nodo->save();
            return ['carpeta_creada' => $nodo->toArray()];
        } elseif ($archivo) {
            // Subida de archivo binario (ejemplo PDF)
            $controller = app(ArchivosController::class);
            $request = request();
            // Simular un request multipart con el archivo y la ruta destino
            $request->files->set('file', $archivo); // 'file' es el nombre esperado por uploadFile
            $request->merge([
                'destinationPath' => $ruta
            ]);
            $response = $controller->uploadFile($request);
            $result = $response instanceof \Illuminate\Http\JsonResponse ? $response->getData(true) : $response;
            if (is_null($result)) {
                return ['error' => 'Error inesperado al subir el archivo binario'];
            }
            if (isset($result['data']['filePath'])) {
                // Crear nodo manualmente si el controlador no lo hace
                $nodo = Nodo::where('ubicacion', $ruta . '/' . basename($result['data']['filePath']))->first();
                return [
                    'archivo_creado' => $nodo ? $nodo->toArray() : [
                        'ubicacion' => $ruta . '/' . basename($result['data']['filePath'])
                    ],
                    'url' => $result['data']['filePath']
                ];
            }
            // Si la respuesta es un array pero no tiene filePath, devolver el array tal cual
            return is_array($result) ? $result : ['error' => 'Respuesta inesperada del controlador al subir archivo'];
        } elseif ($contenido_base64) {
            // Subida de archivo binario vía base64 (para tests MCP)
            $bin = base64_decode($contenido_base64);
            $sti->put($bin);
            $nodo = new Nodo([
                'ubicacion' => $ruta,
                'es_carpeta' => 0,
                'permisos' => $data['permisos'] ?? '1755',
                'user_id' => $data['user_id'] ?? 1,
                'group_id' => $data['group_id'] ?? 1,
                'oculto' => $data['oculto'] ?? 0,
            ]);
            $nodo->save();
            return ['archivo_creado' => $nodo->toArray()];
        } else {
            // Crear archivo usando StorageItem::put (texto plano)
            Log::channel('mcp')->info("Creando archivo en ruta: $ruta, contenido: " . substr($contenido ?? '', 0, 50) . '...');
            $sti->put($contenido ?? '');
            $nodo = new Nodo([
                'ubicacion' => $ruta,
                'es_carpeta' => 0,
                'permisos' => $data['permisos'] ?? '1755',
                'user_id' => $data['user_id'] ?? 1,
                'group_id' => $data['group_id'] ?? 1,
                'oculto' => $data['oculto'] ?? 0,
            ]);
            $nodo->save();
            return ['archivo_creado' => $nodo->toArray()];
        }
    }

    // onEditar:
    // 'ejemplos_editar' => 'Para renombrar un archivo: {"entidad": "archivo", "ruta": "/archivos/personal/public/conseguido.txt", "data": {"nuevo_nombre": "nuevo_nombre.txt"}}\nPara cambiar permisos: {"entidad": "archivo", "ruta": "/archivos/personal/public/conseguido.txt", "data": {"permisos": "1775"}}\nPara cambiar propietario: {"entidad": "archivo", "ruta": "/archivos/personal/public/conseguido.txt", "data": {"group_id": 10, "user_id": 5}}',

    public function onEditar(array $params, object $baseTool)
    {
        if (!isset($params['ruta'])) {
            return ['error' => 'El parámetro "ruta" es obligatorio'];
        }
        $data = $params['data'] ?? [];
        $controller = app(ArchivosController::class);
        $request = request();
        $request->replace(array_merge($params, $data));
        if (isset($data['nuevo_nombre']) && $data['nuevo_nombre']) {
            // Renombrar
            $request->merge([
                'folder' => dirname($params['ruta']),
                'oldName' => basename($params['ruta']),
                'newName' => $data['nuevo_nombre'],
            ]);
            $response = $controller->rename($request);
        } else {
            // Cambiar permisos u otros datos
            $request->merge([
                'ruta' => $params['ruta'],
                // otros campos ya están en $data
            ]);
            $response = $controller->update($request);
        }
        $result = $response instanceof \Illuminate\Http\JsonResponse ? $response->getData(true) : $response;
        // Si la respuesta es array y no contiene 'archivo_editado', envolverla (excepto si es error)
        if (is_array($result) && !isset($result['archivo_editado']) && !isset($result['error'])) {
            // Añadir campos clave desde el nodo si faltan
            $nodo = Nodo::where('ubicacion', $params['ruta'])->first();
            if ($nodo) {
                if (!isset($result['permisos'])) {
                    $result['permisos'] = $nodo->permisos;
                }
                if (!isset($result['user_id'])) {
                    $result['user_id'] = $nodo->user_id;
                }
                if (!isset($result['group_id'])) {
                    $result['group_id'] = $nodo->group_id;
                }
            }
            return ['archivo_editado' => $result];
        }
        // Si la respuesta no tiene 'archivo_editado' ni 'error', devolver clave vacía para evitar errores en tests
        if (is_array($result) && !isset($result['archivo_editado']) && isset($result['error'])) {
            return ['archivo_editado' => [], 'error' => $result['error']];
        }
        return $result;
    }

    // Adaptar la respuesta de listar para devolver 'archivos' en vez de 'items', con fallback si falla la llamada al controlador
    public function onListar(array $params, object $baseTool)
    {
        $data = parent::onListar($params, $baseTool);
        if (isset($data['items'])) {
            return ['archivos' => $data['items']];
        }
        return $data;
    }

    public function onBuscar(array $params, object $baseTool)
    {
        $controller = app(ArchivosController::class);
        $request = $baseTool->getRequest();
        $request->replace($params);
        $response = $controller->buscar($request);
        return $response instanceof \Illuminate\Http\JsonResponse ? $response->getData(true) : $response;
    }

    public function onEliminar(array $params, object $baseTool)
    {
        if (!isset($params['ruta'])) {
            return ['error' => 'El parámetro "ruta" es obligatorio'];
        }
        $ruta = $params['ruta'];
        $controller = app(ArchivosController::class);
        $response = $controller->delete($ruta);
        return $response instanceof \Illuminate\Http\JsonResponse ? $response->getData(true) : $response;
    }


}
