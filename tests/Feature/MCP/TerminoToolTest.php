<?php

namespace Tests\Feature\MCP;

class TerminoToolTest extends McpFeatureTestCase
{
    public function test_info_termino()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'termino']);
        $this->assertIsArray($result);
        $termino = $result['termino'] ?? null;
        $this->assertIsArray($termino);
        $this->assertArrayHasKey('descripcion', $termino);
        $this->assertArrayHasKey('parametros_listar', $termino);
        $this->assertArrayHasKey('campos', $termino);
        $this->assertIsString($termino['descripcion']);
        $this->assertIsArray($termino['parametros_listar']);
        $this->assertIsArray($termino['campos']);
        // Ajustar campos esperados según definición real de info.php si existe
    }

    public function test_listar_terminos()
    {
        \App\Models\Termino::truncate();
        $pp = \App\Http\Controllers\TerminosController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp + 3; $i++) {
            \App\Models\Termino::create([
                'nombre' => 'Termino ' . $i,
                'slug' => 'termino-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'texto' => 'Texto ' . $i,
                'ref_terminos' => '',
                'ref_libros' => '',
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'termino']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'termino', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(3, count($result['listado']['data']));
        // buscar un término específico
        $result = $this->callMcpTool('listar', ['entidad' => 'termino', 'buscar' => 'Termino 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar un término que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'termino', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_termino()
    {
        \App\Models\Termino::truncate();
        $termino = \App\Models\Termino::create([
            'nombre' => 'Termino Test',
            'slug' => 'termino-test-' . uniqid(),
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'ref_terminos' => '',
            'ref_libros' => '',
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'termino', 'slug' => $termino->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('termino', $result);
        $this->assertEquals($termino->slug, $result['termino']['slug'] ?? $result['termino']->slug ?? null);
    }

    public function test_crear_termino()
    {
        \App\Models\Termino::truncate();
        $params = [
            'entidad' => 'termino',
            'data' => [
                'nombre' => 'Nuevo Termino',
                'slug' => 'nuevo-termino-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'texto' => 'Texto de prueba',
                'ref_terminos' => '',
                'ref_libros' => '',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('terminos', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_termino()
    {
        \App\Models\Termino::truncate();
        $termino = \App\Models\Termino::create([
            'nombre' => 'Editar Termino',
            'slug' => 'editar-termino-' . uniqid(),
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'ref_terminos' => '',
            'ref_libros' => '',
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'termino',
            'id' => $termino->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('terminos', ['id' => $termino->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_termino()
    {
        \App\Models\Termino::truncate();
        $termino = \App\Models\Termino::create([
            'nombre' => 'Eliminar Termino',
            'slug' => 'eliminar-termino-' . uniqid(),
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'ref_terminos' => '',
            'ref_libros' => '',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'termino',
            'id' => $termino->id,
            'force' => true,
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('terminos', ['id' => $termino->id]);
    }
}
