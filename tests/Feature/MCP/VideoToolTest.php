<?php

namespace Tests\Feature\MCP;

class VideoToolTest extends McpFeatureTestCase
{
    public function test_info_video()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'video']);
        $this->assertIsArray($result);
        $video = $result['video'] ?? null;
        $this->assertIsArray($video);
        $this->assertArrayHasKey('descripcion', $video);
        $this->assertArrayHasKey('parametros_listar', $video);
        $this->assertArrayHasKey('campos', $video);
        $this->assertIsString($video['descripcion']);
        $this->assertIsArray($video['parametros_listar']);
        $this->assertIsArray($video['campos']);
        // Ajustar campos esperados según definición real de info.php si existe
    }

    public function test_listar_videos()
    {
        \App\Models\Video::truncate();
        $pp = \App\Http\Controllers\VideosController::$ITEMS_POR_PAGINA;
        // crear algunos videos de prueba
        for ($i = 0; $i < $pp + 4; $i++) {
            \App\Models\Video::create([
                'titulo' => 'Video ' . $i,
                'slug' => 'video-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'enlace' => 'https://video' . $i . '.com',
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'video']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'video', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(4, count($result['listado']['data']));
        // buscar un video específico
        $result = $this->callMcpTool('listar', ['entidad' => 'video', 'buscar' => 'Video 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar un video que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'video', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_video()
    {
        \App\Models\Video::truncate();
        $video = \App\Models\Video::create([
            'titulo' => 'Video Test',
            'slug' => 'video-test-' . uniqid(),
            'descripcion' => 'Desc',
            'enlace' => 'https://video-test.com',
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'video', 'slug' => $video->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('video', $result);
        $this->assertEquals($video->slug, $result['video']['slug'] ?? $result['video']->slug ?? null);
    }

    public function test_crear_video()
    {
        \App\Models\Video::truncate();
        $params = [
            'entidad' => 'video',
            'data' => [
                'titulo' => 'Nuevo Video',
                'slug' => 'nuevo-video-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'enlace' => 'https://nuevo-video.com',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('videos', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_video()
    {
        \App\Models\Video::truncate();
        $video = \App\Models\Video::create([
            'titulo' => 'Editar Video',
            'slug' => 'editar-video-' . uniqid(),
            'descripcion' => 'Desc',
            'enlace' => 'https://editar-video.com',
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'video',
            'id' => $video->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('videos', ['id' => $video->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_video()
    {
        \App\Models\Video::truncate();
        $video = \App\Models\Video::create([
            'titulo' => 'Eliminar Video',
            'slug' => 'eliminar-video-' . uniqid(),
            'descripcion' => 'Desc',
            'enlace' => 'https://eliminar-video.com',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'video',
            'id' => $video->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }
}
