<?php

namespace Tests\Feature\Eventos;

use App\Models\Evento;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class EventosUbicacionContactoTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Crea un evento publicado de prueba con los campos opcionales de ubicación/contacto.
     */
    private function crearEvento(array $overrides = []): Evento
    {
        $datos = array_merge([
            'titulo' => 'Evento de prueba ubicacion',
            'slug' => 'evento-prueba-ubicacion-'.uniqid(),
            'descripcion' => 'Descripción del evento de prueba',
            'categoria' => 'Encuentro',
            'texto' => 'Texto del evento de prueba',
            'imagen' => null,
            'published_at' => now(),
            'fecha_inicio' => now()->addWeek(),
            'fecha_fin' => now()->addWeek()->addHours(3),
            'hora_inicio' => '17:00:00',
            'hora_fin' => '18:00:00',
            'pais' => 'ES',
            'visibilidad' => 'P',
            'centro_id' => null,
            'sala_id' => null,
            'equipo_id' => null,
        ], $overrides);

        return Evento::withoutEvents(fn () => Evento::create($datos));
    }

    public function test_index_muestra_ciudad_y_pais_convertido_cuando_el_evento_tiene_ciudad()
    {
        $evento = $this->crearEvento(['ciudad' => 'Madrid']);

        $response = $this->get(route('eventos'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Eventos/Index'));

        $enListado = collect($response->inertiaProps('listado.data'))
            ->firstWhere('slug', $evento->slug);

        $this->assertNotNull($enListado, 'El evento no aparece en el listado de Eventos/Index');
        // El badge "Ciudad, País" se compone en el frontend (Index.vue) a partir de estos dos datos
        $this->assertSame('Madrid', $enListado['ciudad'] ?? null);
        $this->assertSame('España', $enListado['pais'] ?? null);
        // hora_fin debe viajar en el select del index para el badge del card
        $this->assertSame('18:00:00', $enListado['hora_fin'] ?? null);
    }

    public function test_index_sin_ciudad_muestra_solo_el_pais()
    {
        $evento = $this->crearEvento();

        $response = $this->get(route('eventos'));

        $response->assertOk();

        $enListado = collect($response->inertiaProps('listado.data'))
            ->firstWhere('slug', $evento->slug);

        $this->assertNotNull($enListado, 'El evento no aparece en el listado de Eventos/Index');
        // Sin ciudad: el badge muestra solo el país (comportamiento actual intacto)
        $this->assertNull($enListado['ciudad'] ?? null);
        $this->assertSame('España', $enListado['pais'] ?? null);
    }

    public function test_detalle_muestra_lugar_contacto_hora_fin_y_ciudad()
    {
        $evento = $this->crearEvento([
            'ciudad' => 'Madrid',
            'lugar' => 'Sede Tseyor, Avenida de la Paz 12',
            'contacto' => '600 123 456 / evento@tseyor.org',
            'hora_fin' => '20:00:00',
        ]);

        $response = $this->get(route('evento', $evento->slug));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Eventos/Evento')
            ->where('evento.lugar', 'Sede Tseyor, Avenida de la Paz 12')
            ->where('evento.contacto', '600 123 456 / evento@tseyor.org')
            ->where('evento.hora_fin', '20:00:00')
            ->where('evento.ciudad', 'Madrid')
            ->where('evento.pais', 'España'));
    }

    public function test_detalle_sin_campos_opcionales_no_los_incluye()
    {
        $evento = $this->crearEvento([
            'ciudad' => null,
            'hora_fin' => null,
            'lugar' => null,
            'contacto' => null,
        ]);

        $response = $this->get(route('evento', $evento->slug));

        $response->assertOk();
        // Los cuatro campos llegan a null: el frontend los omite con v-if
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Eventos/Evento')
            ->where('evento.lugar', null)
            ->where('evento.contacto', null)
            ->where('evento.hora_fin', null)
            ->where('evento.ciudad', null)
            ->where('evento.pais', 'España'));
    }

    public function test_crear_evento_con_los_cuatro_campos_vacios_guarda_null()
    {
        $evento = $this->crearEvento([
            'ciudad' => '',
            'hora_fin' => '',
            'lugar' => '',
            'contacto' => '',
        ]);

        $evento->refresh();

        // Convención del proyecto: '' se coerce a null en los mutadores del modelo
        $this->assertNull($evento->ciudad);
        $this->assertNull($evento->hora_fin);
        $this->assertNull($evento->lugar);
        $this->assertNull($evento->contacto);
    }
}
