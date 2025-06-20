<?php

namespace Tests\Feature\MCP;

class PaginaToolTest extends McpFeatureTestCase
{
    public function test_info_pagina()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'pagina']);
        $this->assertIsArray($result);
        $pagina = $result['pagina'] ?? null;
        $this->assertIsArray($pagina);
        $this->assertArrayHasKey('descripcion', $pagina);
        $this->assertArrayHasKey('parametros_listar', $pagina);
        $this->assertArrayHasKey('campos', $pagina);
        $this->assertIsString($pagina['descripcion']);
        $this->assertIsArray($pagina['parametros_listar']);
        $this->assertIsArray($pagina['campos']);
        // Ajustar campos esperados según definición real de info.php si existe
    }

    public function test_listar_paginas()
    {
        \App\Models\Pagina::truncate();
        $pp = \App\Http\Controllers\PaginasController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp + 3; $i++) {
            \App\Models\Pagina::create([
                'titulo' => 'Pagina ' . $i,
                'ruta' => '/ruta-' . $i,
                'atras_ruta' => '/atras-' . $i,
                'atras_texto' => 'Atras ' . $i,
                'descripcion' => 'Desc ' . $i,
                'texto' => 'Texto ' . $i,
                'palabras_clave' => 'clave' . $i,
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'pagina']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'pagina', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(3, count($result['listado']['data']));
        // buscar una página específica
        $result = $this->callMcpTool('listar', ['entidad' => 'pagina', 'buscar' => $pp]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar una página que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'pagina', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_pagina()
    {
        \App\Models\Pagina::truncate();
        $pagina = \App\Models\Pagina::create([
            'titulo' => 'Pagina Test',
            'ruta' => '/test',
            'atras_ruta' => '/atras',
            'atras_texto' => 'Atras',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'palabras_clave' => 'clave',
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad' => 'pagina', 'ruta' => $pagina->ruta]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagina', $result);
        $this->assertEquals($pagina->id, $result['pagina']['id'] ?? $result['pagina']->id ?? null);
    }

    public function test_crear_pagina()
    {
        \App\Models\Pagina::truncate();
        $params = [
            'entidad' => 'pagina',
            'data' => [
                'titulo' => 'Nueva Pagina',
                'ruta' => '/nueva',
                'atras_ruta' => '/atras-nueva',
                'atras_texto' => 'Atras Nueva',
                'descripcion' => 'Descripción de prueba',
                'texto' => 'Texto de prueba',
                'palabras_clave' => 'clave',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('paginas', ['ruta' => $params['data']['ruta']]);
    }

    public function test_editar_pagina()
    {
        \App\Models\Pagina::truncate();
        $pagina = \App\Models\Pagina::create([
            'titulo' => 'Editar Pagina',
            'ruta' => '/editar',
            'atras_ruta' => '/atras-editar',
            'atras_texto' => 'Atras Editar',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'palabras_clave' => 'clave',
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'pagina',
            'id' => $pagina->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('paginas', ['id' => $pagina->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_pagina()
    {
        \App\Models\Pagina::truncate();
        $pagina = \App\Models\Pagina::create([
            'titulo' => 'Eliminar Pagina',
            'ruta' => '/eliminar',
            'atras_ruta' => '/atras-eliminar',
            'atras_texto' => 'Atras Eliminar',
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'palabras_clave' => 'clave',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'pagina',
            'id' => $pagina->id,
            'force' => true,
            'token' => config('mcp-server.tokens.admin')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('paginas', ['id' => $pagina->id]);
    }
}
