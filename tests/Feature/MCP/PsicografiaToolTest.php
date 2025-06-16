<?php

namespace Tests\Feature\MCP;

class PsicografiaToolTest extends McpFeatureTestCase
{
    public function test_info_psicografia()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'psicografia']);
        $this->assertIsArray($result);
        $psicografia = $result['psicografia'] ?? null;
        $this->assertIsArray($psicografia);
        $this->assertArrayHasKey('descripcion', $psicografia);
        $this->assertArrayHasKey('parametros_listar', $psicografia);
        $this->assertArrayHasKey('campos', $psicografia);
        $this->assertIsString($psicografia['descripcion']);
        $this->assertIsArray($psicografia['parametros_listar']);
        $this->assertIsArray($psicografia['campos']);
        // Ajustar campos esperados según definición real de info.php si existe
    }

    public function test_listar_psicografias()
    {
        \App\Models\Psicografia::truncate();
        $pp = 14; // PsicografiasController usa paginate(14)
        for ($i = 0; $i < $pp + 3; $i++) {
            \App\Models\Psicografia::create([
                'titulo' => 'Psicografia ' . $i,
                'slug' => 'psicografia-' . $i . '-' . uniqid(),
                'categoria' => 'arte',
                'descripcion' => 'Desc ' . $i,
                'imagen' => '/img/psicografia' . $i . '.jpg',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'psicografia']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'psicografia', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(3, count($result['listado']['data']));
        // buscar una psicografía específica
        $result = $this->callMcpTool('listar', ['entidad' => 'psicografia', 'buscar' => 'Psicografia 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar una psicografía que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'psicografia', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_psicografia()
    {
        \App\Models\Psicografia::truncate();
        $psicografia = \App\Models\Psicografia::create([
            'titulo' => 'Psicografia Test',
            'slug' => 'psicografia-test-' . uniqid(),
            'categoria' => 'arte',
            'descripcion' => 'Desc',
            'imagen' => '/img/psicografia-test.jpg',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'psicografia', 'slug' => $psicografia->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('psicografia', $result);
        $this->assertEquals($psicografia->slug, $result['psicografia']['slug'] ?? $result['psicografia']->slug ?? null);
    }

    public function test_crear_psicografia()
    {
        \App\Models\Psicografia::truncate();
        $params = [
            'entidad' => 'psicografia',
            'data' => [
                'titulo' => 'Nueva Psicografia',
                'slug' => 'nueva-psicografia-' . uniqid(),
                'categoria' => 'arte',
                'descripcion' => 'Descripción de prueba',
                'imagen' => '/img/nueva-psicografia.jpg',
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('psicografias', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_psicografia()
    {
        \App\Models\Psicografia::truncate();
        $psicografia = \App\Models\Psicografia::create([
            'titulo' => 'Editar Psicografia',
            'slug' => 'editar-psicografia-' . uniqid(),
            'categoria' => 'arte',
            'descripcion' => 'Desc',
            'imagen' => '/img/editar-psicografia.jpg',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'psicografia',
            'id' => $psicografia->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('psicografias', ['id' => $psicografia->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_psicografia()
    {
        \App\Models\Psicografia::truncate();
        $psicografia = \App\Models\Psicografia::create([
            'titulo' => 'Eliminar Psicografia',
            'slug' => 'eliminar-psicografia-' . uniqid(),
            'categoria' => 'arte',
            'descripcion' => 'Desc',
            'imagen' => '/img/eliminar-psicografia.jpg',
        ]);
        $params = [
            'entidad' => 'psicografia',
            'id' => $psicografia->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('psicografias', ['id' => $psicografia->id]);
    }
}
