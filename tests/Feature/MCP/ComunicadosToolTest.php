<?php

namespace Tests\Feature\MCP;

use App\MCP\ComunicadosTool;
use App\Models\Comunicado;
use Tests\TestCase;

class ComunicadosToolTest extends McpFeatureTestCase
{
    public function test_listar_comunicados2()
    {
        $result = $this->callMcpTool('listar', ['entidad' => 'comunicado']);
        fwrite(STDERR, print_r($result, true));
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
    }


    public function test_listar_comunicados()
    {
        Comunicado::truncate();
        $pp = \App\Http\Controllers\ComunicadosController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp+2; $i++) {
            $comunicado = new Comunicado([
                'titulo' => 'Titulo ' . $i,
                'numero' => $i + 100,
                'categoria' => 1,
                'descripcion' => 'Desc ' . $i,
                'texto' => 'Texto ' . $i,
                'visibilidad' => 'P',
                'fecha_comunicado' => now()->toDateString(),
                'ano' => date('Y'),
                'slug' => 'slug-' . $i . '-' . uniqid(),
            ]);
            $comunicado->save();
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'comunicado']);
        // Si la respuesta viene anidada en content[0][text], decodificar el JSON
        if (isset($result['content'][0]['text'])) {
            $json = json_decode($result['content'][0]['text'], true);
            if (is_array($json) && isset($json['listado'])) {
                $result = $json;
            }
        }
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('listado', $result, 'La respuesta de MCP no contiene la clave listado');
        $this->assertArrayHasKey('data', $result['listado']);
        $this->assertGreaterThanOrEqual($pp, count($result['listado']['data']));

        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'comunicado', 'page' => 2]);
        // fwrite(STDERR, print_r($result, true));
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        // fwrite(STDERR, print_r($result, true));
        $this->assertEquals(2, count($result['listado']['data']));

        // buscar un comunicado específico
        $result = $this->callMcpTool('listar', ['entidad' => 'comunicado', 'buscar' => 'Titulo 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar un comunicado que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'comunicado', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_comunicado()
    {
        Comunicado::truncate();
        $comunicado = new Comunicado([
            'titulo' => 'Ver Comunicado',
            'numero' => 99,
            'categoria' => 1,
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'visibilidad' => 'P',
            'fecha_comunicado' => now()->toDateString(),
            'ano' => date('Y'),
            'slug' => 'ver-comunicado-' . uniqid(),
        ]);
        $comunicado->save();
        $result = $this->callMcpTool('ver', ['entidad' => 'comunicado', 'id' => $comunicado->slug]);
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('comunicado', $result);
        $this->assertEquals($comunicado->slug, $result['comunicado']['slug'] ?? $result['comunicado']->slug ?? null);

        // comprobar que tambien podemos acceder al comunicado por ID
        $resultById = $this->callMcpTool('ver', ['entidad' => 'comunicado', 'id' => $comunicado->id]);
        $this->assertIsArray($resultById, 'La respuesta de MCP por ID no es un array');
        $this->assertArrayHasKey('comunicado', $resultById);
        $this->assertEquals($comunicado->slug, $resultById['comunicado']['slug'] ?? $resultById['comunicado']->slug ?? null);
        // Comprobar que ambos resultados son iguales
        $this->assertEquals($result['comunicado'], $resultById['comunicado'], 'Los resultados por slug e ID no coinciden');
    }

    public function test_crear_comunicado()
    {
        Comunicado::truncate();
        $params = [
            'entidad' => 'comunicado',
            'data' => [
                'titulo' => 'Test Comunicado',
                'numero' => 123,
                'categoria' => 1,
                'descripcion' => 'Descripción de prueba',
                'texto' => 'Texto de prueba',
                'visibilidad' => 'P',
                'fecha_comunicado' => now()->toDateString(),
                'ano' => date('Y'),
                'slug' => 'test-comunicado'
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('comunicados', ['slug' => 'test-comunicado']);
    }

    public function test_editar_comunicado()
    {
        Comunicado::truncate();
        $comunicado = new Comunicado([
            'titulo' => 'Original',
            'numero' => 1,
            'categoria' => 1,
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'visibilidad' => 'P',
            'fecha_comunicado' => now()->toDateString(),
            'ano' => date('Y'),
            'slug' => 'actualizar-comunicado-' . uniqid(),
        ]);
        $comunicado->save();
        $params = [
            'entidad' => 'comunicado',
            'id' => $comunicado->id,
            'data' => [
                'titulo' => 'Modificado',
                'numero' => $comunicado->numero,
                'categoria' => $comunicado->categoria,
                'descripcion' => $comunicado->descripcion,
                'texto' => $comunicado->texto,
                'visibilidad' => $comunicado->visibilidad,
                'fecha_comunicado' => $comunicado->fecha_comunicado,
                'ano' => $comunicado->ano,
                'slug' => $comunicado->slug,
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $result = $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('comunicados', [
            'id' => $comunicado->id,
            'titulo' => 'Modificado',
        ]);
    }

    public function test_eliminar_comunicado()
    {
        Comunicado::truncate();
        $comunicado = new Comunicado([
            'titulo' => 'Eliminar Comunicado',
            'numero' => 9999,
            'categoria' => 1,
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'visibilidad' => 'P',
            'fecha_comunicado' => now()->toDateString(),
            'ano' => date('Y'),
            'slug' => 'eliminar-comunicado-' . uniqid(),
        ]);
        $comunicado->save();
        $this->callMcpTool('eliminar', [
            'entidad' => 'comunicado',
            'id' => $comunicado->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_todo')
        ]);
        $existe = Comunicado::withTrashed()->find($comunicado->id);
        $this->assertDatabaseMissing('comunicados', ['id' => $comunicado->id]);
    }


    // test de campos de comunicado
    public function test_info_comunicado()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'comunicado']);
        $this->assertIsArray($result);
        $comunicado = $result['comunicado'] ?? null;
        $this->assertIsArray($comunicado);
        $this->assertArrayHasKey('descripcion', $comunicado);
        $this->assertArrayHasKey('parametros_listar', $comunicado);
        $this->assertArrayHasKey('campos', $comunicado);
        $this->assertIsString($comunicado['descripcion']);
        $this->assertIsArray($comunicado['parametros_listar']);
        $this->assertIsArray($comunicado['campos']);
        $campos_esperados = [
            'titulo', 'descripcion', 'texto', 'fecha_comunicado', 'categoria', 'ano', 'imagen', 'visibilidad'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $comunicado['campos'], "Falta el campo '$campo'");
        }
        foreach ($comunicado['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }
}
