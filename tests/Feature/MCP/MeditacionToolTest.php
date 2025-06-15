<?php

namespace Tests\Feature\MCP;

class MeditacionToolTest extends McpFeatureTestCase
{
    public function test_info_meditacion()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'meditacion']);
        $this->assertIsArray($result);
        $meditacion = $result['meditacion'] ?? null;
        $this->assertIsArray($meditacion);
        $this->assertArrayHasKey('descripcion', $meditacion);
        $this->assertArrayHasKey('parametros_listar', $meditacion);
        $this->assertArrayHasKey('campos', $meditacion);
        $this->assertIsString($meditacion['descripcion']);
        $this->assertIsArray($meditacion['parametros_listar']);
        $this->assertIsArray($meditacion['campos']);
        $campos_esperados = [
            'titulo', 'slug', 'categoria', 'descripcion', 'texto', 'audios', 'visibilidad'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $meditacion['campos'], "Falta el campo '$campo'");
        }
        foreach ($meditacion['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }

    public function test_listar_meditaciones()
    {
        \App\Models\Meditacion::truncate();
        for ($i = 0; $i < 2; $i++) {
            \App\Models\Meditacion::create([
                'titulo' => 'Meditacion ' . $i,
                'slug' => 'meditacion-' . $i . '-' . uniqid(),
                'categoria' => 'relajacion',
                'descripcion' => 'Desc ' . $i,
                'texto' => 'Texto ' . $i,
                'audios' => json_encode([]),
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'meditacion']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(2, count($result['listado']['data']));
    }

    public function test_ver_meditacion()
    {
        \App\Models\Meditacion::truncate();
        $meditacion = \App\Models\Meditacion::create([
            'titulo' => 'Meditacion Test',
            'slug' => 'meditacion-test-' . uniqid(),
            'categoria' => 'relajacion',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'audios' => json_encode([]),
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'meditacion', 'slug' => $meditacion->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('meditacion', $result);
        $this->assertEquals($meditacion->slug, $result['meditacion']['slug'] ?? $result['meditacion']->slug ?? null);
    }

    public function test_crear_meditacion()
    {
        \App\Models\Meditacion::truncate();
        $params = [
            'entidad' => 'meditacion',
            'data' => [
                'titulo' => 'Nueva Meditacion',
                'slug' => 'nueva-meditacion-' . uniqid(),
                'categoria' => 'relajacion',
                'descripcion' => 'Descripción de prueba',
                'texto' => 'Texto de prueba',
                'audios' => json_encode([]),
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('meditaciones', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_meditacion()
    {
        \App\Models\Meditacion::truncate();
        $meditacion = \App\Models\Meditacion::create([
            'titulo' => 'Editar Meditacion',
            'slug' => 'editar-meditacion-' . uniqid(),
            'categoria' => 'relajacion',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'audios' => json_encode([]),
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'meditacion',
            'id' => $meditacion->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('meditaciones', ['id' => $meditacion->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_meditacion()
    {
        \App\Models\Meditacion::truncate();
        $meditacion = \App\Models\Meditacion::create([
            'titulo' => 'Eliminar Meditacion',
            'slug' => 'eliminar-meditacion-' . uniqid(),
            'categoria' => 'relajacion',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'audios' => json_encode([]),
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'meditacion',
            'id' => $meditacion->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('meditaciones', ['id' => $meditacion->id]);
    }
}
