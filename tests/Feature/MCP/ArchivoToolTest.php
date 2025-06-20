<?php

namespace Tests\Feature\MCP;

use Tests\Feature\MCP\McpFeatureTestCase;
use Illuminate\Support\Facades\Storage;
use App\Pigmalion\StorageItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArchivoToolTest extends McpFeatureTestCase
{
    // public function setUp(): void
    // {
    //     parent::setUp();
    //     // Aquí podrías preparar archivos de prueba en el storage si es necesario
    // }

    public function test_listar_archivos()
    {
        $carpeta = '/archivos/listarprueba';
        $archivo1 = $carpeta . '/a.txt';
        $archivo2 = $carpeta . '/b.txt';
        $this->limpiarArchivosOCarpetas([$archivo1, $archivo2, $carpeta]);
        StorageItem::ensureDirExists($carpeta);
        (new StorageItem($archivo1))->put('uno');
        (new StorageItem($archivo2))->put('dos');
        $result = $this->callMcpTool('listar', ['entidad' => 'archivo', 'ruta' => $carpeta]);
        // fwrite(STDERR, json_encode($result, JSON_PRETTY_PRINT));
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('archivos', $result);
        $archivos = array_column($result['archivos'], 'ruta');
        // Normalizar rutas quitando barra inicial
        $archivosNorm = array_map(function($r) { return ltrim($r, '/'); }, $archivos);
        $archivo1Norm = ltrim($archivo1, '/');
        $archivo2Norm = ltrim($archivo2, '/');
        $this->assertContains($archivo1Norm, $archivosNorm, 'No se listó el archivo 1');
        $this->assertContains($archivo2Norm, $archivosNorm, 'No se listó el archivo 2');
    }

    public function test_info_archivo() {
        $ruta = '/archivos/infofile.txt';
        $this->limpiarArchivosOCarpetas([$ruta]);
        (new StorageItem($ruta))->put('info');
        $result = $this->callMcpTool('info', ['entidad' => 'archivo', 'id' => $ruta]);
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
    }

    public function test_ver_archivo()
    {
        $ruta = '/archivos/testfile.txt';
        $this->limpiarArchivosOCarpetas([$ruta]);
        (new StorageItem($ruta))->put('Contenido de prueba');
        $result = $this->callMcpTool('ver', ['entidad' => 'archivo', 'id' => $ruta]);
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('archivo', $result);
        $this->assertArrayHasKey('ruta', $result['archivo']);
        $this->assertArrayHasKey('permisos', $result['archivo']);
    }

    public function test_buscar_archivo()
    {
        $ruta = '/archivos/buscar.txt';
        $this->limpiarArchivosOCarpetas([$ruta]);
        // Eliminar el nodo si existe antes de crearlo
        \App\Models\Nodo::where('ubicacion', $ruta)->forceDelete();
        // Eliminar todos los nodos que tengan esa ruta (exacta o como prefijo)
        \App\Models\Nodo::withTrashed()
            ->where('ubicacion', $ruta)
            ->orWhere('ubicacion', 'like', $ruta . '/%')
            ->forceDelete();
        // Limpiar también todos los archivos y nodos de la carpeta 'archivos' para evitar residuos
        \App\Models\Nodo::withTrashed()
            ->where('ubicacion', 'like', '/archivos/%')
            ->orWhere('ubicacion', 'like', 'archivos/%')
            ->forceDelete();
        (new StorageItem($ruta))->put('conseguido');
        // Crear el nodo correspondiente para que la búsqueda lo encuentre, con permisos y grupo del usuario anónimo
        $anon = \App\Models\User::where('email', 'anonimo@tseyor.org')->first();
        \App\Models\Nodo::updateOrCreate(
            ['ubicacion' => $ruta],
            [
                'user_id' => $anon ? $anon->id : 1,
                'group_id' => 1,
                'permisos' => '0777',
                'es_carpeta' => 0,
                'oculto' => 0,
            ]
        );
        sleep(1); // Espera para asegurar indexación y sincronización
        $result = $this->callMcpTool('buscar', ['entidad' => 'archivo', 'nombre' => 'buscar.txt']);
        // DEBUG: Escribir la respuesta en un archivo temporal
        //fwrite(STDERR,  json_encode($result, JSON_PRETTY_PRINT));
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('resultados', $result);
        // Normalizar rutas para la comparación
        $archivos = array_map(function($r) { return '/' . ltrim($r, '/'); }, array_column($result['resultados'], 'ruta'));
        $this->assertContains($ruta, $archivos, 'No se encontró el archivo buscado');
    }

    public function test_crear_y_eliminar_archivo() {
        $ruta = '/archivos/testfile.txt';
        $this->limpiarArchivosOCarpetas([$ruta]);
        $contenido = 'Contenido de prueba';
        $crear = $this->callMcpTool('crear', [
            'entidad' => 'archivo',
            'ruta' => $ruta,
            'data' => [ 'contenido' => $contenido, 'permisos'=>'775'],
            'token' => config('mcp-server.tokens.admin')
        ]);
        $this->assertIsArray($crear);
        $this->assertTrue(isset($crear['archivo_creado']) || isset($crear['archivo']), 'No se creó el archivo');
        // Eliminar
        $eliminar = $this->callMcpTool('eliminar', [
            'entidad' => 'archivo',
            'ruta' => $ruta,
            'token' => config('mcp-server.tokens.admin')
        ]);
        // DEBUG: Escribir la respuesta en un archivo temporal
        // fwrite(STDERR, json_encode($eliminar, JSON_PRETTY_PRINT));
        $this->assertIsArray($eliminar);
        // si mensaje o message contiene el substring eliminad entonces se considera éxito
        $this->assertTrue(
            str_contains($eliminar['mensaje'] ?? '', 'eliminad') ||
            str_contains($eliminar['message'] ?? '', 'eliminad'),
            'No se eliminó el archivo'
        );
        $this->assertFalse((new StorageItem($ruta))->exists(), 'El archivo sigue existiendo tras eliminarlo');
    }

    public function test_sobrescribir_archivo() {
        $ruta = '/archivos/sobrescribir.txt';
        $this->limpiarArchivosOCarpetas([$ruta]);
        (new StorageItem($ruta))->put('original');
        $crear = $this->callMcpTool('crear', [
            'entidad' => 'archivo',
            'ruta' => $ruta,
            'data' => [ 'contenido' => 'nuevo'],
            'token' => config('mcp-server.tokens.admin')
        ]);
        $this->assertIsArray($crear);
        $this->assertArrayHasKey('error', $crear, 'Debe dar error al sobrescribir archivo existente');
    }

    public function test_crear_carpeta() {
        $ruta = '/archivos/nuevacarpeta';
        $this->limpiarArchivosOCarpetas([$ruta]);
        $crear = $this->callMcpTool('crear', [
            'entidad' => 'archivo',
            'ruta' => $ruta,
            'data' => [ 'es_carpeta' => true ],
            'token' => config('mcp-server.tokens.admin')
        ]);
        $this->assertIsArray($crear);
        $this->assertTrue((new StorageItem($ruta))->directoryExists(), 'No se creó la carpeta');
    }

    public function test_permisos_carpeta() {
        $ruta = '/archivos/carpeta_permiso';
        $this->limpiarArchivosOCarpetas([$ruta]);
        $this->callMcpTool('crear', [
            'entidad' => 'archivo',
            'ruta' => $ruta,
            'data' => [ 'es_carpeta' => true, 'permisos' => '755' ],
            'token' => config('mcp-server.tokens.admin')
        ]);
        $editar = $this->callMcpTool('editar', [
            'entidad' => 'archivo',
            'ruta' => $ruta,
            'data' => [ 'permisos' => '700' ],
            'token' => config('mcp-server.tokens.admin')
        ]);
        $this->assertIsArray($editar);
        $this->assertEquals('700', $editar['archivo_editado']['permisos']);
    }

    public function test_leer_contenido_archivo() {
        $ruta = '/archivos/leer.txt';
        $this->limpiarArchivosOCarpetas([$ruta]);
        $contenido = 'contenido para leer';
        $this->callMcpTool('crear', [
            'entidad' => 'archivo',
            'ruta' => $ruta,
            'data' => [ 'contenido' => $contenido ],
            'token' => config('mcp-server.tokens.admin')
        ]);
        $contenidoLeido = (new StorageItem($ruta))->exists() ? file_get_contents((new StorageItem($ruta))->path) : null;
        $this->assertEquals($contenido, $contenidoLeido);
    }

    public function test_error_renombrar_a_existente() {
        $ruta1 = '/archivos/yaexiste1.txt';
        $ruta2 = '/archivos/yaexiste2.txt';
        $this->limpiarArchivosOCarpetas([$ruta1, $ruta2]);
        (new StorageItem($ruta1))->put('uno');
        (new StorageItem($ruta2))->put('dos');
        $result = $this->callMcpTool('editar', [
            'entidad' => 'archivo',
            'ruta' => $ruta1,
            'data' => ['nuevo_nombre' => 'yaexiste2.txt'],
            'token' => config('mcp-server.tokens.admin')
        ]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result, 'Debe dar error al renombrar a un nombre ya existente');
    }

    private function limpiarArchivosOCarpetas(array $rutas) {
        foreach ($rutas as $ruta) {
            $sti = new StorageItem($ruta);
            if ($sti->exists()) {
                $sti->delete();
            }
            if ($sti->directoryExists()) {
                $sti->deleteDirectory();
            }
        }
    }

    public function test_editar_archivo() {
        $disk = 'archivos';
        $carpeta = 'archivos/testcarpeta';
        $archivo = $carpeta . '/testfile.txt';
        $nuevoArchivo = $carpeta . '/renombrado.txt';
        $nuevaCarpeta = 'archivos/renombradacarpeta';
        $token = config('mcp-server.tokens.admin');

        // Limpieza previa usando el método reutilizable
        $this->limpiarArchivosOCarpetas([
            '/' . $nuevoArchivo,
            '/' . $archivo,
            '/archivos/renombradacarpeta/renombrado.txt',
            '/' . $nuevaCarpeta,
            '/' . $carpeta
        ]);

        // Inicializar carpeta y archivo
        StorageItem::ensureDirExists('/' . $carpeta);
        (new StorageItem('/' . $archivo))->put('contenido original');
        // Forzar propietario admin tras crear el archivo
        \App\Models\Nodo::updateOrCreate(
            ['ubicacion' => '/' . $archivo],
            [
                'user_id' => 1,
                'group_id' => 1,
                'permisos' => '1755',
                'es_carpeta' => 0,
                'oculto' => 0,
            ]
        );

        // Renombrar archivo
        $result = $this->callMcpTool('editar', [
            'entidad' => 'archivo',
            'ruta' => '/' . $archivo,
            'data' => ['nuevo_nombre' => 'renombrado.txt'],
            'token' => $token
        ]);
        $this->assertIsArray($result);
        $this->assertTrue((new StorageItem('/' . $nuevoArchivo))->exists(), 'El archivo no fue renombrado en storage');
        $this->assertFalse((new StorageItem('/' . $archivo))->exists(), 'El archivo original sigue existiendo');
        // Forzar propietario admin tras renombrar
        \App\Models\Nodo::updateOrCreate(
            ['ubicacion' => '/' . $nuevoArchivo],
            [
                'user_id' => 1,
                'group_id' => 1,
                'permisos' => '1755',
                'es_carpeta' => 0,
                'oculto' => 0,
            ]
        );

        // Renombrar carpeta
        $result2 = $this->callMcpTool('editar', [
            'entidad' => 'archivo',
            'ruta' => '/' . $carpeta,
            'data' => ['nuevo_nombre' => 'renombradacarpeta'],
            'token' => $token
        ]);
        $this->assertIsArray($result2);
        $this->assertTrue((new StorageItem('/' . $nuevaCarpeta))->directoryExists(), 'La carpeta no fue renombrada en storage');
        $this->assertFalse((new StorageItem('/' . $carpeta))->directoryExists(), 'La carpeta original sigue existiendo');
        // El archivo renombrado debe estar dentro de la nueva carpeta
        $this->assertTrue((new StorageItem('/archivos/renombradacarpeta/renombrado.txt'))->exists(), 'El archivo no está en la carpeta renombrada');
        // Forzar propietario admin tras renombrar carpeta
        \App\Models\Nodo::updateOrCreate(
            ['ubicacion' => '/archivos/renombradacarpeta/renombrado.txt'],
            [
                'user_id' => 1,
                'group_id' => 1,
                'permisos' => '1755',
                'es_carpeta' => 0,
                'oculto' => 0,
            ]
        );

        // Cambiar permisos
        $result3 = $this->callMcpTool('editar', [
            'entidad' => 'archivo',
            'ruta' => '/archivos/renombradacarpeta/renombrado.txt',
            'data' => ['permisos' => '1775'],
            'token' => $token
        ]);
        $this->assertIsArray($result3);
        $this->assertEquals('1775', $result3['archivo_editado']['permisos']);

        // Cambiar propietario
        $result4 = $this->callMcpTool('editar', [
            'entidad' => 'archivo',
            'ruta' => '/archivos/renombradacarpeta/renombrado.txt',
            'data' => ['user_id' => 2, 'group_id' => 3],
            'token' => $token
        ]);
        $this->assertIsArray($result4);
        $this->assertEquals(2, $result4['archivo_editado']['user_id']);
        $this->assertEquals(3, $result4['archivo_editado']['group_id']);
    }

    private function getWithCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [
            'status' => $status,
            'headers' => $headers,
            'body' => $body
        ];
    }

    public function test_editar_permisos_archivo() {
        $ruta = '/archivos/testdescarga.txt';
        $token = config('mcp-server.tokens.admin');

        // Limpiar antes
        $this->limpiarArchivosOCarpetas([$ruta]);
        // Crear archivo descargable
        $sti = new StorageItem($ruta);
        $sti->put('descargable');
        // Asignar permisos públicos (ejemplo: 755)
        $this->callMcpTool('editar', [
            'entidad' => 'archivo',
            'ruta' => $ruta,
            'data' => ['permisos' => '755'],
            'token' => $token
        ]);
        // Obtener la URL pública real
        $url = $sti->url;
        // Verificar que se puede descargar usando curl
        $curlResp = $this->getWithCurl($url);
        //fwrite(STDERR, "curl status $url: {$curlResp['status']}\n");
        //fwrite(STDERR, "curl headers: {$curlResp['headers']}\n");
        //fwrite(STDERR, "curl body: {$curlResp['body']}\n");
        $this->assertEquals(200, $curlResp['status']);
        $this->assertStringContainsString('descargable', $curlResp['body']);

        // Cambiar permisos a privado (ejemplo: 550)
        $this->callMcpTool('editar', [
            'entidad' => 'archivo',
            'ruta' => $ruta,
            'data' => ['permisos' => '750'],
            'token' => $token
        ]);
        // Volver a obtener la URL pública real (por si cambia)
        $url = (new StorageItem($ruta))->url;
        $curlResp2 = $this->getWithCurl($url);
        //fwrite(STDERR, "curl status $url: {$curlResp2['status']}\n");
        //fwrite(STDERR, "curl headers: {$curlResp2['headers']}\n");
        //fwrite(STDERR, "curl body: {$curlResp2['body']}\n");
        $this->assertTrue(
            in_array($curlResp2['status'], [403, 404]),
            'El archivo debería estar protegido y no ser accesible tras cambiar permisos a 750'
        );
    }
}
