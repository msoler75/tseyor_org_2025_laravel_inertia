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
}
