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
        if (!is_array($result) || !array_key_exists('listado', $result)) {
            fwrite(STDERR, "Respuesta MCP listar_comunicados:\n" . print_r($result, true) . "\n");
        }
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('listado', $result, 'La respuesta de MCP no contiene la clave listado');
        $this->assertArrayHasKey('data', $result['listado']);
        $this->assertGreaterThanOrEqual(3, count($result['listado']['data']));
    }

    public function test_ver_comunicado()
    {
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
        if (isset($result['content'][0]['text'])) {
            $json = json_decode($result['content'][0]['text'], true);
            if (is_array($json) && isset($json['comunicado'])) {
                $result = $json;
            }
        }
        $this->assertArrayHasKey('comunicado', $result);
        $this->assertEquals($comunicado->slug, $result['comunicado']['slug'] ?? $result['comunicado']->slug ?? null);
    }

    public function test_crear_comunicado()
    {
        Comunicado::truncate();
        $params = [
            'titulo' => 'Test Comunicado',
            'numero' => 123,
            'categoria' => 1,
            'descripcion' => 'Descripción de prueba',
            'texto' => 'Texto de prueba',
            'visibilidad' => 'P',
            'fecha_comunicado' => now()->toDateString(),
            'ano' => date('Y'),
            'slug' => 'test-comunicado',
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('crear_comunicado', $params);
        $this->assertDatabaseHas('comunicados', ['slug' => 'test-comunicado']);
    }

    public function test_actualizar_comunicado()
    {
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
            'titulo' => 'Modificado',
            'numero' => $comunicado->numero,
            'categoria' => $comunicado->categoria,
            'descripcion' => $comunicado->descripcion,
            'texto' => $comunicado->texto,
            'visibilidad' => $comunicado->visibilidad,
            'fecha_comunicado' => $comunicado->fecha_comunicado,
            'ano' => $comunicado->ano,
            'slug' => $comunicado->slug,
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $result = $this->callMcpTool('editar_comunicado', $params);
        fwrite(STDERR, "Respuesta MCP editar_comunicado:\n" . print_r($result, true) . "\n");
        $this->assertDatabaseHas('comunicados', [
            'id' => $comunicado->id,
            'titulo' => 'Modificado',
        ]);
    }

    public function test_eliminar_comunicado()
    {
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
        $this->assertDatabaseMissing('comunicados', ['id' => $comunicado->id]);
    }
}
