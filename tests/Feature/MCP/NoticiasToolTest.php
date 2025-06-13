<?php

namespace Tests\Feature\MCP;

use App\MCP\NoticiasTool;
use App\Models\Noticia;
use Tests\TestCase;

class NoticiasToolTest extends McpFeatureTestCase
{
    public function test_listar_noticias()
    {
        Noticia::truncate();
        for ($i = 0; $i < 3; $i++) {
            $noticia = new Noticia([
                'titulo' => 'Titulo ' . $i,
                'slug' => 'slug-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'texto' => 'Texto ' . $i,
                'visibilidad' => 'P',
                'published_at' => now(),
            ]);
            $noticia->save();
        }
        $result = $this->callMcpTool('listar_noticias');
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

    public function test_ver_noticia()
    {
        $noticia = new Noticia([
            'titulo' => 'Ver Noticia',
            'slug' => 'ver-noticia-' . uniqid(),
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'visibilidad' => 'P',
            'published_at' => now(),
        ]);
        $noticia->save();
        $result = $this->callMcpTool('ver_noticia', ['slug' => $noticia->slug]);
        if (isset($result['content'][0]['text'])) {
            $json = json_decode($result['content'][0]['text'], true);
            if (is_array($json) && isset($json['noticia'])) {
                $result = $json;
            }
        }
        $this->assertArrayHasKey('noticia', $result);
        $this->assertEquals($noticia->slug, $result['noticia']['slug'] ?? $result['noticia']->slug ?? null);
    }

    public function test_crear_noticia()
    {
        Noticia::truncate();
        $params = [
            'titulo' => 'Test Noticia',
            'slug' => 'test-noticia',
            'descripcion' => 'Descripción de prueba',
            'texto' => 'Texto de prueba',
            'visibilidad' => 'P',
            'published_at' => now()->toDateTimeString(),
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('crear_noticia', $params);
        $this->assertDatabaseHas('noticias', ['slug' => 'test-noticia']);
    }

    public function test_editar_noticia()
    {
        $noticia = new Noticia([
            'titulo' => 'Original',
            'slug' => 'editar-noticia-' . uniqid(),
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'visibilidad' => 'P',
            'published_at' => now(),
        ]);
        $noticia->save();
        $params = [
            'id' => $noticia->id,
            'titulo' => 'Modificado',
            'slug' => $noticia->slug,
            'descripcion' => $noticia->descripcion,
            'texto' => $noticia->texto,
            'visibilidad' => $noticia->visibilidad,
            'published_at' => $noticia->published_at?->toDateTimeString(),
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('editar_noticia', $params);
        $this->assertDatabaseHas('noticias', [
            'id' => $noticia->id,
            'titulo' => 'Modificado',
        ]);
    }

    public function test_eliminar_noticia()
    {
        $noticia = new Noticia([
            'titulo' => 'Eliminar Noticia',
            'slug' => 'eliminar-noticia-' . uniqid(),
            'descripcion' => 'Desc',
            'texto' => 'Texto',
            'visibilidad' => 'P',
            'published_at' => now(),
        ]);
        $noticia->save();
        $this->callMcpTool('eliminar_noticia', [
            'id' => $noticia->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_todo')
        ]);
        $this->assertDatabaseMissing('noticias', ['id' => $noticia->id]);
    }
}
