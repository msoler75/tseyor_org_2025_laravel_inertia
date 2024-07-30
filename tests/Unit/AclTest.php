<?php


namespace Tests\Unit;

use App\Models\Acl;
use Illuminate\Support\Facades\Cache;

class AclTest extends NodoTest {


    protected function getAcl($nodo_id, $verbos, $values = [])
    {
        // $faker = \Faker\Factory::create();
        $acl =Acl::where('nodo_id', $nodo_id)->first();
        if (!$acl)
            $acl = Acl::create(['nodo_id' => $nodo_id, 'verbos' => $verbos]);
        $reset_values = [
            'user_id' => null,
            'group_id' => null,
            'verbos' => $verbos,
        ];
        $update = array_merge($reset_values, $values);
        // \Log::info("update nodo {$nodo->id}",['values'=>$values, 'update'=>$update]);
        $acl->update($update);
        return $acl;
    }


    // Prueba el método from para obtener ACLs tanto para usuario como para grupo.

    public function test_acl_from_user()
    {
        $user = $this->getUser("TestUser");
        $grupo = $this->getGrupo("TestGroup");

        //$user->grupos()->detach();
        if(!$user->enGrupo($grupo->id))
            $user->grupos()->attach($grupo->id);

        // remueve todos los acl del usuario $user:
        Acl::where('user_id', $user->id)->delete();

        // remueve todos los acl del grupo $grupo:
        Acl::where('group_id', $grupo->id)->delete();

        $nodo1 = $this->getNodo('/archivos/acl_test1');
        $nodo2 = $this->getNodo('/archivos/acl_test2');
        $nodo3 = $this->getNodo('/archivos/acl_test2.3');

        Acl::create([
            'nodo_id' => $nodo1->id,
            'user_id' => $user->id,
            'verbos' => 'leer,escribir'
        ]);
        Acl::create([
            'nodo_id' => $nodo2->id,
            'group_id' => $grupo->id,
            'verbos' => 'leer,ejecutar'
        ]);

        $acls = Acl::from($user);

        $this->assertCount(2, $acls);
        $this->assertTrue($acls->contains('nodo_id', $nodo1->id));
        $this->assertTrue($acls->contains('nodo_id', $nodo2->id));

        $this->assertFalse($acls->contains('nodo_id', $nodo3->id));
    }


    //  Prueba el método from con verbos específicos
    public function test_acl_from_user_with_verbos()
    {
        $user = $this->getUser("TestUser2");
        $nodo = $this->getNodo('/archivos/acl_test3');

        // remueve todos los acl del usuario $user:
        Acl::where('user_id', $user->id)->delete();

        Acl::create([
            'nodo_id' => $nodo->id,
            'user_id' => $user->id,
            'verbos' => 'leer,escribir'
        ]);

        $this->assertCount(1, Acl::from($user));
        $this->assertCount(0, Acl::from($user, ['ejecutar']));
        $acls = Acl::from($user, ['leer', 'escribir']);
        $this->assertCount(1, $acls);
        $this->assertEquals('leer,escribir', $acls->first()->verbos);
    }

    // Prueba el método inNodes para obtener ACLs para nodos específicos.
    public function test_acl_in_nodes()
    {
        $user1 = $this->getUser("TestUser4");
        $user2 = $this->getUser("TestUser5");
        $grupo = $this->getGrupo("TestGroup2");


        $nodo1 = $this->getNodo('/archivos/acl_test5');
        $nodo2 = $this->getNodo('/archivos/acl_test6');

        // borra todos los acls
        Acl::where('nodo_id', $nodo1->id)->delete();
        Acl::where('nodo_id', $nodo2->id)->delete();
        Acl::where('group_id', $grupo->id)->delete();

        Acl::create([
            'nodo_id' => $nodo1->id,
            'user_id' => $user1->id,
            'verbos' => 'leer,escribir'
        ]);

        Acl::create([
            'nodo_id' => $nodo2->id,
            'group_id' => $grupo->id,
            'verbos' => 'leer,ejecutar'
        ]);

        $acls = Acl::inNodes([$nodo1->id, $nodo2->id]);

        $this->assertCount(2, $acls);
        $this->assertEquals($user1->name, $acls->first()->usuario);
        $this->assertEquals($grupo->nombre, $acls->last()->grupo);
    }


    // Verifica que los accessors funcionan correctamente
    public function test_acl_accessors()
    {
        $user = $this->getUser("TestUser6");
        $grupo = $this->getGrupo("TestGroup3");
        $nodo = $this->getNodo('/archivos/acl_test7');

        // borra todos los acls
        Acl::where('nodo_id', $nodo->id)->delete();

        $acl = Acl::create([
            'nodo_id' => $nodo->id,
            'user_id' => $user->id,
            'group_id' => $grupo->id,
            'verbos' => 'leer'
        ]);

        \Log::info('acl', ['nodo'=>$nodo->toArray(), 'acl'=>$acl->toArray()]);

        $this->assertEquals($nodo->ubicacion, $acl->ruta_nodo);
        $this->assertEquals($user->name, $acl->nombre_usuario);
        $this->assertEquals($grupo->nombre, $acl->nombre_grupo);
    }


    /**
     * Prueba la obtención de ACLs para un usuario sin permisos
     */
    public function test_acl_from_user_without_permissions()
    {
        $user = $this->getUser("UserWithoutPermissions");

        // Asegurarse de que no hay ACLs para este usuario
        Acl::where('user_id', $user->id)->delete();

        $acls = Acl::from($user);

        $this->assertCount(0, $acls);
    }

    /**
     * Prueba la obtención de ACLs para un usuario con múltiples grupos
     */
    public function test_acl_from_user_with_multiple_groups()
    {
        $user = $this->getUser("MultiGroupUser");
        $grupo1 = $this->getGrupo("Group1");
        $grupo2 = $this->getGrupo("Group2");
        $nodo1 = $this->getNodo('/archivos/acl/group1');
        $nodo2 = $this->getNodo('/archivos/acl/group2');

        $user->grupos()->sync([$grupo1->id, $grupo2->id]);

        // borra todos los acl de estos grupos
        Acl::whereIn('group_id', [$grupo1->id, $grupo2->id])->delete();

        Acl::create([
            'nodo_id' => $nodo1->id,
            'group_id' => $grupo1->id,
            'verbos' => 'leer'
        ]);

        Acl::create([
            'nodo_id' => $nodo2->id,
            'group_id' => $grupo2->id,
            'verbos' => 'escribir'
        ]);

        $acls = Acl::from($user);

        $this->assertCount(2, $acls);
        $this->assertTrue($acls->contains('nodo_id', $nodo1->id));
        $this->assertTrue($acls->contains('nodo_id', $nodo2->id));
    }

    /**
     * Prueba la obtención de ACLs a través de verbos 
     */
    public function test_acl_user_verbos()
    {
        $user = $this->getUser("PriorityUser");
        $grupo = $this->getGrupo("PriorityGroup");
        $nodo = $this->getNodo('/archivos/priority_test');

        $user->grupos()->sync([$grupo->id]);

        // borra todos los acls de este usuario y grupo
        Acl::where('user_id', $user->id)->delete();
        Acl::where('group_id', $grupo->id)->delete();


        Acl::create([
            'nodo_id' => $nodo->id,
            'group_id' => $grupo->id,
            'verbos' => 'leer'
        ]);

        Acl::create([
            'nodo_id' => $nodo->id,
            'user_id' => $user->id,
            'verbos' => 'leer,escribir'
        ]);

        $acls = Acl::from($user, ['escribir']);

        $this->assertCount(1, $acls);
        $this->assertEquals('leer,escribir', $acls->first()->verbos);
    }

    /**
     * Prueba la obtención de ACLs con caché
     */
    public function test_acl_caching()
    {
        $user = $this->getUser("CacheUser");
        $nodo = $this->getNodo('/archivos/cache_test');

        Acl::where('user_id', $user->id)->delete();
        Acl::where('nodo_id', $nodo->id)->delete();

        // borramos toda la cache
        Cache::flush();


        Acl::create([
            'nodo_id' => $nodo->id,
            'user_id' => $user->id,
            'verbos' => 'leer'
        ]);

        // Primera llamada, debería guardar en caché
        $acls1 = Acl::from($user);

        // Modificamos el ACL
        Acl::where('user_id', $user->id)->update(['verbos' => 'leer,escribir']);

        // Segunda llamada, debería obtener de caché
        $acls2 = Acl::from($user);

        $this->assertEquals($acls1, $acls2);
        $this->assertEquals('leer', $acls2->first()->verbos);

        // Limpiamos la caché
        Cache::flush();

        // Tercera llamada, debería obtener los datos actualizados
        $acls3 = Acl::from($user);

        $this->assertNotEquals($acls2, $acls3);
        $this->assertEquals('leer,escribir', $acls3->first()->verbos);
    }

    /**
     * Prueba la obtención de ACLs para nodos que no existen
     */
    public function test_acl_for_non_existent_nodes()
    {
        $nonExistentNodeId = 99999; // Asumiendo que este ID no existe

        $acls = Acl::inNodes([$nonExistentNodeId]);

        $this->assertCount(0, $acls);
    }

    /**
     * Prueba la creación y actualización de ACLs
     */
    public function test_acl_create_and_update()
    {
        $user = $this->getUser("UpdateUser");
        $nodo = $this->getNodo('/archivos/update_test');
        
        Acl::where('user_id', $user->id)->delete();

        $acl = Acl::create([
            'nodo_id' => $nodo->id,
            'user_id' => $user->id,
            'verbos' => 'leer'
        ]);

        $this->assertEquals('leer', $acl->verbos);

        $acl->update(['verbos' => 'leer,escribir']);

        $this->assertEquals('leer,escribir', $acl->fresh()->verbos);
    }



}
