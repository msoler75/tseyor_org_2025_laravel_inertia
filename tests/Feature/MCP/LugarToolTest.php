<?php

namespace Tests\Feature\MCP;

class LugarToolTest extends McpFeatureTestCase
{
    public function test_info_lugar()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'lugar']);
        $this->assertIsArray($result);
        $lugar = $result['lugar'] ?? null;
        $this->assertIsArray($lugar);
        $this->assertArrayHasKey('descripcion', $lugar);
        $this->assertArrayHasKey('parametros_listar', $lugar);
        $this->assertArrayHasKey('campos', $lugar);
        $this->assertIsString($lugar['descripcion']);
        $this->assertIsArray($lugar['parametros_listar']);
        $this->assertIsArray($lugar['campos']);
        $campos_esperados = [
            'nombre', 'slug', 'descripcion', 'categoria', 'imagen', 'texto', 'libros', 'relacionados', 'visibilidad'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $lugar['campos'], "Falta el campo '$campo'");
        }
        foreach ($lugar['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }

    public function test_listar_lugares()
    {
        \App\Models\Lugar::truncate();
        for ($i = 0; $i < 2; $i++) {
            \App\Models\Lugar::create([
                'nombre' => 'Lugar ' . $i,
                'slug' => 'lugar-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'categoria' => 'parque',
                'imagen' => '/img/lugar' . $i . '.jpg',
                'texto' => 'Texto ' . $i,
                'libros' => '',
                'relacionados' => '',
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'lugar']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(2, count($result['listado']['data']));
    }

    public function test_ver_lugar()
    {
        \App\Models\Lugar::truncate();
        $lugar = \App\Models\Lugar::create([
            'nombre' => 'Lugar Test',
            'slug' => 'lugar-test-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'parque',
            'imagen' => '/img/lugar-test.jpg',
            'texto' => 'Texto',
            'libros' => '',
            'relacionados' => '',
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'lugar', 'slug' => $lugar->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('lugar', $result);
        $this->assertEquals($lugar->slug, $result['lugar']['slug'] ?? $result['lugar']->slug ?? null);
    }

    public function test_crear_lugar()
    {
        \App\Models\Lugar::truncate();
        $params = [
            'entidad' => 'lugar',
            'data' => [
                'nombre' => 'Nuevo Lugar',
                'slug' => 'nuevo-lugar-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'categoria' => 'parque',
                'imagen' => '/img/nuevo-lugar.jpg',
                'texto' => 'Texto de prueba',
                'libros' => '',
                'relacionados' => '',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('lugares', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_lugar()
    {
        \App\Models\Lugar::truncate();
        $lugar = \App\Models\Lugar::create([
            'nombre' => 'Editar Lugar',
            'slug' => 'editar-lugar-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'parque',
            'imagen' => '/img/editar-lugar.jpg',
            'texto' => 'Texto',
            'libros' => '',
            'relacionados' => '',
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'lugar',
            'id' => $lugar->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('lugares', ['id' => $lugar->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_lugar()
    {
        \App\Models\Lugar::truncate();
        $lugar = \App\Models\Lugar::create([
            'nombre' => 'Eliminar Lugar',
            'slug' => 'eliminar-lugar-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'parque',
            'imagen' => '/img/eliminar-lugar.jpg',
            'texto' => 'Texto',
            'libros' => '',
            'relacionados' => '',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'lugar',
            'id' => $lugar->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('lugares', ['id' => $lugar->id]);
    }
}
