<?php

namespace Tests\Feature\MCP;

use App\MCP\EntradasTool;
use App\Models\Entrada;
use Tests\TestCase;

class EntradasToolTest extends TestCase
{
    public function test_listar_entradas()
    {
        Entrada::truncate();
        // Crear entradas manualmente
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
        $result = (new EntradasTool())->listar();
        $this->assertArrayHasKey('listado', $result);
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
        $result = (new EntradasTool())->ver(['slug' => $entrada->slug]);
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
            // añadimos el token de MCP para simular la autorización
            'token' => config('mcp.tokens.administrar_todo')
        ];
        $response = (new EntradasTool())->crear($params);
        $this->assertDatabaseHas('entradas', ['slug' => 'test-entrada-xxx']);
    }


    public function test_actualizar_entrada()
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
            'token' => config('mcp.tokens.administrar_todo')
        ];
        (new EntradasTool())->actualizar($params);
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
        (new EntradasTool())->eliminar([
            'id' => $entrada->id,
            'force' => true, // Para eliminar definitivamente
            'token' => config('mcp.tokens.administrar_todo')
        ]);
        $this->assertDatabaseMissing('entradas', ['id' => $entrada->id]);
    }
}
