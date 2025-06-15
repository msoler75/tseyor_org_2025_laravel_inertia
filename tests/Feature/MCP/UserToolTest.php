<?php

namespace Tests\Feature\MCP;

class UserToolTest extends McpFeatureTestCase
{
    public function test_info_user()
    {
        $result = $this->callMcpTool('info', ['entidad' => 'user']);
        $this->assertIsArray($result);
        $user = $result['user'] ?? null;
        $this->assertIsArray($user);
        $this->assertArrayHasKey('descripcion', $user);
        $this->assertArrayHasKey('parametros_listar', $user);
        $this->assertArrayHasKey('campos', $user);
        $this->assertIsString($user['descripcion']);
        $this->assertIsArray($user['parametros_listar']);
        $this->assertIsArray($user['campos']);
        $campos_esperados = [
            'name', 'slug', 'email', 'frase', 'profile_photo_path', 'roles', 'equipos', 'created_at', 'updated_at'
        ];
        foreach ($campos_esperados as $campo) {
            $this->assertArrayHasKey($campo, $user['campos'], "Falta el campo '$campo'");
        }
        foreach ($user['campos'] as $campo => $definicion) {
            $this->assertArrayHasKey('type', $definicion, "El campo '$campo' no tiene 'type'");
            $this->assertArrayHasKey('description', $definicion, "El campo '$campo' no tiene 'description'");
        }
    }

    public function test_listar_users()
    {
        \App\Models\User::truncate();
        for ($i = 0; $i < 2; $i++) {
            \App\Models\User::create([
                'name' => 'User ' . $i,
                'slug' => 'user-' . $i . '-' . uniqid(),
                'email' => 'user' . $i . '@test.com',
                'password' => bcrypt('password'),
                'frase' => 'Frase ' . $i,
                'profile_photo_path' => '',
            ]);
        }
        $result = $this->callMcpTool('listar', ['entidad' => 'user']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('users', $result);
        $this->assertGreaterThanOrEqual(2, count($result['users']));
    }

    public function test_ver_user()
    {
        \App\Models\User::truncate();
        $user = \App\Models\User::create([
            'name' => 'User Test',
            'slug' => 'user-test-' . uniqid(),
            'email' => 'usertest@test.com',
            'password' => bcrypt('password'),
            'frase' => 'Frase test',
            'profile_photo_path' => '',
        ]);
        $result = $this->callMcpTool('ver', ['entidad'=>'user', 'id' => $user->id]);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals($user->id, $result['user']['id'] ?? $result['user']->id ?? null);
    }

    public function test_crear_user()
    {
        \App\Models\User::truncate();
        $params = [
            'entidad' => 'user',
            'data' => [
                'name' => 'Nuevo User',
                'slug' => 'nuevo-user-' . uniqid(),
                'email' => 'nuevo-user@test.com',
                'password' => bcrypt('password'),
                'frase' => 'Frase nueva',
                'profile_photo_path' => '',
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('crear', $params);
        $this->assertDatabaseHas('users', ['slug' => $params['data']['slug']]);
    }

    public function test_editar_user()
    {
        \App\Models\User::truncate();
        $user = \App\Models\User::create([
            'name' => 'Editar User',
            'slug' => 'editar-user-' . uniqid(),
            'email' => 'editar-user@test.com',
            'password' => bcrypt('password'),
            'frase' => 'Frase editar',
            'profile_photo_path' => '',
        ]);
        $nuevoNombre = 'Nombre Editado';
        $params = [
            'entidad' => 'user',
            'id' => $user->id,
            'data' => [
                'name' => $nuevoNombre
            ],
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('editar', $params);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $nuevoNombre]);
    }

    public function test_eliminar_user()
    {
        \App\Models\User::truncate();
        $user = \App\Models\User::create([
            'name' => 'Eliminar User',
            'slug' => 'eliminar-user-' . uniqid(),
            'email' => 'eliminar-user@test.com',
            'password' => bcrypt('password'),
            'frase' => 'Frase eliminar',
            'profile_photo_path' => '',
        ]);
        $params = [
            'entidad' => 'user',
            'id' => $user->id,
            'force' => true,
            'token' => config('mcp-server.tokens.administrar_contenidos')
        ];
        $this->callMcpTool('eliminar', $params);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
