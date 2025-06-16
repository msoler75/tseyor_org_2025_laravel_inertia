<?php

namespace Tests\Feature\MCP;

class EventoToolTest extends McpFeatureTestCase
{
    public function test_info_evento()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'evento']);
        $this->assertIsArray($result);
        $evento = $result['evento'] ?? null;
        $this->assertIsArray($evento);
        $this->assertArrayHasKey('descripcion', $evento);
        $this->assertArrayHasKey('parametros_listar', $evento);
        $this->assertArrayHasKey('campos', $evento);
        $this->assertIsString($evento['descripcion']);
        $this->assertIsArray($evento['parametros_listar']);
        $this->assertIsArray($evento['campos']);
        $campos_esperados = [
            'titulo', 'slug', 'descripcion', 'categoria', 'texto', 'imagen', 'published_at', 'fecha_inicio', 'fecha_fin', 'hora_inicio', 'visibilidad', 'centro_id', 'sala_id', 'equipo_id'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $evento['campos'], "Falta el campo '$campo'");
        }
        foreach ($evento['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }

    public function test_listar_eventos()
    {
        \App\Models\Evento::truncate();
        $pp = \App\Http\Controllers\EventosController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp + 4; $i++) {
            \App\Models\Evento::create([
                'titulo' => 'Evento ' . $i,
                'slug' => 'evento-' . $i . '-' . uniqid(),
                'descripcion' => 'Desc ' . $i,
                'categoria' => 'general',
                'texto' => 'Texto ' . $i,
                'imagen' => null,
                'published_at' => now(),
                'fecha_inicio' => now(),
                'fecha_fin' => now()->addDay(),
                'hora_inicio' => '10:00',
                'visibilidad' => 'P',
                'centro_id' => null,
                'sala_id' => null,
                'equipo_id' => null,
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'evento']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals($pp, count($result['listado']['data']));
        // obtener la página siguiente
        $result = $this->callMcpTool('listar', ['entidad' => 'evento', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(4, count($result['listado']['data']));
        // buscar un evento específico
        $result = $this->callMcpTool('listar', ['entidad' => 'evento', 'buscar' => 'Evento 1']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(1, count($result['listado']['data']));
        // buscar un evento que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'evento', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_evento()
    {
        \App\Models\Evento::truncate();
        $evento = \App\Models\Evento::create([
            'titulo' => 'Evento Test',
            'slug' => 'evento-test-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'texto' => 'Texto',
            'imagen' => null,
            'published_at' => now(),
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDay(),
            'hora_inicio' => '10:00',
            'visibilidad' => 'P',
            'centro_id' => null,
            'sala_id' => null,
            'equipo_id' => null,
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'evento', 'slug' => $evento->slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('evento', $result);
        $this->assertEquals($evento->slug, $result['evento']['slug'] ?? $result['evento']->slug ?? null);
    }

    public function test_crear_evento()
    {
        \App\Models\Evento::truncate();
        $now = now()->format('Y-m-d H:i:s');
        $params = [
            'entidad' => 'evento',
            'data' => [
                'titulo' => 'Nuevo Evento',
                'slug' => 'nuevo-evento-' . uniqid(),
                'descripcion' => 'Descripción de prueba',
                'categoria' => 'test',
                'texto' => 'Texto de prueba',
                'imagen' => null,
                'published_at' => $now,
                'fecha_inicio' => $now,
                'fecha_fin' => null,
                'hora_inicio' => '12:00',
                'visibilidad' => 'P',
                'centro_id' => null,
                'sala_id' => null,
                'equipo_id' => null,
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('eventos', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_evento()
    {
        \App\Models\Evento::truncate();
        $evento = \App\Models\Evento::create([
            'titulo' => 'Editar Evento',
            'slug' => 'editar-evento-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'texto' => 'Texto',
            'imagen' => null,
            'published_at' => now(),
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDay(),
            'hora_inicio' => '10:00',
            'visibilidad' => 'P',
            'centro_id' => null,
            'sala_id' => null,
            'equipo_id' => null,
        ]);
        $nuevaDescripcion = 'Descripción editada';
        $params = [
            'entidad' => 'evento',
            'id' => $evento->id,
            'data' => [
                'descripcion' => $nuevaDescripcion
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('eventos', ['id' => $evento->id, 'descripcion' => $nuevaDescripcion]);
    }

    public function test_eliminar_evento()
    {
        \App\Models\Evento::truncate();
        $evento = \App\Models\Evento::create([
            'titulo' => 'Eliminar Evento',
            'slug' => 'eliminar-evento-' . uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'texto' => 'Texto',
            'imagen' => null,
            'published_at' => now(),
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDay(),
            'hora_inicio' => '10:00',
            'visibilidad' => 'P',
            'centro_id' => null,
            'sala_id' => null,
            'equipo_id' => null,
        ]);
        $params = [
            'entidad' => 'evento',
            'id' => $evento->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('eventos', ['id' => $evento->id]);
    }
}
