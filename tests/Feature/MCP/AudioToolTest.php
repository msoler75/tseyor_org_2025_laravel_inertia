<?php

namespace Tests\Feature\MCP;

use App\Models\Audio;

class AudioToolTest extends McpFeatureTestCase
{
    public function test_listar_audios()
    {
        Audio::truncate();
        for ($i = 0; $i < 2; $i++) {
            Audio::create([
                'titulo' => 'Audio ' . $i,
                'slug' => 'audio-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'categoria' => 'general',
                'audio' => '/almacen/audio' . $i . '.mp3',
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'audio']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(2, count($result['listado']['data']));
    }

    public function test_ver_audio()
    {
        Audio::truncate();
        $audio = Audio::create([
            'titulo' => 'Audio Test',
            'slug' => 'audio-test-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'audio' => '/almacen/audio-test.mp3',
            'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'audio', 'slug' => $audio->slug]);
        // fwrite(STDERR, print_r($result, true)); // Mostrar la respuesta real
        $this->assertIsArray($result);
        $this->assertArrayHasKey('audio', $result);
        $this->assertEquals($audio->slug, $result['audio']['slug'] ?? $result['audio']->slug ?? null);
    }

    public function test_crear_audio()
    {
        Audio::truncate();
        $params = [
            'entidad' => 'audio',
            'data' => [
                'titulo' => 'Nuevo Audio',
                'slug' => 'nuevo-audio-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'categoria' => 'test',
                'audio' => '/almacen/nuevo-audio.mp3',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('audios', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_audio()
    {
        Audio::truncate();
        $audio = Audio::create([
            'titulo' => 'Editar Audio',
            'slug' => 'editar-audio-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'audio' => '/almacen/editar-audio.mp3',
            'visibilidad' => 'P',
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'audio',
            'id' => $audio->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('audios', ['id' => $audio->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_audio()
    {
        Audio::truncate();
        $audio = Audio::create([
            'titulo' => 'Eliminar Audio',
            'slug' => 'eliminar-audio-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'audio' => '/almacen/eliminar-audio.mp3',
            'visibilidad' => 'P',
        ]);
        $params = [
            'entidad' => 'audio',
            'id' => $audio->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('audios', ['id' => $audio->id]);
    }

    public function test_info_audio()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'audio']);
        $this->assertIsArray($result);
        $audio = $result['audio'] ?? null;
        $this->assertArrayHasKey('descripcion', $audio);
        $this->assertArrayHasKey('parametros_listar', $audio);
        $this->assertArrayHasKey('campos', $audio);
        $this->assertIsString($audio['descripcion']);
        $this->assertIsArray($audio['parametros_listar']);
        $this->assertIsArray($audio['campos']);
    }
}
