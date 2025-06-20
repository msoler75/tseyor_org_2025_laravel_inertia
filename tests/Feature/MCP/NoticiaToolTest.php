<?php

namespace Tests\Feature\MCP;

class NoticiaToolTest extends McpFeatureTestCase
{
    public function test_info_noticia()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'noticia']);
        $this->assertIsArray($result);
        $noticia = $result['noticia'] ?? null;
        $this->assertIsArray($noticia);
        $this->assertArrayHasKey('descripcion', $noticia);
        $this->assertArrayHasKey('parametros_listar', $noticia);
        $this->assertArrayHasKey('campos', $noticia);
        $this->assertIsString($noticia['descripcion']);
        $this->assertIsArray($noticia['parametros_listar']);
        $this->assertIsArray($noticia['campos']);
        $campos_esperados = [
            'titulo', 'descripcion', 'texto', 'published_at', 'imagen', 'visibilidad'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $noticia['campos'], "Falta el campo '$campo'");
        }
        foreach ($noticia['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }

    public function test_listar_noticias()
    {
        \App\Models\Noticia::truncate();
        $pp = \App\Http\Controllers\NoticiasController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp*2 + 5; $i++) {
            \App\Models\Noticia::create([
                'titulo' => 'Noticia ' . $i . ($i < $pp + 3 ? '' : ' extra'),
                'descripcion' => 'Desc ' . $i,
                'texto' => 'Texto ' . $i,
                'published_at' => now()->toDateString(),
                'imagen' => '/img/noticia' . $i . '.jpg',
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'noticia']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'noticia', 'page' => 3]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(5, count($result['listado']['data']));
        // buscar una noticia específica
        $result = $this->callMcpTool('listar', ['entidad' => 'noticia', 'buscar' => 'Noticia 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar una noticia que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'noticia', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_noticia()
    {
        \App\Models\Noticia::truncate();
        $noticia = \App\Models\Noticia::create([
            'titulo' => 'Noticia Test',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'published_at' => now()->toDateString(),
            'imagen' => '/img/noticia-test.jpg',
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'noticia', 'id' => $noticia->id]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('noticia', $result);
        $this->assertEquals($noticia->id, $result['noticia']['id'] ?? $result['noticia']->id ?? null);
    }

    public function test_crear_noticia()
    {
        \App\Models\Noticia::truncate();
        $params = [
            'entidad' => 'noticia',
            'data' => [
                'titulo' => 'Nueva Noticia',
                'descripcion' => 'Descripción de prueba',
                'texto' => 'Texto de prueba',
                'published_at' => now()->toDateString(),
                'imagen' => '/img/nueva-noticia.jpg',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('noticias', ['titulo' => $params['data']['titulo']]);
    }

    public function test_editar_noticia()
    {
        \App\Models\Noticia::truncate();
        $noticia = \App\Models\Noticia::create([
            'titulo' => 'Editar Noticia',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'published_at' => now()->toDateString(),
            'imagen' => '/img/editar-noticia.jpg',
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'noticia',
            'id' => $noticia->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('noticias', ['id' => $noticia->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_noticia()
    {
        \App\Models\Noticia::truncate();
        $noticia = \App\Models\Noticia::create([
            'titulo' => 'Eliminar Noticia',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'published_at' => now()->toDateString(),
            'imagen' => '/img/eliminar-noticia.jpg',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'noticia',
            'id' => $noticia->id,
            'force' => true,
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('noticias', ['id' => $noticia->id]);
    }
}
