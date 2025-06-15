<?php

namespace Tests\Feature\MCP;

use App\Models\Centro;
use Illuminate\Support\Facades\DB;

class CentroToolTest extends McpFeatureTestCase
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

    public function test_listar_centros()
    {
        Centro::truncate();
        for ($i = 0; $i < 2; $i++) {
            Centro::create([
                'nombre' => 'Casa Tseyor ' . $i,
                'slug' => 'centro-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'imagen' => '/almacen/centro' . $i . '.jpg',
                'pais' => 'ES',
                'poblacion' => 'Ciudad ' . $i,
                'contacto_id' => null,
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'centro']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('muulasterios', $result);
        $this->assertArrayHasKey('casas', $result);
        $this->assertArrayHasKey('paises', $result);
        $this->assertGreaterThanOrEqual(2, count($result['casas']));
    }

    public function test_ver_centro()
    {
        Centro::truncate();
        $centro = Centro::create([
            'nombre' => 'Centro Test',
            'slug' => 'centro-test-' . uniqid(),
            'descripcion' => 'Desc',
            'imagen' => '/almacen/centro-test.jpg',
            'pais' => 'ES',
            'poblacion' => 'Ciudad Test',
            'contacto_id' => null,
        ]);
        $result = $this->callMcpTool('ver', ['entidad' => 'centro', 'id' => $centro->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('centro', $result);
        $this->assertEquals($centro->slug, $result['centro']['slug'] ?? $result['centro']->slug ?? null);
    }

    public function test_crear_centro()
    {
        Centro::truncate();
        $params = [
            'entidad' => 'centro',
            'data' => [
                'nombre' => 'Nuevo Centro',
                'slug' => 'nuevo-centro-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'imagen' => '/almacen/nuevo-centro.jpg',
                'pais' => 'ES',
                'poblacion' => 'Ciudad Nueva',
                'contacto_id' => null,
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('centros', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_centro()
    {
        Centro::truncate();
        $centro = Centro::create([
            'nombre' => 'Editar Centro',
            'slug' => 'editar-centro-' . uniqid(),
            'descripcion' => 'Desc',
            'imagen' => '/almacen/editar-centro.jpg',
            'pais' => 'ES',
            'poblacion' => 'Ciudad Editar',
            'contacto_id' => null,
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'centro',
            'id' => $centro->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('centros', ['id' => $centro->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_centro()
    {
        Centro::truncate();
        $centro = Centro::create([
            'nombre' => 'Eliminar Centro',
            'slug' => 'eliminar-centro-' . uniqid(),
            'descripcion' => 'Desc',
            'imagen' => '/almacen/eliminar-centro.jpg',
            'pais' => 'ES',
            'poblacion' => 'Ciudad Eliminar',
            'contacto_id' => null,
        ]);
        $params = [
            'entidad' => 'centro',
            'id' => $centro->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('centros', ['id' => $centro->id]);
    }

    public function test_info_centro()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'centro']);
        $this->assertIsArray($result);
        $centro = $result['centro'] ?? null;
        $this->assertIsArray($centro);
        $this->assertArrayHasKey('descripcion', $centro);
        $this->assertArrayHasKey('parametros_listar', $centro);
        $this->assertArrayHasKey('campos', $centro);
        $this->assertIsString($centro['descripcion']);
        $this->assertIsArray($centro['parametros_listar']);
        $this->assertIsArray($centro['campos']);
        $campos_esperados = [
            'nombre', 'slug', 'imagen', 'descripcion', 'entradas', 'libros', 'poblacion', 'pais', 'contacto_id'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $centro['campos'], "Falta el campo '$campo'");
        }
        foreach ($centro['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }
}
