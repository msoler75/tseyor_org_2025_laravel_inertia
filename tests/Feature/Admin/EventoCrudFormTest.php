<?php

namespace Tests\Feature\Admin;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Formulario de edición de eventos en el admin (Backpack):
 * - los campos nullable deben renderizarse vacíos (sin el literal "NULL")
 * - el orden renderizado: fecha_inicio → fecha_fin → hora_inicio → hora_fin →
 *   fechas_evento → … → published_at → visibilidad (último campo)
 */
class EventoCrudFormTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Campos del formulario, para filtrarlos del HTML y calcular su orden renderizado.
     * `id` es el hidden que añade Backpack al final del array de campos.
     */
    private const CAMPOS_FORMULARIO = [
        'titulo',
        'slug',
        'categoria',
        'centro_id',
        'sala_id',
        'equipo_id',
        'descripcion',
        'texto',
        'imagen',
        'imagenes',
        'mostrar_miniatura',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'fechas_evento',
        'pais',
        'ciudad',
        'lugar',
        'contacto',
        'published_at',
        'visibilidad',
        'id',
    ];

    /**
     * Crea un usuario administrador (rol superadministrador) para superar el middleware CheckIfAdmin.
     */
    private function crearAdmin(): User
    {
        $admin = User::create([
            'name' => 'admin_form_eventos',
            'email' => 'admin_form_eventos@tseyor.org',
            'slug' => 'admin-form-eventos',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('superadministrador');

        return $admin;
    }

    /**
     * Crea un evento con los campos opcionales de ubicación/contacto en NULL real.
     */
    private function crearEventoConCamposVacios(): Evento
    {
        return Evento::withoutEvents(fn () => Evento::create([
            'titulo' => 'Evento de prueba formulario admin',
            'slug' => 'evento-prueba-formulario-'.uniqid(),
            'descripcion' => 'Descripción del evento de prueba',
            'categoria' => 'Encuentro',
            'texto' => 'Texto del evento de prueba',
            'pais' => 'ES',
            'visibilidad' => 'B',
            'fecha_inicio' => '2026-11-01',
            'hora_inicio' => '17:00:00',
            // opcionales: quedan en NULL real en la BD
            'ciudad' => null,
            'lugar' => null,
            'contacto' => null,
            'hora_fin' => null,
            'published_at' => null,
            'imagen' => null,
        ]));
    }

    /**
     * GET autenticado del formulario de edición; devuelve el HTML renderizado.
     */
    private function formularioEdicion(Evento $evento): string
    {
        $response = $this->actingAs($this->crearAdmin())
            ->get('/admin/evento/'.$evento->id.'/edit');

        $response->assertOk();

        return $response->getContent();
    }

    /**
     * Devuelve el atributo value del input con el nombre indicado, o null si no existe.
     */
    private function valorDelInput(string $html, string $nombre): ?string
    {
        if (! preg_match('/<input\b(?:(?!>).)*?(?<![\w-])name="'.$nombre.'"(?:(?!>).)*>/is', $html, $tag)) {
            return null;
        }

        return preg_match('/(?<![\w-])value="([^"]*)"/', $tag[0], $valor) ? $valor[1] : null;
    }

    /**
     * Orden en el que se renderizan los campos del formulario (primera aparición de cada uno).
     */
    private function ordenDeCampos(string $html): array
    {
        preg_match_all('/(?<![\w-])(?:name|field-name)="([a-z_]+)"/', $html, $coincidencias);

        $orden = [];
        foreach ($coincidencias[1] as $nombre) {
            if (! in_array($nombre, self::CAMPOS_FORMULARIO, true)) {
                continue;
            }
            // primera aparición únicamente (p. ej. los dos radios de visibilidad)
            if (in_array($nombre, $orden, true)) {
                continue;
            }
            $orden[] = $nombre;
        }

        return $orden;
    }

    /**
     * Sublista de $orden restringida a los nombres indicados (en el orden real renderizado).
     */
    private function suborden(array $orden, array $nombres): array
    {
        return array_values(array_filter($orden, fn ($nombre) => in_array($nombre, $nombres, true)));
    }

    /**
     * Posición de un campo en el orden renderizado; falla con el orden completo si no aparece.
     */
    private function posicionDe(array $orden, string $nombre): int
    {
        $posicion = array_search($nombre, $orden, true);

        $this->assertNotFalse(
            $posicion,
            "El campo {$nombre} no aparece en el formulario. Orden renderizado: ".json_encode($orden)
        );

        return $posicion;
    }

    public function test_formulario_editar_renderiza_vacio_los_campos_nullable(): void
    {
        $evento = $this->crearEventoConCamposVacios();
        $html = $this->formularioEdicion($evento);

        // Regresión T7: los campos con NULL real deben pintar value="", nunca el texto "NULL"
        foreach (['ciudad', 'lugar', 'contacto', 'hora_fin'] as $campo) {
            $this->assertSame(
                '',
                $this->valorDelInput($html, $campo),
                "El campo {$campo} debe renderizarse vacío cuando la BD tiene NULL, no el texto \"NULL\""
            );
        }

        // El mismo síntoma afectaba a campos nullable preexistentes (imagen es hidden y también
        // caía en el default 'NULL'), lo que demuestra que la causa es genérica y no de los campos nuevos.
        $this->assertSame(
            '',
            $this->valorDelInput($html, 'imagen'),
            'El campo hidden imagen debe renderizarse vacío cuando la BD tiene NULL'
        );
    }

    public function test_formulario_editar_pinta_los_valores_cuando_existen(): void
    {
        // Triangulación: el arreglo no debe vaciar los inputs, solo el literal "NULL"
        $evento = Evento::withoutEvents(fn () => Evento::create([
            'titulo' => 'Evento de prueba con valores',
            'slug' => 'evento-prueba-valores-'.uniqid(),
            'descripcion' => 'Descripción del evento con valores',
            'categoria' => 'Encuentro',
            'texto' => 'Texto del evento con valores',
            'pais' => 'ES',
            'visibilidad' => 'B',
            'fecha_inicio' => '2026-11-01',
            'hora_inicio' => '17:00:00',
            'ciudad' => 'Madrid',
            'lugar' => 'Sede Central',
            'contacto' => '600 000 000',
            'hora_fin' => '18:30:00',
        ]));
        $html = $this->formularioEdicion($evento);

        $this->assertSame('Madrid', $this->valorDelInput($html, 'ciudad'));
        $this->assertSame('Sede Central', $this->valorDelInput($html, 'lugar'));
        $this->assertSame('600 000 000', $this->valorDelInput($html, 'contacto'));
        $this->assertSame('18:30:00', $this->valorDelInput($html, 'hora_fin'));
    }

    public function test_orden_del_formulario_fechas_horas_fechas_repetidas_y_visibilidad_al_final(): void
    {
        $evento = $this->crearEventoConCamposVacios();
        $html = $this->formularioEdicion($evento);
        $orden = $this->ordenDeCampos($html);

        // T8: hora_inicio y hora_fin justo después de las fechas y antes del widget de fechas repetidas
        $this->assertSame(
            ['fecha_inicio', 'fecha_fin', 'hora_inicio', 'hora_fin', 'fechas_evento'],
            $this->suborden($orden, ['fecha_inicio', 'fecha_fin', 'hora_inicio', 'hora_fin', 'fechas_evento']),
            'Las horas deben ir tras las fechas y antes de fechas_evento. Orden renderizado: '.json_encode($orden)
        );

        // T9: la cadena centro → sala → equipo encadenada y antes de published_at
        $this->assertSame(
            ['centro_id', 'sala_id', 'equipo_id'],
            $this->suborden($orden, ['centro_id', 'sala_id', 'equipo_id']),
            'Centro, sala y equipo deben ir encadenados en ese orden. Orden renderizado: '.json_encode($orden)
        );
        $this->assertLessThan(
            $this->posicionDe($orden, 'published_at'),
            $this->posicionDe($orden, 'equipo_id'),
            'El equipo organizador debe ir antes de published_at. Orden renderizado: '.json_encode($orden)
        );

        // T9: published_at antes de visibilidad
        $this->assertLessThan(
            $this->posicionDe($orden, 'visibilidad'),
            $this->posicionDe($orden, 'published_at'),
            'published_at debe ir antes de visibilidad. Orden renderizado: '.json_encode($orden)
        );

        // T9: visibilidad es el último campo del formulario
        // (Backpack añade el hidden `id` al final del array de campos: es el único permitido tras visibilidad)
        $trasVisibilidad = array_slice($orden, $this->posicionDe($orden, 'visibilidad') + 1);
        $permitidosTrasVisibilidad = ['id'];

        $this->assertSame(
            [],
            array_values(array_diff($trasVisibilidad, $permitidosTrasVisibilidad)),
            'visibilidad debe ser el último campo del formulario. Orden renderizado: '.json_encode($orden)
        );
    }
}
