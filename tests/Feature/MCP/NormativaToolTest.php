<?php

namespace Tests\Feature\MCP;

class NormativaToolTest extends McpFeatureTestCase
{
    public function test_info_normativa()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'normativa']);
        $this->assertIsArray($result);
        $normativa = $result['normativa'] ?? null;
        $this->assertIsArray($normativa);
        $this->assertArrayHasKey('descripcion', $normativa);
        $this->assertArrayHasKey('parametros_listar', $normativa);
        $this->assertArrayHasKey('campos', $normativa);
        $this->assertIsString($normativa['descripcion']);
        $this->assertIsArray($normativa['parametros_listar']);
        $this->assertIsArray($normativa['campos']);
        $campos_esperados = [
            'titulo', 'slug', 'descripcion', 'texto', 'published_at', 'visibilidad'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $normativa['campos'], "Falta el campo '$campo'");
        }
        foreach ($normativa['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }

    public function test_listar_normativas()
    {
        \App\Models\Normativa::truncate();
        for ($i = 0; $i < 2; $i++) {
            \App\Models\Normativa::create([
                'titulo' => 'Normativa ' . $i,
                'slug' => 'normativa-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'texto' => 'Texto ' . $i,
                'published_at' => now()->toDateString(),
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'normativa']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(2, count($result['listado']['data']));
    }

    public function test_ver_normativa()
    {
        \App\Models\Normativa::truncate();
        $normativa = \App\Models\Normativa::create([
            'titulo' => 'Normativa Test',
            'slug' => 'normativa-test-' . uniqid(),
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'published_at' => now()->toDateString(),
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'normativa', 'slug' => $normativa->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('normativa', $result);
        $this->assertEquals($normativa->slug, $result['normativa']['slug'] ?? $result['normativa']->slug ?? null);
    }

    public function test_crear_normativa()
    {
        \App\Models\Normativa::truncate();
        $params = [
            'entidad' => 'normativa',
            'data' => [
                'titulo' => 'Nueva Normativa',
                'slug' => 'nueva-normativa-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'texto' => 'Texto de prueba',
                'published_at' => now()->toDateString(),
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('normativas', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_normativa()
    {
        \App\Models\Normativa::truncate();
        $normativa = \App\Models\Normativa::create([
            'titulo' => 'Editar Normativa',
            'slug' => 'editar-normativa-' . uniqid(),
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'published_at' => now()->toDateString(),
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'normativa',
            'id' => $normativa->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('normativas', ['id' => $normativa->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_normativa()
    {
        \App\Models\Normativa::truncate();
        $normativa = \App\Models\Normativa::create([
            'titulo' => 'Eliminar Normativa',
            'slug' => 'eliminar-normativa-' . uniqid(),
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'published_at' => now()->toDateString(),
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'normativa',
            'id' => $normativa->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('normativas', ['id' => $normativa->id]);
    }
}
