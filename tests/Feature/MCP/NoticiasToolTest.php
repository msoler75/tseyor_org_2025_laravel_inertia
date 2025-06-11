<?php

namespace Tests\Feature\MCP;

use App\MCP\NoticiasTool;
use App\Models\Noticia;
use Tests\TestCase;

class NoticiasToolTest extends TestCase
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
        $result = (new NoticiasTool())->listar();
        $this->assertArrayHasKey('listado', $result);
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
        $result = (new NoticiasTool())->ver(['slug' => $noticia->slug]);
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
            'token' => config('mcp.tokens.administrar_todo')
        ];
        $response = (new NoticiasTool())->crear($params);
        $this->assertDatabaseHas('noticias', ['slug' => 'test-noticia']);
    }

    public function test_actualizar_noticia()
    {
        $noticia = new Noticia([
            'titulo' => 'Original',
            'slug' => 'actualizar-noticia-' . uniqid(),
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
            'token' => config('mcp.tokens.administrar_todo')
        ];
        (new NoticiasTool())->actualizar($params);
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
        (new NoticiasTool())->eliminar([
            'id' => $noticia->id,
            'force' => true,
            'token' => config('mcp.tokens.administrar_todo')
        ]);
        $this->assertDatabaseMissing('noticias', ['id' => $noticia->id]);
    }
}
