<?php

namespace Tests\Unit;

use App\Models\Evento;

class EventoMiniaturaTest extends BaseTest
{
    protected function makeEvento(array $overrides = []): Evento
    {
        return Evento::create(array_merge([
            'titulo' => 'Evento Portada',
            'slug' => 'evento-portada-'.uniqid(),
            'descripcion' => 'Desc',
            'categoria' => 'general',
            'texto' => 'Texto',
            'imagen' => null,
            'published_at' => now(),
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDay(),
            'hora_inicio' => '10:00',
            'visibilidad' => 'P',
            'centro_id' => null,
            'sala_id' => null,
            'equipo_id' => null,
        ], $overrides));
    }

    public function test_mostrar_miniatura_default_is_false()
    {
        $evento = $this->makeEvento();
        $fresh = Evento::find($evento->id);

        $this->assertFalse($fresh->mostrar_miniatura);
        $this->assertIsBool($fresh->mostrar_miniatura);
    }

    public function test_mostrar_miniatura_is_cast_to_boolean()
    {
        $evento = $this->makeEvento(['mostrar_miniatura' => true]);

        $this->assertTrue($evento->mostrar_miniatura);
        $this->assertIsBool($evento->mostrar_miniatura);
    }

    public function test_mostrar_miniatura_is_persisted_and_read_back()
    {
        $evento = $this->makeEvento(['mostrar_miniatura' => true]);

        $fresh = Evento::find($evento->id);
        $this->assertTrue($fresh->mostrar_miniatura);

        $fresh->mostrar_miniatura = false;
        $fresh->save();

        $this->assertFalse(Evento::find($evento->id)->mostrar_miniatura);
    }

    public function test_mostrar_miniatura_is_fillable()
    {
        $this->assertContains('mostrar_miniatura', (new Evento())->getFillable());
    }
}
