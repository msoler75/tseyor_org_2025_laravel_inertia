<?php

namespace Tests\Feature\MCP;

use App\Http\Controllers\EquiposController;
use App\Models\Equipo;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EquipoToolTest extends McpFeatureTestCase
{
    use DatabaseTransactions;

    public function test_listar_equipos()
    {
        $pp = EquiposController::$ITEMS_POR_PAGINA;
        // Datos base sembrados por setup-test-db.sh (equipo 1) — cuentan en las páginas
        $base = Equipo::count();
        Equipo::withoutEvents(function () use ($pp) {
            for ($i = 0; $i < $pp + 3; $i++) {
                Equipo::create([
                    'nombre' => 'Equipo '.$i.($i < ($pp + 2) ? ' extra' : ''),
                    'slug' => 'equipo-'.$i.'-'.uniqid(),
                    'descripcion' => 'Desc '.$i,
                    'categoria' => 'general',
                    'imagen' => '/almacen/equipo'.$i.'.jpg',
                ]);
            }
        });
        $this->makeAllSearchable(Equipo::class);
        $result = $this->callMcpTool('listar', ['entidad' => 'equipo']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'equipo', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(3 + $base, count($result['listado']['data']));
        // buscar un equipo específico
        $result = $this->callMcpTool('listar', ['entidad' => 'equipo', 'buscar' => 'Equipo 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar un equipo que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'equipo', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_equipo()
    {
        $equipo = Equipo::create([
            'nombre' => 'Equipo Test',
            'slug' => 'equipo-test-'.uniqid(),
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
        $params = [
            'entidad' => 'equipo',
            'data' => [
                'nombre' => 'Nuevo Equipo',
                'slug' => 'nuevo-equipo-'.uniqid(),
                'descripcion' => 'Descripción de prueba',
                'categoria' => 'test',
                'imagen' => '/almacen/nuevo-equipo.jpg',
            ],
            'token' => config('mcp-server.tokens.admin'),
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('equipos', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_equipo()
    {
        $equipo = Equipo::create([
            'nombre' => 'Editar Equipo',
            'slug' => 'editar-equipo-'.uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'imagen' => '/almacen/editar-equipo.jpg',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'equipo',
            'id' => $equipo->id,
            'data' => [
                'descripcion' => $nuevaDescripcion,
            ],
            'token' => config('mcp-server.tokens.admin'),
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('equipos', ['id' => $equipo->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_equipo()
    {
        // El hook created de Equipo crea grupo + carpeta automáticamente, por lo
        // que un equipo recién creado SIEMPRE tiene carpetas asociadas y el
        // checkDeleteable bloquea la eliminación. Verificamos ese comportamiento.
        $equipo = Equipo::create([
            'nombre' => 'Eliminar Equipo',
            'slug' => 'eliminar-equipo-'.uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'imagen' => '/almacen/eliminar-equipo.jpg',
        ]);
        $params = [
            'entidad' => 'equipo',
            'id' => $equipo->id,
            'force' => true,
            'token' => config('mcp-server.tokens.admin'),
        ];
        $result = $this->callMcpTool('eliminar', $params);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('carpetas asociadas', $result['error']);
        $this->assertDatabaseHas('equipos', ['id' => $equipo->id]);
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
            'nombre', 'slug', 'descripcion', 'imagen', 'categoria', 'group_id', 'anuncio', 'reuniones', 'informacion', 'oculto', 'ocultarCarpetas', 'ocultarArchivos', 'ocultarMiembros', 'ocultarSolicitudes',
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
