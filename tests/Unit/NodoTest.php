<?php

namespace Tests\Unit;

use App\Models\Nodo;
use App\Models\User;
use App\Models\Grupo;
use App\Policies\NodoPolicy;

class NodoTest extends BaseTest
{
    protected $nodoPolicy;

    protected function setUp(): void
    {
        parent::setUp();

        // comprobamos que existe el usuario admin y es el user_id 1
        $admin = User::find(1);

        $this->assertNotNull($admin);
        $this->assertEquals("admin", $admin->name);


        // comprobamos que existe el grupo 1 de administradores
        $adminGroup =  Grupo::find(1);

        $this->assertNotNull($adminGroup);
        $this->assertEquals("administradores", $adminGroup->nombre);

        $this->nodoPolicy = new NodoPolicy();
    }

    // Verifica que el propietario del nodo tiene todos los permisos (lectura, escritura y ejecución) cuando los permisos son 755.
    public function test_permisos_propietario()
    {
        // correct this code:
        $propietario = $this->getUser("Propietario");
        $this->assertEquals("Propietario", $propietario->name);

        $grupo = $this->getGrupo("test1");

        // asociar el grupo al propietario
        if(!$propietario->enGrupo($grupo->id))
            $propietario->grupos()->attach($grupo->id);

        $this->assertNotNull($propietario->grupos()->where('grupos.id', $grupo->id)->first());

        $nodo = $this->getNodo('/archivos/test1', ['user_id' => $propietario->id, 'group_id' => $grupo->id]);

        // probamos diferentes permisos

        // sin permisos
        $nodo->update(['permisos' => '1000']);
        $this->assertFalse($this->nodoPolicy->leer($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($propietario, $nodo));

        // todos los permisos para propietario
        $nodo->update(['permisos' => '1700']);
        $this->assertTrue($this->nodoPolicy->leer($propietario, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir($propietario, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar($propietario, $nodo));

        // --x------
        $nodo->update(['permisos' => '1100']);
        $this->assertFalse($this->nodoPolicy->leer($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($propietario, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar($propietario, $nodo));

        // -w-------
        $nodo->update(['permisos' => '1200']);
        $this->assertFalse($this->nodoPolicy->leer($propietario, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($propietario, $nodo));

        // r--------
        $nodo->update(['permisos' => '1400']);
        $this->assertTrue($this->nodoPolicy->leer($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($propietario, $nodo));


        // r-x------
        $nodo->update(['permisos' => '1500']);
        $this->assertTrue($this->nodoPolicy->leer($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($propietario, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar($propietario, $nodo));

        // rw-------
        $nodo->update(['permisos' => '1600']);
        $this->assertTrue($this->nodoPolicy->leer($propietario, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($propietario, $nodo));

        // sticky bit a 0
        $nodo->update(['permisos' => '0000']);
        $this->assertFalse($this->nodoPolicy->leer($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($propietario, $nodo));

        // sin sticky bit definido
        $nodo->update(['permisos' => '400']);
        $this->assertTrue($this->nodoPolicy->leer($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($propietario, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($propietario, $nodo));

        // permisos de lectura solo para grupo
        $nodo->update(['permisos' => '1070']);
        $this->assertTrue($this->nodoPolicy->leer($propietario, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir($propietario, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar($propietario, $nodo));
    }

    // Comprueba que un miembro del grupo tiene los permisos correctos (lectura y ejecución, pero no escritura) cuando los permisos son 750.
    public function test_permisos_grupo()
    {
        $propietario = $this->getUser("Propietario");
        $miembroGrupo = $this->getUser("MiembroGrupo");
        $grupo = $this->getGrupo("test2");

        $nodo = $this->getNodo('/archivos/test2', [
            'user_id' => $propietario->id,
            'group_id' => $grupo->id,
        ]);

        // Asociamos el usuario al grupo
        if(!$miembroGrupo->enGrupo($grupo->id))
            $miembroGrupo->grupos()->attach($grupo->id);

        $nodo->update(['permisos' => '1070']);
        $this->assertTrue($this->nodoPolicy->leer($miembroGrupo, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir($miembroGrupo, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar($miembroGrupo, $nodo));

        $nodo->update(['permisos' => '1000']);
        $this->assertFalse($this->nodoPolicy->leer($miembroGrupo, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($miembroGrupo, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($miembroGrupo, $nodo));


        $nodo->update(['permisos' => '1010']);
        $this->assertFalse($this->nodoPolicy->leer($miembroGrupo, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($miembroGrupo, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar($miembroGrupo, $nodo));

        $nodo->update(['permisos' => '1020']);
        $this->assertFalse($this->nodoPolicy->leer($miembroGrupo, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir($miembroGrupo, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($miembroGrupo, $nodo));

        $nodo->update(['permisos' => '1040']);
        $this->assertTrue($this->nodoPolicy->leer($miembroGrupo, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($miembroGrupo, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($miembroGrupo, $nodo));

        $nodo->update(['permisos' => '060']);
        $this->assertTrue($this->nodoPolicy->leer($miembroGrupo, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir($miembroGrupo, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($miembroGrupo, $nodo));

        $nodo->update(['permisos' => '070']);
        $miembroGrupo->grupos()->detach($grupo->id);
        $this->assertFalse($this->nodoPolicy->leer($miembroGrupo, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($miembroGrupo, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($miembroGrupo, $nodo));

    }


    // Verifica que otros usuarios tienen solo permiso de lectura cuando los permisos son 754
    public function test_permisos_otros()
    {
        $propietario = $this->getUser("Propietario");
        $otroUsuario = $this->getUser("OtroUsuario");

        $grupo = $this->getGrupo("test3");

        $nodo = $this->getNodo('/archivos/test3', [
            'user_id' => $propietario->id,
            'group_id' => $grupo->id,
        ]);

        // Asociamos el usuario al grupo
        if($otroUsuario->enGrupo($grupo->id))
            $otroUsuario->grupos()->detach($grupo->id);

        $nodo->update(['permisos' => '000']);
        $this->assertFalse($this->nodoPolicy->leer($otroUsuario, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($otroUsuario, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($otroUsuario, $nodo));

        $nodo->update(['permisos' => '007']);
        $this->assertTrue($this->nodoPolicy->leer($otroUsuario, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir($otroUsuario, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar($otroUsuario, $nodo));

        $nodo->update(['permisos' => '001']);
        $this->assertFalse($this->nodoPolicy->leer($otroUsuario, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($otroUsuario, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar($otroUsuario, $nodo));

        $nodo->update(['permisos' => '002']);
        $this->assertFalse($this->nodoPolicy->leer($otroUsuario, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir($otroUsuario, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($otroUsuario, $nodo));

        $nodo->update(['permisos' => '004']);
        $this->assertTrue($this->nodoPolicy->leer($otroUsuario, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($otroUsuario, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($otroUsuario, $nodo));

        $nodo->update(['permisos' => '005']);
        $this->assertTrue($this->nodoPolicy->leer($otroUsuario, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir($otroUsuario, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar($otroUsuario, $nodo));

        $nodo->update(['permisos' => '1006']);
        $this->assertTrue($this->nodoPolicy->leer($otroUsuario, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir($otroUsuario, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar($otroUsuario, $nodo));

    }


    //  Prueba el caso de acceso sin usuario autenticado (null), con permisos 704
    public function test_permisos_sin_usuario()
    {
        $propietario = $this->getUser("Propietario");

        $nodo = $this->getNodo('/archivos/test4', [
            'user_id' => $propietario->id,
        ]);

        $nodo->update(['permisos' => '000']);
        $this->assertFalse($this->nodoPolicy->leer(null, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir(null, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar(null, $nodo));

        $nodo->update(['permisos' => '007']);
        $this->assertTrue($this->nodoPolicy->leer(null, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir(null, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar(null, $nodo));

        $nodo->update(['permisos' => '001']);
        $this->assertFalse($this->nodoPolicy->leer(null, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir(null, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar(null, $nodo));

        $nodo->update(['permisos' => '002']);
        $this->assertFalse($this->nodoPolicy->leer(null, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir(null, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar(null, $nodo));

        $nodo->update(['permisos' => '004']);
        $this->assertTrue($this->nodoPolicy->leer(null, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir(null, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar(null, $nodo));

        $nodo->update(['permisos' => '005']);
        $this->assertTrue($this->nodoPolicy->leer(null, $nodo));
        $this->assertFalse($this->nodoPolicy->escribir(null, $nodo));
        $this->assertTrue($this->nodoPolicy->ejecutar(null, $nodo));

        $nodo->update(['permisos' => '1006']);
        $this->assertTrue($this->nodoPolicy->leer(null, $nodo));
        $this->assertTrue($this->nodoPolicy->escribir(null, $nodo));
        $this->assertFalse($this->nodoPolicy->ejecutar(null, $nodo));

    }


    // test accesors

    public function test_nodo_accesors()
    {
        $user = $this->getUser("Propietario");
        $grupo = $this->getGrupo("access");
        $nodo1 = $this->getNodo('/archivos/test_accesors1', [
            'user_id' => $user->id,
            'permisos' => '007',
        ]);
        $nodo2 = $this->getNodo('/archivos/test_accesors2', [
            'group_id' => $grupo->id,
            'permisos' => '1750',
        ]);

        $this->assertEquals($user->name, $nodo1->nombreUsuario);
        $this->assertEquals($grupo->nombre, $nodo2->nombreGrupo);
        $this->assertEquals(0, $nodo1->sticky);
        $this->assertEquals(1, $nodo2->sticky);
    }

}
