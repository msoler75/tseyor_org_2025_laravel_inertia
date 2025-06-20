<?php

namespace Tests\Feature\MCP;

use Illuminate\Support\Facades\DB;

class GrupoToolTest extends McpFeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    }

    protected function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    public function test_listar_grupos()
    {
        \App\Models\Grupo::truncate();
        $pp = 12; // Valor por defecto para paginación de grupos
        for ($i = 0; $i < $pp + 4; $i++) {
            \App\Models\Grupo::create([
                'nombre' => 'Grupo ' . $i,
                'slug' => 'grupo-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'grupo']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('grupos', $result);
        $this->assertEquals($pp + 4, count($result['grupos']));
        // buscar un grupo específico
        $result = $this->callMcpTool('listar', ['entidad' => 'grupo', 'buscar' => 'Grupo 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('grupos', $result);
        $this->assertGreaterThanOrEqual(1, count($result['grupos']));
        // buscar un grupo que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'grupo', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('grupos', $result);
        $this->assertEquals(0, count($result['grupos']));
    }

    public function test_ver_grupo()
    {
        \App\Models\Grupo::truncate();
        $grupo = \App\Models\Grupo::create([
            'nombre' => 'Grupo Test',
            'slug' => 'grupo-test-' . uniqid(),
            'descripcion' => 'Desc',
        ]);
        $result = $this->callMcpTool('ver', ['entidad' => 'grupo', 'slug' => $grupo->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('grupo', $result);
        $this->assertEquals($grupo->slug, $result['grupo']['slug'] ?? $result['grupo']->slug ?? null);
    }

    public function test_crear_grupo()
    {
        \App\Models\Grupo::truncate();
        $params = [
            'entidad' => 'grupo',
            'data' => [
                'nombre' => 'Nuevo Grupo',
                'slug' => 'nuevo-grupo-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('grupos', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_grupo()
    {
        \App\Models\Grupo::truncate();
        $grupo = \App\Models\Grupo::create([
            'nombre' => 'Editar Grupo',
            'slug' => 'editar-grupo-' . uniqid(),
            'descripcion' => 'Desc',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'grupo',
            'id' => $grupo->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('grupos', ['id' => $grupo->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_grupo()
    {
        \App\Models\Grupo::truncate();
        $grupo = \App\Models\Grupo::create([
            'nombre' => 'Eliminar Grupo',
            'slug' => 'eliminar-grupo-' . uniqid(),
            'descripcion' => 'Desc',
        ]);
        // Eliminar definitivamente todos los nodos asociados (incluyendo soft deleted)
        \App\Models\Nodo::withTrashed()->where('group_id', $grupo->id)->forceDelete();
        $params = [
            'entidad' => 'grupo',
            'id' => $grupo->id,
            'force' => true,
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('grupos', ['id' => $grupo->id]);
    }

    public function test_no_eliminar_grupo_con_nodos_asociados()
    {
        \App\Models\Grupo::truncate();
        \App\Models\Nodo::truncate();
        $grupo = \App\Models\Grupo::create([
            'nombre' => 'Grupo Con Nodos',
            'slug' => 'grupo-con-nodos-' . uniqid(),
            'descripcion' => 'Desc',
        ]);
        // Asociar un nodo a este grupo
        \App\Models\Nodo::create([
            'ubicacion' => 'test',
            'permisos' => '1755',
            'user_id' => 1,
            'group_id' => $grupo->id,
            'es_carpeta' => 1,
        ]);
        $params = [
            'entidad' => 'grupo',
            'id' => $grupo->id,
            'force' => true,
            'token' => config('mcp-server.tokens.admin')
        ];
        $result = $this->callMcpTool('eliminar', $params);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('nodos asociados', $result['error']);
        $this->assertDatabaseHas('grupos', ['id' => $grupo->id]);
    }

    public function test_info_grupo()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'grupo']);
        $this->assertIsArray($result);
        $grupo = $result['grupo'] ?? null;
        $this->assertIsArray($grupo);
        $this->assertArrayHasKey('descripcion', $grupo);
        $this->assertArrayHasKey('parametros_listar', $grupo);
        $this->assertArrayHasKey('campos', $grupo);
        $this->assertIsString($grupo['descripcion']);
        $this->assertIsArray($grupo['parametros_listar']);
        $this->assertIsArray($grupo['campos']);
        $campos_esperados = [
            'nombre',
            'slug',
            'descripcion'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $grupo['campos'], "Falta el campo '$campo'");
        }
        foreach ($grupo['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }
}
