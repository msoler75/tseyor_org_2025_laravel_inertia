<?php

namespace Tests\Feature\MCP;

use App\Models\Equipo;
use Illuminate\Support\Facades\DB;

class EquipoToolTest extends McpFeatureTestCase
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

    public function test_listar_equipos()
    {
        Equipo::truncate();
        for ($i = 0; $i < 2; $i++) {
            Equipo::create([
                'nombre' => 'Equipo ' . $i,
                'slug' => 'equipo-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'categoria' => 'general',
                'imagen' => '/almacen/equipo' . $i . '.jpg',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'equipo']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(2, count($result['listado']['data']));
    }

    public function test_ver_equipo()
    {
        Equipo::truncate();
        $equipo = Equipo::create([
            'nombre' => 'Equipo Test',
            'slug' => 'equipo-test-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'imagen' => '/almacen/equipo-test.jpg',
        ]);
        $result = $this->callMcpTool('ver', ['entidad' => 'equipo', 'id' => $equipo->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('equipo', $result);
        $this->assertEquals($equipo->slug, $result['equipo']['slug'] ?? $result['equipo']->slug ?? null);
    }

    public function test_crear_equipo()
    {
        Equipo::truncate();
        $params = [
            'entidad' => 'equipo',
            'data' => [
                'nombre' => 'Nuevo Equipo',
                'slug' => 'nuevo-equipo-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'categoria' => 'test',
                'imagen' => '/almacen/nuevo-equipo.jpg',
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('equipos', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_equipo()
    {
        Equipo::truncate();
        $equipo = Equipo::create([
            'nombre' => 'Editar Equipo',
            'slug' => 'editar-equipo-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'imagen' => '/almacen/editar-equipo.jpg',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'equipo',
            'id' => $equipo->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('equipos', ['id' => $equipo->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_equipo()
    {
        \DB::table('informes')->truncate();
        \DB::table('equipo_user')->truncate();
        Equipo::truncate();
        $equipo = Equipo::create([
            'nombre' => 'Eliminar Equipo',
            'slug' => 'eliminar-equipo-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'imagen' => '/almacen/eliminar-equipo.jpg',
        ]);
        $params = [
            'entidad' => 'equipo',
            'id' => $equipo->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('equipos', ['id' => $equipo->id]);
    }

    public function test_info_equipo()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'equipo']);
        $this->assertIsArray($result);
        $equipo = $result['equipo'] ?? null;
        $this->assertIsArray($equipo);
        $this->assertArrayHasKey('descripcion', $equipo);
        $this->assertArrayHasKey('parametros_listar', $equipo);
        $this->assertArrayHasKey('campos', $equipo);
        $this->assertIsString($equipo['descripcion']);
        $this->assertIsArray($equipo['parametros_listar']);
        $this->assertIsArray($equipo['campos']);
        $campos_esperados = [
            'nombre', 'slug', 'descripcion', 'imagen', 'categoria', 'group_id', 'anuncio', 'reuniones', 'informacion', 'oculto', 'ocultarCarpetas', 'ocultarArchivos', 'ocultarMiembros', 'ocultarSolicitudes'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $equipo['campos'], "Falta el campo '$campo'");
        }
        foreach ($equipo['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }
}
