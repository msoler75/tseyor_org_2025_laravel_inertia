<?php

namespace Tests\Feature\MCP;

class TutorialToolTest extends McpFeatureTestCase
{
    public function test_info_tutorial()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'tutorial']);
        $this->assertIsArray($result);
        $tutorial = $result['tutorial'] ?? null;
        $this->assertIsArray($tutorial);
        $this->assertArrayHasKey('descripcion', $tutorial);
        $this->assertArrayHasKey('parametros_listar', $tutorial);
        $this->assertArrayHasKey('campos', $tutorial);
        $this->assertIsString($tutorial['descripcion']);
        $this->assertIsArray($tutorial['parametros_listar']);
        $this->assertIsArray($tutorial['campos']);
        // Ajustar campos esperados según definición real de info.php si existe
    }

    public function test_listar_tutoriales()
    {
        \App\Models\Tutorial::truncate();
        $pp = \App\Http\Controllers\TutorialesController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp + 5; $i++) {
            \App\Models\Tutorial::create([
                'titulo' => 'Tutorial ' . $i,
                'slug' => 'tutorial-' . $i . '-' . uniqid(),
                'categoria' => 'general',
                'descripcion' => 'Desc ' . $i,
                'texto' => 'Texto ' . $i,
                'video' => 'https://video' . $i . '.com',
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'tutorial']);
        $this->assertIsArray($result);
        // fwrite(STDERR, print_r($result, true));
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'tutorial', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(5, count($result['listado']['data']));
        // buscar un tutorial específico
        $result = $this->callMcpTool('listar', ['entidad' => 'tutorial', 'buscar' => 'Tutorial 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar un tutorial que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'tutorial', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_tutorial()
    {
        \App\Models\Tutorial::truncate();
        $tutorial = \App\Models\Tutorial::create([
            'titulo' => 'Tutorial Test',
            'slug' => 'tutorial-test-' . uniqid(),
            'categoria' => 'general',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'video' => 'https://video-test.com',
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad' => 'tutorial', 'slug' => $tutorial->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('tutorial', $result);
        $this->assertEquals($tutorial->slug, $result['tutorial']['slug'] ?? $result['tutorial']->slug ?? null);
    }

    public function test_crear_tutorial()
    {
        \App\Models\Tutorial::truncate();
        $params = [
            'entidad' => 'tutorial',
            'data' => [
                'titulo' => 'Nuevo Tutorial',
                'slug' => 'nuevo-tutorial-' . uniqid(),
                'categoria' => 'general',
                'descripcion' => 'Descripción de prueba',
                'texto' => 'Texto de prueba',
                'video' => 'https://nuevo-tutorial.com',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('tutoriales', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_tutorial()
    {
        \App\Models\Tutorial::truncate();
        $tutorial = \App\Models\Tutorial::create([
            'titulo' => 'Editar Tutorial',
            'slug' => 'editar-tutorial-' . uniqid(),
            'categoria' => 'general',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'video' => 'https://editar-tutorial.com',
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'tutorial',
            'id' => $tutorial->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('tutoriales', ['id' => $tutorial->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_tutorial()
    {
        \App\Models\Tutorial::truncate();
        $tutorial = \App\Models\Tutorial::create([
            'titulo' => 'Eliminar Tutorial',
            'slug' => 'eliminar-tutorial-' . uniqid(),
            'categoria' => 'general',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'video' => 'https://eliminar-tutorial.com',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'tutorial',
            'id' => $tutorial->id,
            'force' => true,
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('tutoriales', ['id' => $tutorial->id]);
    }
}
