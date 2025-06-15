<?php

namespace Tests\Feature\MCP;

use App\Models\Contacto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactoToolTest extends McpFeatureTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    }

    public function tearDown(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    public function test_listar_contactos()
    {
        Contacto::truncate();
        for ($i = 0; $i < 2; $i++) {
            Contacto::create([
                'nombre' => 'Contacto ' . $i,
                'slug' => 'contacto-' . $i . '-' . uniqid(),
                'pais' => 'ES',
                'poblacion' => 'Ciudad ' . $i,
                'visibilidad' => 'P',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'contacto']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual(2, count($result['listado']['data']));
    }

    public function test_ver_contacto()
    {
        Contacto::truncate();
        $slug = 'contacto-test-' . uniqid();
        Contacto::create([
                'nombre' => 'Contacto Test',
                'slug' => $slug,
                'pais' => 'ES',
                'poblacion' => 'Ciudad Test',
                'visibilidad' => 'P',
        ]);
        $result = $this->callMcpTool('ver', ['entidad' => 'contacto', 'id' => $slug]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('contacto', $result);
        $this->assertEquals($slug, $result['contacto']['slug'] ?? $result['contacto']->slug ?? null);
    }

    public function test_crear_contacto()
    {
        Contacto::truncate();
        $params = [
            'entidad' => 'contacto',
            'data' => [
                'nombre' => 'Nuevo Contacto',
                'slug' => 'nuevo-contacto-' . uniqid(),
                'pais' => 'ES',
                'poblacion' => 'Ciudad Nueva',
                'visibilidad' => 'P',
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $crear = $this->callMcpTool('crear', $params);
        $contactoId = $crear['contacto_creado']['id'] ?? null;
        $nuevoNombre = 'Nombre Editado';
        $paramsEditar = [
            'entidad' => 'contacto',
            'id' => $contactoId,
            'data' => [
                'nombre' => $nuevoNombre
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $response = $this->callMcpTool('editar', $paramsEditar);
        $this->assertDatabaseHas('contactos', ['id' => $contactoId, 'nombre' => $nuevoNombre]);
    }

    public function test_eliminar_contacto()
    {
        Contacto::truncate();
        $contacto = Contacto::create([
            'nombre' => 'Eliminar Contacto',
            'slug' => 'eliminar-contacto-' . uniqid(),
            'pais' => 'ES',
            'poblacion' => 'Ciudad Eliminar',
        ]);
        $params = [
            'entidad' => 'contacto',
            'id' => $contacto->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('contactos', ['id' => $contacto->id]);
    }

    public function test_info_contacto()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'contacto']);
        $this->assertIsArray($result);
        $contacto = $result['contacto'] ?? null;
        $this->assertIsArray($contacto);
        $this->assertArrayHasKey('descripcion', $contacto);
        $this->assertArrayHasKey('parametros_listar', $contacto);
        $this->assertArrayHasKey('campos', $contacto);
        $this->assertIsString($contacto['descripcion']);
        $this->assertIsArray($contacto['parametros_listar']);
        $this->assertIsArray($contacto['campos']);
        $campos_esperados = [
            'nombre', 'slug', 'imagen', 'pais', 'poblacion', 'provincia', 'direccion', 'codigo', 'telefono', 'social', 'email', 'latitud', 'longitud', 'centro_id', 'user_id', 'visibilidad'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $contacto['campos'], "Falta el campo '$campo'");
        }
        foreach ($contacto['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }
}
