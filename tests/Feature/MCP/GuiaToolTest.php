<?php

namespace Tests\Feature\MCP;

class GuiaToolTest extends McpFeatureTestCase
{
    public function test_info_guia()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'guia']);
        $this->assertIsArray($result);
        $guia = $result['guia'] ?? null;
        $this->assertIsArray($guia);
        $this->assertArrayHasKey('descripcion', $guia);
        $this->assertArrayHasKey('parametros_listar', $guia);
        $this->assertArrayHasKey('campos', $guia);
        $this->assertIsString($guia['descripcion']);
        $this->assertIsArray($guia['parametros_listar']);
        $this->assertIsArray($guia['campos']);
        $campos_esperados = [
            'nombre', 'slug', 'categoria', 'descripcion', 'texto', 'imagen', 'bibliografia', 'libros', 'visibilidad'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $guia['campos'], "Falta el campo '$campo'");
        }
        foreach ($guia['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }

    public function test_listar_guias()
    {
        \App\Models\Guia::truncate();
        $pp = \App\Http\Controllers\GuiasController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp + 2; $i++) {
            \App\Models\Guia::create([
                'nombre' => 'Guia ' . $i,
                'slug' => 'guia-' . $i . '-' . uniqid(),
                'categoria' => 'tecnica',
                'descripcion' => 'Desc ' . $i,
                'texto' => 'Texto ' . $i,
                'imagen' => '/img/guia' . $i . '.jpg',
                'bibliografia' => '',
                'libros' => '',
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'guia']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'guia', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(2, count($result['listado']['data']));
        // buscar una guía específica
        $result = $this->callMcpTool('listar', ['entidad' => 'guia', 'buscar' => 'Guia 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar una guía que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'guia', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_guia()
    {
        \App\Models\Guia::truncate();
        $guia = \App\Models\Guia::create([
            'nombre' => 'Guia Test',
            'slug' => 'guia-test-' . uniqid(),
            'categoria' => 'tecnica',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'imagen' => '/img/guia-test.jpg',
            'bibliografia' => '',
            'libros' => '',
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'guia', 'slug' => $guia->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('guia', $result);
        $this->assertEquals($guia->slug, $result['guia']['slug'] ?? $result['guia']->slug ?? null);
    }

    public function test_crear_guia()
    {
        \App\Models\Guia::truncate();
        $params = [
            'entidad' => 'guia',
            'data' => [
                'nombre' => 'Nueva Guia',
                'slug' => 'nueva-guia-' . uniqid(),
                'categoria' => 'tecnica',
                'descripcion' => 'Descripción de prueba',
                'texto' => 'Texto de prueba',
                'imagen' => '/img/nueva-guia.jpg',
                'bibliografia' => '',
                'libros' => '',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('guias', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_guia()
    {
        \App\Models\Guia::truncate();
        $guia = \App\Models\Guia::create([
            'nombre' => 'Editar Guia',
            'slug' => 'editar-guia-' . uniqid(),
            'categoria' => 'tecnica',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'imagen' => '/img/editar-guia.jpg',
            'bibliografia' => '',
            'libros' => '',
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'guia',
            'id' => $guia->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('guias', ['id' => $guia->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_guia()
    {
        \App\Models\Guia::truncate();
        $guia = \App\Models\Guia::create([
            'nombre' => 'Eliminar Guia',
            'slug' => 'eliminar-guia-' . uniqid(),
            'categoria' => 'tecnica',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'imagen' => '/img/eliminar-guia.jpg',
            'bibliografia' => '',
            'libros' => '',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'guia',
            'id' => $guia->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('guias', ['id' => $guia->id]);
    }
}
