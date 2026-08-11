<?php

namespace Tests\Feature\MCP;

use App\Http\Controllers\SalasController;
use App\Models\Sala;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SalaToolTest extends McpFeatureTestCase
{
    use DatabaseTransactions;

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
        $pp = SalasController::$ITEMS_POR_PAGINA;
        Sala::withoutEvents(function () use ($pp) {
            for ($i = 0; $i < $pp + 2; $i++) {
                Sala::create([
                    'nombre' => 'Sala '.$i,
                    'slug' => 'sala-'.$i.'-'.uniqid(),
                    'descripcion' => 'Desc '.$i,
                    'enlace' => 'https://enlace'.$i.'.com',
                ]);
            }
        });
        $this->makeAllSearchable(Sala::class);
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
        $sala = Sala::create([
            'nombre' => 'Sala Test',
            'slug' => 'sala-test-'.uniqid(),
            'descripcion' => 'Desc',
            'enlace' => 'https://enlace-test.com',
        ]);
        $result = $this->callMcpTool('ver', ['entidad' => 'sala', 'slug' => $sala->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('sala', $result);
        $this->assertEquals($sala->slug, $result['sala']['slug'] ?? $result['sala']->slug ?? null);
    }

    public function test_crear_sala()
    {
        $params = [
            'entidad' => 'sala',
            'data' => [
                'nombre' => 'Nueva Sala',
                'slug' => 'nueva-sala-'.uniqid(),
                'descripcion' => 'Descripción de prueba',
                'enlace' => 'https://nueva-sala.com',
            ],
            'token' => config('mcp-server.tokens.admin'),
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('salas', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_sala()
    {
        $sala = Sala::create([
            'nombre' => 'Editar Sala',
            'slug' => 'editar-sala-'.uniqid(),
            'descripcion' => 'Desc',
            'enlace' => 'https://editar-sala.com',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'sala',
            'id' => $sala->id,
            'data' => [
                'descripcion' => $nuevaDescripcion,
            ],
            'token' => config('mcp-server.tokens.admin'),
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('salas', ['id' => $sala->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_sala()
    {
        $sala = Sala::create([
            'nombre' => 'Eliminar Sala',
            'slug' => 'eliminar-sala-'.uniqid(),
            'descripcion' => 'Desc',
            'enlace' => 'https://eliminar-sala.com',
        ]);
        $params = [
            'entidad' => 'sala',
            'id' => $sala->id,
            'force' => true,
            'token' => config('mcp-server.tokens.admin'),
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('salas', ['id' => $sala->id]);
    }
}
