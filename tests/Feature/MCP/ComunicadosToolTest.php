<?php

namespace Tests\Feature\MCP;

use App\MCP\ComunicadosTool;
use App\Models\Comunicado;
use Tests\TestCase;

class ComunicadosToolTest extends McpFeatureTestCase
{
    public function test_listar_comunicados()
    {
        Comunicado::truncate();
        for ($i = 0; $i < 3; $i++) {
            $comunicado = new Comunicado([
                'titulo' => 'Titulo ' . $i,
                'numero' => $i + 100, // Usar número único para evitar duplicados
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
        $result = $this->callMcpTool('listar_comunicados');
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
        $this->assertGreaterThanOrEqual(3, count($result['listado']['data']));
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
        $result = $this->callMcpTool('ver_comunicado', ['slug' => $comunicado->slug]);
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('comunicado', $result);
        $this->assertEquals($comunicado->slug, $result['comunicado']['slug'] ?? $result['comunicado']->slug ?? null);

        // comprobar que tambien podemos acceder al comunicado por ID
        $resultById = $this->callMcpTool('ver_comunicado', ['id' => $comunicado->id]);
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
        $this->callMcpTool('crear_comunicado', $params);
        $this->assertDatabaseHas('comunicados', ['slug' => 'test-comunicado']);
    }

    public function test_actualizar_comunicado()
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
        $result = $this->callMcpTool('editar_comunicado', $params);
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
            'numero' => 9999, // Usar número único para evitar duplicados
            'categoria' => 1,
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'visibilidad' => 'P',
            'fecha_comunicado' => now()->toDateString(),
            'ano' => date('Y'),
            'slug' => 'eliminar-comunicado-' . uniqid(),
        ]);
        $comunicado->save();
        $this->callMcpTool('eliminar_comunicado', [
            'id' => $comunicado->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_todo')
        ]);
        // Debug: mostrar si el comunicado sigue existiendo
        $existe = Comunicado::withTrashed()->find($comunicado->id);
        $this->assertDatabaseMissing('comunicados', ['id' => $comunicado->id]);
    }


    // test de campos de comunicado
    public function test_campos_comunicado()
    {
        $result = $this->callMcpTool('campos_comunicado');
        // fwrite(STDERR, print_r($result, true));
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('fields', $result, 'La respuesta de MCP no contiene la clave fields');
        $this->assertGreaterThan(0, count($result['fields']), 'No se han encontrado campos de comunicado');
        // Comprobar que existen los campos esperados
        $campos_esperados = ['titulo', 'descripcion', 'texto', 'fecha_comunicado', 'categoria', 'ano', 'imagen', 'visibilidad'];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $result['fields'], "Falta el campo '$campo'");
        }
        // Comprobar que cada campo tiene 'type' y 'description'
        foreach ($result['fields'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }
}
