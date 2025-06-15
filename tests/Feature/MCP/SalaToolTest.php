<?php

namespace Tests\Feature\MCP;

class SalaToolTest extends McpFeatureTestCase
{
    public function test_info_sala()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'sala']);
        $this->assertIsArray($result);
        $sala = $result['sala'] ?? null;
        $this->assertIsArray($sala);
        $this->assertArrayHasKey('descripcion', $sala);
        $this->assertArrayHasKey('parametros_listar', $sala);
        $this->assertArrayHasKey('campos', $sala);
        $this->assertIsString($sala['descripcion']);
        $this->assertIsArray($sala['parametros_listar']);
        $this->assertIsArray($sala['campos']);
        // Ajustar campos esperados según definición real de info.php si existe
    }

    public function test_listar_salas()
    {
        \App\Models\Sala::truncate();
        for ($i = 0; $i < 2; $i++) {
            \App\Models\Sala::create([
                'nombre' => 'Sala ' . $i,
                'slug' => 'sala-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'enlace' => 'https://enlace' . $i . '.com',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'sala']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('salas', $result);
        $this->assertGreaterThanOrEqual(2, count($result['salas']));
    }

    public function test_ver_sala()
    {
        \App\Models\Sala::truncate();
        $sala = \App\Models\Sala::create([
            'nombre' => 'Sala Test',
            'slug' => 'sala-test-' . uniqid(),
            'descripcion' => 'Desc',
            'enlace' => 'https://enlace-test.com',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'sala', 'slug' => $sala->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('sala', $result);
        $this->assertEquals($sala->slug, $result['sala']['slug'] ?? $result['sala']->slug ?? null);
    }

    public function test_crear_sala()
    {
        \App\Models\Sala::truncate();
        $params = [
            'entidad' => 'sala',
            'data' => [
                'nombre' => 'Nueva Sala',
                'slug' => 'nueva-sala-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'enlace' => 'https://nueva-sala.com',
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('salas', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_sala()
    {
        \App\Models\Sala::truncate();
        $sala = \App\Models\Sala::create([
            'nombre' => 'Editar Sala',
            'slug' => 'editar-sala-' . uniqid(),
            'descripcion' => 'Desc',
            'enlace' => 'https://editar-sala.com',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'sala',
            'id' => $sala->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('salas', ['id' => $sala->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_sala()
    {
        \App\Models\Sala::truncate();
        $sala = \App\Models\Sala::create([
            'nombre' => 'Eliminar Sala',
            'slug' => 'eliminar-sala-' . uniqid(),
            'descripcion' => 'Desc',
            'enlace' => 'https://eliminar-sala.com',
        ]);
        $params = [
            'entidad' => 'sala',
            'id' => $sala->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('salas', ['id' => $sala->id]);
    }
}
