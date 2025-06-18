<?php

namespace Tests\Feature\MCP;

use Tests\Feature\MCP\McpFeatureTestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArchivoToolTest extends McpFeatureTestCase
{
    // public function setUp(): void
    // {
    //     parent::setUp();
    //     // Aquí podrías preparar archivos de prueba en el storage si es necesario
    // }

    public function test_listar_archivos()
    {
        $result = $this->callMcpTool('listar', ['entidad' => 'archivo', 'ruta' => '/editar-equipo-684e772775604']);
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('archivos', $result);
    }

        public function test_info_archivo() {
            $result = $this->callMcpTool('info', ['entidad' => 'archivo' ]);
            fwrite(STDERR, print_r($result, true));
            $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        }

    public function test_ver_archivo()
    {
        $result = $this->callMcpTool('ver', ['entidad' => 'archivo', 'id' => '/archivos/equipos/editar-equipo-684e772775604']);
        fwrite(STDERR, print_r($result, true));
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('archivo', $result);
        $this->assertArrayHasKey('ruta', $result['archivo']);
        $this->assertArrayHasKey('permisos', $result['archivo']);
    }

    public function test_buscar_archivo()
    {
        $result = $this->callMcpTool('buscar', ['entidad' => 'archivo', 'buscar' => 'conseguido']);
        $this->assertIsArray($result, 'La respuesta de MCP no es un array');
        $this->assertArrayHasKey('archivos', $result);
    }

    public function test_crear_y_eliminar_archivo()
    {
        $ruta = '/archivos/personal/public/testfile.txt';
        $contenido = 'Contenido de prueba';
        $crear = $this->callMcpTool('crear', ['entidad' => 'archivo', 'data' => ['ruta' => $ruta, 'contenido' => $contenido]]);
        $this->assertIsArray($crear);
        $this->assertTrue(isset($crear['archivo_creado']) || isset($crear['archivo']), 'No se creó el archivo');
        $eliminar = $this->callMcpTool('eliminar', ['entidad' => 'archivo', 'id' => $ruta]);
        $this->assertIsArray($eliminar);
        $this->assertTrue(isset($eliminar['archivo_borrado']) || isset($eliminar['archivo']), 'No se eliminó el archivo');
    }
}
