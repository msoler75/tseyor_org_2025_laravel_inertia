<?php

namespace Tests\Feature\MCP;

use App\Http\Controllers\InformesController;
use App\Models\Equipo;
use App\Models\Informe;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InformeToolTest extends McpFeatureTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Usar firstOrCreate para evitar duplicados si por alguna razón no se vacía
        Equipo::firstOrCreate(
            ['id' => 1],
            ['nombre' => 'Equipo Test', 'slug' => 'equipo-test', 'oculto' => false]
        );
    }

    public function test_info_informe()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'informe']);
        $this->assertIsArray($result);
        $informe = $result['informe'] ?? null;
        $this->assertIsArray($informe);
        $this->assertArrayHasKey('descripcion', $informe);
        $this->assertArrayHasKey('parametros_listar', $informe);
        $this->assertArrayHasKey('campos', $informe);
        $this->assertIsString($informe['descripcion']);
        $this->assertIsArray($informe['parametros_listar']);
        $this->assertIsArray($informe['campos']);
        $campos_esperados = [
            'titulo', 'categoria', 'equipo_id', 'descripcion', 'texto', 'audios', 'archivos', 'visibilidad',
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $informe['campos'], "Falta el campo '$campo'");
        }
        foreach ($informe['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }

    public function test_listar_informes()
    {
        $pp = InformesController::$ITEMS_POR_PAGINA;
        Informe::withoutEvents(function () use ($pp) {
            for ($i = 0; $i < $pp + 2; $i++) {
                Informe::create([
                    'titulo' => 'Informe '.$i,
                    'categoria' => 'anual',
                    'equipo_id' => 1,
                    'descripcion' => 'Desc '.$i,
                    'texto' => 'Texto '.$i,
                    'audios' => json_encode([]),
                    'archivos' => json_encode([]),
                    'visibilidad' => 'P',
                ]);
            }
        });
        $this->makeAllSearchable(Informe::class);
        $result = $this->callMcpTool('listar', ['entidad' => 'informe']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'informe', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(2, count($result['listado']['data']));
        // buscar un informe específico
        $result = $this->callMcpTool('listar', ['entidad' => 'informe', 'buscar' => 'Informe 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar un informe que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'informe', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_informe()
    {
        $informe = Informe::create([
            'titulo' => 'Informe Test',
            'categoria' => 'anual',
            'equipo_id' => 1,
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'audios' => json_encode([]),
            'archivos' => json_encode([]),
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad' => 'informe', 'id' => $informe->id]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('informe', $result);
        $this->assertEquals($informe->id, $result['informe']['id'] ?? $result['informe']->id ?? null);
    }

    public function test_crear_informe()
    {
        $params = [
            'entidad' => 'informe',
            'data' => [
                'titulo' => 'Nuevo Informe',
                'categoria' => 'anual',
                'equipo_id' => 1,
                'descripcion' => 'Descripción de prueba',
                'texto' => 'Texto de prueba',
                'audios' => json_encode([]),
                'archivos' => json_encode([]),
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.admin'),
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('informes', ['titulo' => $params['data']['titulo']]);
    }

    public function test_editar_informe()
    {
        $informe = Informe::create([
            'titulo' => 'Editar Informe',
            'categoria' => 'anual',
            'equipo_id' => 1,
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'audios' => json_encode([]),
            'archivos' => json_encode([]),
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'informe',
            'id' => $informe->id,
            'data' => [
                'descripcion' => $nuevaDescripcion,
            ],
            'token' => config('mcp-server.tokens.admin'),
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('informes', ['id' => $informe->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_informe()
    {
        // Un informe SIN equipo asociado sí se puede eliminar
        $informe = Informe::create([
            'titulo' => 'Eliminar Informe',
            'categoria' => 'anual',
            'equipo_id' => null,
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'audios' => json_encode([]),
            'archivos' => json_encode([]),
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'informe',
            'id' => $informe->id,
            'force' => true,
            'token' => config('mcp-server.tokens.admin'),
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('informes', ['id' => $informe->id]);

        // Un informe asociado a un equipo NO se puede eliminar (checkDeleteable)
        $informeConEquipo = Informe::create([
            'titulo' => 'Informe Con Equipo',
            'categoria' => 'anual',
            'equipo_id' => 1,
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'audios' => json_encode([]),
            'archivos' => json_encode([]),
            'visibilidad' => 'P',
        ]);
        $params2 = [
            'entidad' => 'informe',
            'id' => $informeConEquipo->id,
            'force' => true,
            'token' => config('mcp-server.tokens.admin'),
        ];
        $result = $this->callMcpTool('eliminar', $params2);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('asociado a un equipo', $result['error']);
        $this->assertDatabaseHas('informes', ['id' => $informeConEquipo->id]);
    }
}
