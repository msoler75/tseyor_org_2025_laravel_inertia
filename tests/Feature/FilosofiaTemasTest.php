<?php

namespace Tests\Feature;

use App\Models\Pagina;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class FilosofiaTemasTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Crea una página publicada de prueba.
     */
    private function crearPagina(array $overrides = []): Pagina
    {
        $datos = array_merge([
            'titulo' => 'Tema de prueba',
            'ruta' => 'tema-test-'.uniqid(),
            'texto' => 'Texto del tema de prueba',
            'descripcion' => 'Descripción del tema de prueba',
            'descubre' => false,
            'filosofia' => true,
            'orden' => null,
            'visibilidad' => 'P',
        ], $overrides);

        return Pagina::withoutEvents(fn () => Pagina::create($datos));
    }

    public function test_temas_incluye_paginas_fuera_del_mapa_hardcodeado()
    {
        // slug deliberadamente fuera del mapa de categorías del controlador
        $this->crearPagina(['ruta' => 'tema-test-otros', 'titulo' => 'Tema fuera del mapa']);

        $response = $this->get(route('filosofia.temas'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Filosofia/Temas'));

        $grupos = $response->inertiaProps('paginasPorCategoria');

        $grupoOtros = collect($grupos)->firstWhere('categoria', 'Otros temas');
        $this->assertNotNull($grupoOtros, 'La página fuera del mapa no aparece en el grupo «Otros temas»');

        $rutas = collect($grupoOtros['paginas'])->pluck('ruta');
        $this->assertTrue($rutas->contains('tema-test-otros'), 'La página tema-test-otros no está en el grupo «Otros temas»');
    }

    public function test_portada_devuelve_top4_por_orden_sin_like_hardcodeado()
    {
        // slugs sin las palabras rayo/sociedades/confederacion/observacion que
        // antes hacían falta para pasar el LIKE hardcodeado del controlador
        foreach (['uno', 'dos', 'tres', 'cuatro', 'cinco'] as $i => $nombre) {
            $this->crearPagina([
                'ruta' => 'tema-test-'.$nombre,
                'titulo' => 'Tema '.$nombre,
                'orden' => ($i + 1) * 10,
            ]);
        }

        $response = $this->get(route('portada'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Portada'));

        $rutas = collect($response->inertiaProps('paginasFilosofia'))->pluck('ruta');

        $this->assertSame(
            ['tema-test-uno', 'tema-test-dos', 'tema-test-tres', 'tema-test-cuatro'],
            $rutas->values()->all(),
            'La portada no devuelve el top-4 por orden'
        );
        $this->assertFalse($rutas->contains('tema-test-cinco'), 'La quinta página no debe aparecer en la portada');
    }

    public function test_temas_agrupa_paginas_marcadas()
    {
        // slug SÍ en el mapa: 'el-rayo-sincronizador' → «El cambio de era»
        $this->crearPagina(['ruta' => 'el-rayo-sincronizador', 'titulo' => 'El Rayo Sincronizador (test)']);

        $response = $this->get(route('filosofia.temas'));

        $response->assertOk();

        $grupos = $response->inertiaProps('paginasPorCategoria');

        $grupo = collect($grupos)->firstWhere('categoria', 'El cambio de era');
        $this->assertNotNull($grupo, 'La página temática marcada no aparece en «El cambio de era»');

        $rutas = collect($grupo['paginas'])->pluck('ruta');
        $this->assertTrue($rutas->contains('el-rayo-sincronizador'), 'el-rayo-sincronizador no está en su categoría');
    }
}
