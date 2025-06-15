<?php

namespace Tests\Feature\MCP;

class LibroToolTest extends McpFeatureTestCase
{
    public function test_info_libro()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'libro']);
        $this->assertIsArray($result);
        $libro = $result['libro'] ?? null;
        $this->assertIsArray($libro);
        $this->assertArrayHasKey('descripcion', $libro);
        $this->assertArrayHasKey('parametros_listar', $libro);
        $this->assertArrayHasKey('campos', $libro);
        $this->assertIsString($libro['descripcion']);
        $this->assertIsArray($libro['parametros_listar']);
        $this->assertIsArray($libro['campos']);
        $campos_esperados = [
            'titulo', 'slug', 'descripcion', 'categoria', 'imagen', 'edicion', 'paginas', 'pdf', 'visibilidad'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $libro['campos'], "Falta el campo '$campo'");
        }
        foreach ($libro['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }

    public function test_listar_libros()
    {
        \App\Models\Libro::truncate();
        for ($i = 0; $i < 2; $i++) {
            \App\Models\Libro::create([
                'titulo' => 'Libro ' . $i,
                'slug' => 'libro-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'categoria' => 'novela',
                'imagen' => '/img/libro' . $i . '.jpg',
                'edicion' => '1a',
                'paginas' => 100 + $i,
                'pdf' => '/pdf/libro' . $i . '.pdf',
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'libro']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(2, count($result['listado']['data']));
    }

    public function test_ver_libro()
    {
        \App\Models\Libro::truncate();
        $libro = \App\Models\Libro::create([
            'titulo' => 'Libro Test',
            'slug' => 'libro-test-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'novela',
            'imagen' => '/img/libro-test.jpg',
            'edicion' => '1a',
            'paginas' => 123,
            'pdf' => '/pdf/libro-test.pdf',
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'libro', 'slug' => $libro->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('libro', $result);
        $this->assertEquals($libro->slug, $result['libro']['slug'] ?? $result['libro']->slug ?? null);
    }

    public function test_crear_libro()
    {
        \App\Models\Libro::truncate();
        $params = [
            'entidad' => 'libro',
            'data' => [
                'titulo' => 'Nuevo Libro',
                'slug' => 'nuevo-libro-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'categoria' => 'novela',
                'imagen' => '/img/nuevo-libro.jpg',
                'edicion' => '1a',
                'paginas' => 222,
                'pdf' => '/pdf/nuevo-libro.pdf',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('libros', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_libro()
    {
        \App\Models\Libro::truncate();
        $libro = \App\Models\Libro::create([
            'titulo' => 'Editar Libro',
            'slug' => 'editar-libro-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'novela',
            'imagen' => '/img/editar-libro.jpg',
            'edicion' => '1a',
            'paginas' => 111,
            'pdf' => '/pdf/editar-libro.pdf',
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'libro',
            'id' => $libro->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('libros', ['id' => $libro->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_libro()
    {
        \App\Models\Libro::truncate();
        $libro = \App\Models\Libro::create([
            'titulo' => 'Eliminar Libro',
            'slug' => 'eliminar-libro-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'novela',
            'imagen' => '/img/eliminar-libro.jpg',
            'edicion' => '1a',
            'paginas' => 333,
            'pdf' => '/pdf/eliminar-libro.pdf',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'libro',
            'id' => $libro->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('libros', ['id' => $libro->id]);
    }
}
