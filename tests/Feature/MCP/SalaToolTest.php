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
        // remover foreign key constraints to allow truncation
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Sala::truncate();
        $pp = \App\Http\Controllers\SalasController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp + 2; $i++) {
            \App\Models\Sala::create([
                'nombre' => 'Sala ' . $i,
                'slug' => 'sala-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'enlace' => 'https://enlace' . $i . '.com',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'sala']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'sala', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(2, count($result['listado']['data']));
        // buscar una sala específica
        $result = $this->callMcpTool('listar', ['entidad' => 'sala', 'buscar' => 'Sala 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar una sala que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'sala', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_sala()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
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
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Sala::truncate();
        $params = [
            'entidad' => 'sala',
            'data' => [
                'nombre' => 'Nueva Sala',
                'slug' => 'nueva-sala-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'enlace' => 'https://nueva-sala.com',
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('salas', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_sala()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
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
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('salas', ['id' => $sala->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_sala()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
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
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('salas', ['id' => $sala->id]);
    }
}
