<?php

namespace Tests\Feature\MCP;

class UsuarioToolTest extends McpFeatureTestCase
{
    public function test_info_usuario()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'usuario']);
        $this->assertIsArray($result);
        $usuario = $result['usuario'] ?? null;
        $this->assertIsArray($usuario);
        $this->assertArrayHasKey('descripcion', $usuario);
        $this->assertArrayHasKey('parametros_listar', $usuario);
        $this->assertArrayHasKey('campos', $usuario);
        $this->assertIsString($usuario['descripcion']);
        $this->assertIsArray($usuario['parametros_listar']);
        $this->assertIsArray($usuario['campos']);
        $campos_esperados = [
            'name', 'slug', 'email', 'frase', 'profile_photo_path', 'roles', 'equipos', 'created_at', 'updated_at'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $usuario['campos'], "Falta el campo '$campo'");
        }
        foreach ($usuario['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }

    public function test_listar_usuarios()
    {
        // remover foreign key constraints to allow truncation
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\User::truncate();
        $pp = \App\Http\Controllers\UsuariosController::$ITEMS_POR_PAGINA;
        for ($i = 0; $i < $pp+6; $i++) {
            \App\Models\User::create([
                'name' => 'usuario ' . $i,
                'slug' => 'usuario-' . $i . '-' . uniqid(),
                'email' => 'usuario' . $i . '@test.com',
                'password' => bcrypt('password'),
                'frase' => 'Frase ' . $i,
                'profile_photo_path' => '',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'usuario']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertGreaterThanOrEqual($pp, count($result['listado']['data']));
        // pagina 2
        $result = $this->callMcpTool('listar', ['entidad' => 'usuario', 'page' => 2]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(6, count($result['listado']['data']));
        // buscar un usuario específico
        $result = $this->callMcpTool('listar', ['entidad' => 'usuario', 'buscar' => $pp]);
        $this->assertIsArray( $result);
        $this->assertEquals(1, count($result['listado']['data']));
        // buscar un usuario que no existe
        $result = $this->callMcpTool('listar', ['entidad' => 'usuario', 'buscar' => 'Inexistente']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('listado', $result);
        $this->assertEquals(0, count($result['listado']['data']));
    }

    public function test_ver_usuario()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\User::truncate();
        $usuario = \App\Models\User::create([
            'name' => 'usuario Test',
            'slug' => 'usuario-test-' . uniqid(),
            'email' => 'usuariotest@test.com',
            'password' => bcrypt('password'),
            'frase' => 'Frase test',
            'profile_photo_path' => '',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'usuario', 'id' => $usuario->id]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('usuario', $result);
        $this->assertEquals($usuario->id, $result['usuario']['id'] ?? $result['usuario']->id ?? null);
    }

    public function test_crear_usuario()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\User::truncate();
        $params = [
            'entidad' => 'usuario',
            'data' => [
                'name' => 'Nuevo usuario',
                'slug' => 'nuevo-usuario-' . uniqid(),
                'email' => 'nuevo-usuario@test.com',
                'password' => bcrypt('password'),
                'frase' => 'Frase nueva',
                'profile_photo_path' => '',
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('crear', $params);
        // fwrite(STDERR, print_r($result, true));
        $this->assertDatabaseHas('users', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_usuario()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\User::truncate();
        $usuario = \App\Models\User::create([
            'name' => 'Editar usuario',
            'slug' => 'editar-usuario-' . uniqid(),
            'email' => 'editar-usuario@test.com',
            'password' => bcrypt('password'),
            'frase' => 'Frase editar',
            'profile_photo_path' => '',
        ]);
        $nuevoNombre = 'Nombre Editado';
        $params = [
            'entidad' => 'usuario',
            'id' => $usuario->id,
            'data' => [
                'name' => $nuevoNombre
            ],
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('users', ['id' => $usuario->id, 'name' => $nuevoNombre]);
    }

    public function test_eliminar_usuario()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\User::truncate();
        \App\Models\Nodo::truncate();
        $usuario = \App\Models\User::create([
            'name' => 'Eliminar usuario',
            'slug' => 'eliminar-usuario-' . uniqid(),
            'email' => 'eliminar-usuario@test.com',
            'password' => bcrypt('password'),
            'frase' => 'Frase eliminar',
            'profile_photo_path' => '',
        ]);
        $params = [
            'entidad' => 'usuario',
            'id' => $usuario->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_todo')
        ];
        $result = $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
    }
}
