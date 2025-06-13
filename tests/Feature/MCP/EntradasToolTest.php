<?php

namespace Tests\Feature\MCP;

use App\MCP\EntradasTool;
use App\Models\Entrada;
use Tests\TestCase;

class EntradasToolTest extends McpFeatureTestCase
{
    public function test_listar_entradas()
    {
        Entrada::truncate();
        for ($i = 0; $i < 3; $i++) {
            $entrada = new Entrada([
                'titulo' => 'Titulo ' . $i,
                'slug' => 'slug-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'categoria' => 'Pueblo Tseyor',
                'texto' => 'Texto ' . $i,
                'visibilidad' => 'P',
                'published_at' => now(),
            ]);
            $entrada->save();
        }
        $result = $this->callMcpTool('listar_entradas');
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

    public function test_ver_entrada()
    {
        $entrada = new Entrada([
            'titulo' => 'Ver Entrada',
            'slug' => 'ver-entrada-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'Pueblo Tseyor',
            'texto' => 'Texto',
            'visibilidad' => 'P',
            'published_at' => now(),
        ]);
        $entrada->save();
        $result = $this->callMcpTool('ver_entrada', ['slug' => $entrada->slug]);
        if (isset($result['content'][0]['text'])) {
            $json = json_decode($result['content'][0]['text'], true);
            if (is_array($json) && isset($json['entrada'])) {
                $result = $json;
            }
        }
        $this->assertArrayHasKey('entrada', $result);
        $this->assertEquals($entrada->slug, $result['entrada']['slug'] ?? $result['entrada']->slug ?? null);
    }

    public function test_crear_entrada()
    {
        Entrada::truncate();
        $params = [
            'titulo' => 'Test Entrada XXX',
            'descripcion' => 'Descripción de prueba',
            'categoria' => 'Pueblo Tseyor',
            'texto' => 'Texto de prueba',
            'visibilidad' => 'P',
            'published_at' => now()->toDateTimeString(),
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('crear_entrada', $params);
        $this->assertDatabaseHas('entradas', ['slug' => 'test-entrada-xxx']);
    }

    public function test_editar_entrada()
    {
        $entrada = new Entrada([
            'titulo' => 'Original',
            'slug' => 'actualizar-entrada-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'Pueblo Tseyor',
            'texto' => 'Texto',
            'visibilidad' => 'P',
            'published_at' => now(),
        ]);
        $entrada->save();
        $params = [
            'id' => $entrada->id,
            'titulo' => 'Modificado',
            'slug' => $entrada->slug,
            'descripcion' => $entrada->descripcion,
            'categoria' => $entrada->categoria,
            'texto' => $entrada->texto,
            'visibilidad' => $entrada->visibilidad,
            'published_at' => $entrada->published_at?->toDateTimeString(),
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('editar_entrada', $params);
        $this->assertDatabaseHas('entradas', [
            'id' => $entrada->id,
            'titulo' => 'Modificado',
        ]);
    }

    public function test_eliminar_entrada()
    {
        $entrada = new Entrada([
            'titulo' => 'Eliminar Entrada',
            'slug' => 'eliminar-entrada-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'Pueblo Tseyor',
            'texto' => 'Texto',
            'visibilidad' => 'P',
            'published_at' => now(),
        ]);
        $entrada->save();
        $this->callMcpTool('eliminar_entrada', [
            'id' => $entrada->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_todo')
        ]);
        $this->assertDatabaseMissing('entradas', ['id' => $entrada->id]);
    }
}
