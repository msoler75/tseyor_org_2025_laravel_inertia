<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Equipo;
use App\Models\Nodo;
use App\Models\Acl;
use App\Models\User;
use App\Models\Grupo;
use Illuminate\Support\Str;
use App\Pigmalion\StorageItem;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Policies\NodoPolicy;
use Illuminate\Support\Facades\Log;

class EquipoTest extends BaseTest
{
    // use WithoutMiddleware;

    protected $equipo, $carpetaEquipo, $coordinador, $miembro, $otroUsuario;

    protected function setUp(): void
    {

        parent::setUp();

        $admin = $this->getUser("admin");
        $this->actingAs($admin);

        $this->coordinador = $this->getUser("Coordinador");
        $this->miembro = $this->getUser("MiembroEquipo");
        $this->otroUsuario = $this->getUser("A_OtroUsuario");

        $nombre = "Patamala";
        $slug = Str::slug($nombre);
        $this->carpetaEquipo = "/archivos/equipos/$slug";
        $this->limpiarDatos($nombre);

        // borramos carpeta del equipo si acaso existe
        $dir = new StorageItem($this->carpetaEquipo);
        // si la carpeta tiene archivos, los eliminamos
        if ($dir->exists())
            $dir->deleteDirectory();
        //@rmdir($dir->path);

        $exists = file_exists($dir->path);
        $this->assertFalse($exists);

        // $token = csrf_token();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);


        $response = $this->post('/admin/equipo', [
            // '_token' => $token,
            // '_http_referrer'=> 'http://localhost/admin/equipo',
            'nombre' => $nombre,
            'slug' => $slug,
            'descripcion' => 'desc',
            'imagen' => '/almacen/medios/equipos/equipo1.jpg',
            'categoria' => '',
            'anuncio' => '',
            'reuniones' => '',
            'informacion' => '',
            'grupo' => '',
            'CoordinadoresJSON' => json_encode([["value" => $this->coordinador->id, "label" => "Coordinador"]]),
            'MiembrosJSON' => json_encode([["value" => $this->miembro->id, "label" => "Miembro"]]),
            'ocultarMiembros' => '0',
            'ocultarCarpetas' => '0',
            'ocultarArchivos' => '0',
        ]);

        $this->equipo = Equipo::with('grupo')->where('nombre', $nombre)->first();

        $nodo = Nodo::desde($this->carpetaEquipo);
        $nodo->update(['permisos'=>'1750']);
    }

    protected function limpiarDatos($nombre)  {
        $slug = Str::slug($nombre);

        Nodo::where("ubicacion", 'like', $this->carpetaEquipo . "%")->forceDelete();
        Equipo::where('nombre', $nombre)->forceDelete();
        Grupo::where('nombre', $nombre)->forceDelete();
        Grupo::where('slug', $slug)->forceDelete();
        Acl::where('user_id', $this->coordinador->id)->forceDelete();
        Acl::where('user_id', $this->miembro->id)->forceDelete();
    }

    public function test_equipo_limpiar() {
        $this->limpiarDatos($this->equipo->nombre);
        $this->assertTrue(true);
    }

    public function test_equipo_creacion()
    {
        // check response status 200:
        // $response->assertStatus(302);
        $dir = new StorageItem($this->carpetaEquipo);
        // comprueba que se ha creado la carpeta del equipo
        Log::info("Test path mkdir: " . $dir->path);
        $exists = file_exists($dir->path);
        $this->assertTrue($exists);

        // comprueba que se ha creado el equipo
        $this->assertNotNull($this->equipo);
        // $this->assertEquals($nombre, $this->equipo->nombre);


        // verifica que se ha creado el grupo
        $grupo = Grupo::where('nombre', $this->equipo->slug)->first();

        $this->assertNotNull($grupo);
        $this->assertEquals($this->equipo->slug, $grupo->slug);
        $this->assertEquals($this->equipo->nombre, $grupo->nombre);

        $grupo = $this->equipo->grupo()->first();

        $this->assertEquals($this->equipo->grupo()->first()->id, $grupo->id);

        // verifica que los miembros están en el grupo
        $this->assertTrue($grupo->usuarios()->get()->contains($this->coordinador->id));
        $this->assertTrue($grupo->usuarios()->where('users.id', $this->miembro->id)->exists());
        $this->assertFalse($grupo->usuarios()->where('users.id', $this->otroUsuario->id)->exists());

        // verifica los coordinadores y miembros
        $this->assertCount(1, $this->equipo->coordinadores()->get());
        $this->assertCount(2, $this->equipo->miembros()->get());

        // el coordinador 'papa' tiene permisos de escritura en la carpeta $carpeta
        $coordinadorE = $this->equipo->coordinadores()->first();
        $this->assertNotNull($coordinadorE);
        $this->assertEquals($this->coordinador->id, $coordinadorE->id);

        // comprobamos si $miembro está en $equipo->miembros()

        $encontrado = $this->equipo->miembros()->where('users.id', $this->miembro->id)->first();
        $this->assertNotNull($encontrado);
        $this->assertEquals($this->miembro->id, $encontrado->id);


        Log::info("grupos de coordinador", $this->coordinador->grupos()->get()->toArray());
        $this->assertTrue($this->coordinador->enGrupo($grupo->id));
        $this->assertTrue($this->miembro->enGrupo($grupo->id));
        // comprobar el rol de cada usuario en el equipo (pivot)


        // comprobamos permisos básicos de acceso a la carpeta del equipo
        $nodoPolicy = new NodoPolicy();
        $nodo = Nodo::desde($this->carpetaEquipo);
        $this->assertNotNull($nodo);
        $this->assertEquals($this->carpetaEquipo, $nodo->ubicacion);

        $this->assertTrue($nodoPolicy->leer($coordinadorE, $nodo));
        $this->assertTrue($nodoPolicy->escribir($coordinadorE, $nodo));
        $this->assertTrue($nodoPolicy->ejecutar($coordinadorE, $nodo));

        // el miembro puede leer, y escribir, y listar (ejecutar)

        $this->assertTrue($nodoPolicy->leer($this->miembro, $nodo));
        $this->assertFalse($nodoPolicy->escribir($this->miembro, $nodo));
        $this->assertTrue($nodoPolicy->ejecutar($this->miembro, $nodo));

        // cualquier otro usuario no tiene acceso (permisos 750)
        $this->assertFalse($nodoPolicy->leer($this->otroUsuario, $nodo));
        $this->assertFalse($nodoPolicy->escribir($this->otroUsuario, $nodo));
        $this->assertFalse($nodoPolicy->ejecutar($this->otroUsuario, $nodo));

        // comprueba existencia de ACL
        $acl = Acl::where('user_id', $coordinadorE->id)->where('nodo_id', $nodo->id)->first();
        $this->assertNotNull($acl);
        $this->assertEquals('leer,escribir,ejecutar', $acl->verbos);
    }



    function test_equipo_agrega_coordinador()
    {
        // miembro no es coordinador
        $this->assertFalse($this->equipo->coordinadores()->where('users.id', $this->miembro->id)->exists());

        // comprobamos permisos básicos de acceso a la carpeta del equipo
        $nodoPolicy = new NodoPolicy();
        $nodo = Nodo::desde($this->carpetaEquipo);
        $this->assertNotNull($nodo);
        $this->assertEquals($this->carpetaEquipo, $nodo->ubicacion);
        // no tiene permisos de escritura
        $this->assertFalse($nodoPolicy->escribir($this->miembro, $nodo));

        $this->actingAs($this->coordinador);
        // agrega a $miembro como coordinador
        $response = $this->put("/equipos/{$this->equipo->id}/rol/{$this->miembro->id}/coordinador");

        $response->assertStatus(200);

        $this->equipo->refresh();
        $this->assertNotNull($this->equipo);

        // ahora miembro sí es coordinador
        $this->assertTrue($this->equipo->coordinadores()->where('users.id', $this->miembro->id)->exists());

        // ahora sí tiene permisos de escritura
        // $this->miembro->refresh();
        $this->assertTrue($nodoPolicy->escribir($this->miembro, $nodo));

        // lo remueve de coordinador
        $response = $this->put("/equipos/{$this->equipo->id}/rol/{$this->miembro->id}/miembro");

        $response->assertStatus(200);

        // vuelve a no tener permisos de escritura a la carpeta
        $this->assertFalse($nodoPolicy->escribir($this->miembro, $nodo));
    }


    /**
     * El último coordinador del equipo se degrada a simple miembro. Se elige a un nuevo coordinador
     */
    function test_equipo_cambia_coordinador_degradacion()
    {
        // miembro no es coordinador
        $this->assertFalse($this->equipo->coordinadores()->where('users.id', $this->miembro->id)->exists());

        $this->actingAs($this->coordinador);
        // establece a $coordinador como simple miembro
        $response = $this->put("/equipos/{$this->equipo->id}/rol/{$this->coordinador->id}/miembro");

        $response->assertStatus(200);

        $this->equipo->refresh();

        // miembro es ahora el nuevo coordinador
        $this->assertTrue($this->equipo->coordinadores()->where('users.id', $this->miembro->id)->exists());
    }


    /**
     * El ultimo coordinador del equipo se retira del equipo. Se debe asignar un nuevo coordinador
     */
    function test_equipo_cambia_coordinador_baja()
    {
        // miembro no es coordinador
        $this->assertFalse($this->equipo->coordinadores()->where('users.id', $this->miembro->id)->exists());

        $this->actingAs($this->coordinador);
        // elimina el coordinador
        $response = $this->put("/equipos/{$this->equipo->id}/remover/{$this->coordinador->id}");

        $response->assertStatus(200);

        $this->equipo->refresh();

        // miembro es ahora el nuevo coordinador
        $this->assertTrue($this->equipo->coordinadores()->where('users.id', $this->miembro->id)->exists());
    }



    /**
     * Elige como nuevo coordinador al más antigup
     */
    function test_equipo_cambia_coordinador_antiguo()
    {
        // miembro no es coordinador
        $this->assertFalse($this->equipo->coordinadores()->where('users.id', $this->miembro->id)->exists());

        $this->actingAs($this->coordinador);
        // agrega a nuevo miembro
        sleep(1); // pausa 1 segundo
        $response = $this->put("/equipos/{$this->equipo->id}/agregar/{$this->otroUsuario->id}");
        $response->assertStatus(200);

        // nos aseguramos que OtroUser tiene una fecha de creación antigua
        $this->otroUsuario->created_at = '2020-08-01 12:00:00';
        $this->otroUsuario->save();

        // elimina el coordinador
        $response = $this->put("/equipos/{$this->equipo->id}/remover/{$this->coordinador->id}");

        $response->assertStatus(200);

        $this->equipo->refresh();

        // miembro es ahora el nuevo coordinador, y no OtroUser
        $this->assertTrue($this->equipo->coordinadores()->where('users.id', $this->miembro->id)->exists());
    }
}
