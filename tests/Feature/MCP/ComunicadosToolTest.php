<?php

namespace Tests\Feature\MCP;

use App\MCP\ComunicadosTool;
use App\Models\Comunicado;
use Tests\TestCase;

class ComunicadosToolTest extends TestCase
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
        $result = (new ComunicadosTool())->listar();
        $this->assertArrayHasKey('listado', $result);
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
        $result = (new ComunicadosTool())->ver(['slug' => $comunicado->slug]);
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
            'token' => config('mcp.tokens.administrar_todo')
        ];
        $response = (new ComunicadosTool())->crear($params);
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
            'token' => config('mcp.tokens.administrar_todo')
        ];
        (new ComunicadosTool())->actualizar($params);
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
        (new ComunicadosTool())->eliminar([
            'id' => $comunicado->id,
            'force' => true,
            'token' => config('mcp.tokens.administrar_todo')
        ]);
        $this->assertDatabaseMissing('comunicados', ['id' => $comunicado->id]);
    }
}
