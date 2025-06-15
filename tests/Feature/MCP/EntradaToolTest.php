<?php

namespace Tests\Feature\MCP;

use App\Models\Entrada;
use Illuminate\Support\Facades\DB;

class EntradaToolTest extends McpFeatureTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    }

    public function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    public function test_listar_entradas()
    {
        Entrada::truncate();
        for ($i = 0; $i < 2; $i++) {
            Entrada::create([
                'titulo' => 'Entrada ' . $i,
                'slug' => 'entrada-' . $i . '-' . uniqid(),
                'texto' => 'texto md ' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'categoria' => 'general',
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'entrada']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(2, count($result['listado']['data']));
    }

    public function test_ver_entrada()
    {
        Entrada::truncate();
        $entrada = Entrada::create([
            'titulo' => 'Entrada Test',
            'slug' => 'entrada-test-' . uniqid(),
            'texto' => 'texto md ' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad' => 'entrada', 'id' => $entrada->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('entrada', $result);
        $this->assertEquals($entrada->slug, $result['entrada']['slug'] ?? $result['entrada']->slug ?? null);
    }

    public function test_crear_entrada()
    {
        Entrada::truncate();
        $params = [
            'entidad' => 'entrada',
            'data' => [
                'titulo' => 'Nueva Entrada',
                'slug' => 'nueva-entrada-' . uniqid(),
                'texto' => 'texto md ' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'categoria' => 'test',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('entradas', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_entrada()
    {
        Entrada::truncate();
        $entrada = Entrada::create([
            'titulo' => 'Editar Entrada',
            'slug' => 'editar-entrada-' . uniqid(),
            'texto' => 'texto md' . uniqid(),
            'descripcion' => 'Desc original',
            'categoria' => 'general',
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'entrada',
            'id' => $entrada->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('entradas', ['id' => $entrada->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_entrada()
    {
        Entrada::truncate();
        $entrada = Entrada::create([
            'titulo' => 'Eliminar Entrada',
            'slug' => 'eliminar-entrada-' . uniqid(),
            'texto' => 'texto md ' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'entrada',
            'id' => $entrada->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('entradas', ['id' => $entrada->id]);
    }

    public function test_info_entrada()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'entrada']);
        $this->assertIsArray($result);
        $entrada = $result['entrada'] ?? null;
        $this->assertIsArray($entrada);
        $this->assertArrayHasKey('descripcion', $entrada);
        $this->assertArrayHasKey('parametros_listar', $entrada);
        $this->assertArrayHasKey('campos', $entrada);
        $this->assertIsString($entrada['descripcion']);
        $this->assertIsArray($entrada['parametros_listar']);
        $this->assertIsArray($entrada['campos']);
        $campos_esperados = [
            'titulo', 'descripcion', 'texto', 'published_at', 'imagen', 'visibilidad'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $entrada['campos'], "Falta el campo '$campo'");
        }
        foreach ($entrada['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }
}
