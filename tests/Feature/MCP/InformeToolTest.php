<?php

namespace Tests\Feature\MCP;

use Illuminate\Support\Facades\DB;

class InformeToolTest extends McpFeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Forzar limpieza y evitar duplicados de slug
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Informe::truncate();
        \App\Models\Equipo::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // Usar firstOrCreate para evitar duplicados si por alguna razón no se vacía
        \App\Models\Equipo::firstOrCreate(
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
            'titulo', 'categoria', 'equipo_id', 'descripcion', 'texto', 'audios', 'archivos', 'visibilidad'
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
        \App\Models\Informe::truncate();
        $pp = \App\Http\Controllers\InformesController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp + 2; $i++) {
            \App\Models\Informe::create([
                'titulo' => 'Informe ' . $i,
                'categoria' => 'anual',
                'equipo_id' => 1,
                'descripcion' => 'Desc ' . $i,
                'texto' => 'Texto ' . $i,
                'audios' => json_encode([]),
                'archivos' => json_encode([]),
                'visibilidad' => 'P',
            ]);
        }
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
        \App\Models\Informe::truncate();
        $informe = \App\Models\Informe::create([
            'titulo' => 'Informe Test',
            'categoria' => 'anual',
            'equipo_id' => 1,
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'audios' => json_encode([]),
            'archivos' => json_encode([]),
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'informe', 'id' => $informe->id]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('informe', $result);
        $this->assertEquals($informe->id, $result['informe']['id'] ?? $result['informe']->id ?? null);
    }

    public function test_crear_informe()
    {
        \App\Models\Informe::truncate();
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
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('informes', ['titulo' => $params['data']['titulo']]);
    }

    public function test_editar_informe()
    {
        \App\Models\Informe::truncate();
        $informe = \App\Models\Informe::create([
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
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('informes', ['id' => $informe->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_informe()
    {
        \App\Models\Informe::truncate();
        $informe = \App\Models\Informe::create([
            'titulo' => 'Eliminar Informe',
            'categoria' => 'anual',
            'equipo_id' => 1,
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
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('informes', ['id' => $informe->id]);
    }
}
