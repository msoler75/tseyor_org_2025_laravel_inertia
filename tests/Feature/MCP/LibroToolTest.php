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
        $pp = \App\Http\Controllers\LibrosController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp + 3; $i++) {
            \App\Models\Libro::create([
                'titulo' => 'Libro ' . $i . ($i < $pp+2?' extra' : ''),
                'slug' => 'libro-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'categoria' => 'novela',
                'imagen' => '/img/libro' . $i . '.jpg',
                'edicion' => 1,
                'paginas' => 100 + $i,
                'pdf' => '/pdf/libro' . $i . '.pdf',
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'libro']);
        // fwrite(STDERR, print_r($result, true));
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual($pp, count($result['listado']['data']));

        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'libro', 'page' => 2]);
        // fwrite(STDERR, print_r($result, true));
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(3, count($result['listado']['data']));

        // buscar un libro específico
        $result = $this->callMcpTool('listar', ['entidad' => 'libro', 'buscar' => 'Libro 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar un libro que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'libro', 'buscar' => 'Inexistente']);
        // fwrite(STDERR, print_r($result, true));
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));

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
            'edicion' => 1,
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
                'edicion' => 1,
                'paginas' => 222,
                'pdf' => '/pdf/nuevo-libro.pdf',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
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
            'edicion' => 1,
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
            'token' => config('mcp-server.tokens.administrar_todo')
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
            'edicion' => 1,
            'paginas' => 333,
            'pdf' => '/pdf/eliminar-libro.pdf',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'libro',
            'id' => $libro->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('libros', ['id' => $libro->id]);
    }
}
