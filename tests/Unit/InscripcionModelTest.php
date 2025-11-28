<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\InscripcionAsignada;

class InscripcionModelTest extends TestCase
{
    private User $usuario;
    private Inscripcion $inscripcion;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();

        // Limpiar inscripciones existentes
        DB::table('inscripciones')->truncate();

        // Crear o encontrar usuario de prueba
        $usuarioExistente = User::where('email', 'usuario_test_model@tseyor.org')->first();
        if ($usuarioExistente) {
            $this->usuario = $usuarioExistente;
        } else {
            $this->usuario = User::create([
                'name' => 'Usuario Test Model',
                'email' => 'usuario_test_model@tseyor.org',
                'password' => bcrypt('password'),
            ]);
        }

        // Crear inscripción de prueba
        $this->inscripcion = Inscripcion::create([
            'nombre' => 'Test Inscripcion',
            'email' => 'inscripcion@test.com',
            'estado' => 'nueva',
            'fecha_nacimiento' => '1990-01-01',
            'ciudad' => 'Test Ciudad',
            'region' => 'Test Región',
            'pais' => 'Test País',
            'ultima_actividad' => now() // Simular el comportamiento del controller
        ]);
    }



    public function test_inscripcion_nueva_debe_tener_ultima_actividad()
    {
        // Crear una inscripción como lo haría el controller
        $inscripcion = Inscripcion::create([
            'nombre' => 'Nueva Inscripción Test',
            'email' => 'nueva@test.com',
            'estado' => 'nueva',
            'fecha_nacimiento' => '1985-05-15',
            'ciudad' => 'Madrid',
            'region' => 'Comunidad de Madrid',
            'pais' => 'España',
            'ultima_actividad' => now()
        ]);

        $this->assertNotNull($inscripcion->ultima_actividad);
        $this->assertInstanceOf(Carbon::class, $inscripcion->ultima_actividad);
        $this->assertTrue($inscripcion->ultima_actividad->isToday());
    }

    public function test_comentar_actualiza_notas()
    {
        $mensaje = 'Este es un comentario de prueba';

        $this->inscripcion->comentar($mensaje);

        $this->assertStringContainsString($mensaje, $this->inscripcion->notas);
        $this->assertStringContainsString('Sistema', $this->inscripcion->notas);
        $this->assertStringContainsString(now()->format('d/m/Y'), $this->inscripcion->notas);
    }

    public function test_comentar_con_actividad_tutor_actualiza_ultima_actividad()
    {
        $fechaAntes = $this->inscripcion->ultima_actividad;

        // Pequeño delay para asegurar diferencia de tiempo
        usleep(1000); // 1 milisegundo

        $this->inscripcion->comentar('Comentario del tutor', true);

        $this->assertTrue($this->inscripcion->ultima_actividad->greaterThanOrEqualTo($fechaAntes));
        $this->assertNotNull($this->inscripcion->ultima_actividad);
    }

    public function test_comentar_sin_actividad_tutor_no_actualiza_ultima_actividad()
    {
        $this->inscripcion->ultima_actividad = now()->subDays(5);
        $this->inscripcion->save();
        $fechaAntes = $this->inscripcion->ultima_actividad;

        $this->inscripcion->comentar('Comentario automático', false);

        $this->assertEquals($fechaAntes->format('Y-m-d H:i:s'), $this->inscripcion->ultima_actividad->format('Y-m-d H:i:s'));
    }

    public function test_asignar_a_usuario_cambia_estado_y_campos()
    {
        $motivo = 'Asignación de prueba';
        // Asignar mediante update para usar observers
        $this->inscripcion->update(['user_id' => $this->usuario->id]);
        // Añadir motivo como comentario explícito
        $this->inscripcion->comentar($motivo, true);

        $this->assertEquals('asignada', $this->inscripcion->estado);
        $this->assertEquals($this->usuario->id, $this->inscripcion->user_id);
        $this->assertNotNull($this->inscripcion->fecha_asignacion);
        $this->assertNotNull($this->inscripcion->ultima_actividad);
        $this->assertNotNull($this->inscripcion->ultima_notificacion, 'ultima_notificacion debe establecerse cuando se envía notificación exitosamente');
        $this->assertStringContainsString($motivo, $this->inscripcion->notas);
    }

    public function test_rebotar_actualiza_estado_y_limpia_asignacion()
    {
        // Primero asignar
        $this->inscripcion->update(['user_id' => $this->usuario->id]);

        $motivo = 'No responde a emails';
        // Emular comportamiento del controller: suprimir observers en la instancia,
        // persistir los cambios y luego restaurar observers
        $this->inscripcion->setSkipEstadoObserver(true);
        $this->inscripcion->setSkipUserObserver(true);

        $this->inscripcion->estado = 'rebotada';
        $this->inscripcion->user_id = null;
        $this->inscripcion->setAttribute('fecha_asignacion', null);
        $this->inscripcion->setAttribute('ultima_notificacion', null);
        $this->inscripcion->ultima_actividad = now();
        $this->inscripcion->save();

        $this->inscripcion->setSkipEstadoObserver(false);
        $this->inscripcion->setSkipUserObserver(false);
        $nombreUsuario = $this->usuario->name ?? 'Usuario desconocido';
        $this->inscripcion->comentar("Rebotada por {$nombreUsuario}. Motivo: {$motivo}");

        $this->assertEquals('rebotada', $this->inscripcion->estado);
        $this->assertNull($this->inscripcion->user_id);
        $this->assertNull($this->inscripcion->fecha_asignacion);
        $this->assertNull($this->inscripcion->ultima_notificacion);
        $this->assertNotNull($this->inscripcion->ultima_actividad);
        $this->assertStringContainsString($motivo, $this->inscripcion->notas);
        $this->assertStringContainsString($this->usuario->name, $this->inscripcion->notas);
    }

    public function test_actualizar_estado_cambia_estado_y_actualiza_actividad()
    {
        $estadoAnterior = $this->inscripcion->estado;
        $nuevoEstado = 'contactado';
        $comentario = 'Primer contacto realizado';

        // Reemplazo de actualizarEstado: actualizar estado; el observer añadirá la nota estándar y actualizará `ultima_actividad`.
        // Usar update() para persistir cambio y disparar correctamente los eventos
        $this->inscripcion->update(['estado' => $nuevoEstado]);

        // Refrescar la instancia para obtener las notas añadidas por el observer
        $this->inscripcion->refresh();

        $this->assertEquals($nuevoEstado, $this->inscripcion->estado);
        $this->assertNotNull($this->inscripcion->ultima_actividad);

        // Verificar que la nota de cambio de estado se ha añadido
        $expectedMessage = "Cambiado de '{$estadoAnterior}' a '{$nuevoEstado}'";
        $this->assertStringContainsString($expectedMessage, $this->inscripcion->notas);
    }

    public function test_marcar_actividad_actualiza_ultima_actividad()
    {
        $fechaAntes = now()->subHours(2);
        $this->inscripcion->ultima_actividad = $fechaAntes;
        $this->inscripcion->save();

        $this->inscripcion->marcarActividad();

        $this->assertTrue($this->inscripcion->ultima_actividad->isAfter($fechaAntes));
    }

    public function test_necesita_seguimiento_devuelve_true_para_estados_seguimiento()
    {
        $estadosSeguimiento = config('inscripciones.notificaciones.estados_seguimiento');

        foreach ($estadosSeguimiento as $estado) {
            $this->inscripcion->estado = $estado;
            $this->inscripcion->user_id = $this->usuario->id;

            $this->assertTrue($this->inscripcion->necesitaSeguimiento(),
                "El estado '{$estado}' debería necesitar seguimiento");
        }
    }

    public function test_necesita_seguimiento_devuelve_false_para_estados_finales()
    {
        $estadosFinales = config('inscripciones.notificaciones.estados_finales');

        foreach ($estadosFinales as $estado) {
            $this->inscripcion->estado = $estado;
            $this->inscripcion->user_id = $this->usuario->id;

            $this->assertFalse($this->inscripcion->necesitaSeguimiento(),
                "El estado '{$estado}' NO debería necesitar seguimiento");
        }
    }

    public function test_necesita_seguimiento_devuelve_false_sin_usuario_asignado()
    {
        $this->inscripcion->estado = 'asignada';
        $this->inscripcion->user_id = null;

        $this->assertFalse($this->inscripcion->necesitaSeguimiento(),
            'Sin usuario asignado no debería necesitar seguimiento');
    }

    public function test_proxima_notificacion_calcula_correctamente_para_estado_asignada()
    {
        $this->inscripcion->estado = 'asignada';
        $this->inscripcion->user_id = $this->usuario->id;
        $this->inscripcion->setAttribute('ultima_notificacion', null);
        $this->inscripcion->fecha_asignacion = now()->subDays(5);

        $proximaNotificacion = $this->inscripcion->proximaNotificacion();

        $this->assertNotNull($proximaNotificacion);
        $this->assertInstanceOf(Carbon::class, $proximaNotificacion);

        // Para primera notificación, siempre devuelve now()->subMinute()
        $fechaEsperada = now()->subMinute();

        $this->assertEquals(
            $fechaEsperada->format('Y-m-d H:i'),
            $proximaNotificacion->format('Y-m-d H:i')
        );
    }

    public function test_proxima_notificacion_calcula_correctamente_para_otros_estados()
    {
        $this->inscripcion->estado = 'contactado';
        $this->inscripcion->user_id = $this->usuario->id;
        $this->inscripcion->ultima_notificacion = now()->subDays(10);

        $proximaNotificacion = $this->inscripcion->proximaNotificacion();

        $this->assertNotNull($proximaNotificacion);

        $diasIntervalo = config('inscripciones.notificaciones.dias_intervalo');
        $fechaEsperada = $this->inscripcion->ultima_notificacion->addDays($diasIntervalo);

        $this->assertEquals(
            $fechaEsperada->format('Y-m-d'),
            $proximaNotificacion->format('Y-m-d')
        );
    }

    public function test_proxima_notificacion_devuelve_null_si_no_necesita_seguimiento()
    {
        $this->inscripcion->estado = 'finalizado'; // Estado final
        $this->inscripcion->user_id = $this->usuario->id;

        $proximaNotificacion = $this->inscripcion->proximaNotificacion();

        $this->assertNull($proximaNotificacion);
    }

    public function test_nombre_usuario_asignado_attribute_devuelve_nombre()
    {
        $this->inscripcion->user_id = $this->usuario->id;
        $this->inscripcion->load('usuarioAsignado');

        $nombre = $this->inscripcion->nombre_usuario_asignado;

        $this->assertEquals($this->usuario->name, $nombre);
    }

    public function test_nombre_usuario_asignado_attribute_fallback_campo_legacy()
    {
        $this->inscripcion->user_id = null;

        $nombre = $this->inscripcion->nombre_usuario_asignado;

        // Legacy 'asignado' field is ignored; expect null when no user assigned
        $this->assertNull($nombre);
    }

    public function test_estado_etiqueta_attribute_devuelve_etiqueta_configurada()
    {
        $this->inscripcion->estado = 'asignada';

        $etiqueta = $this->inscripcion->estado_etiqueta;

        $etiquetas = config('inscripciones.etiquetas_estados', []);
        $etiquetaEsperada = $etiquetas['asignada'] ?? 'asignada';

        $this->assertEquals($etiquetaEsperada, $etiqueta);
    }

    public function test_multiples_comentarios_se_acumulan_correctamente()
    {
        $this->inscripcion->comentar('Primer comentario');
        $this->inscripcion->comentar('Segundo comentario');
        $this->inscripcion->comentar('Tercer comentario');

        $this->assertStringContainsString('Primer comentario', $this->inscripcion->notas);
        $this->assertStringContainsString('Segundo comentario', $this->inscripcion->notas);
        $this->assertStringContainsString('Tercer comentario', $this->inscripcion->notas);

        // Verificar que están en orden cronológico (primer comentario aparece antes)
        $posicionPrimero = strpos($this->inscripcion->notas, 'Primer comentario');
        $posicionSegundo = strpos($this->inscripcion->notas, 'Segundo comentario');
        $posicionTercero = strpos($this->inscripcion->notas, 'Tercer comentario');

        $this->assertTrue($posicionPrimero < $posicionSegundo);
        $this->assertTrue($posicionSegundo < $posicionTercero);
    }

    public function test_asignar_a_mismo_usuario_no_genera_error()
    {
        $this->inscripcion->update(['user_id' => $this->usuario->id]);
        // Simular reasignación al mismo usuario y añadir nota de reasignación
        $this->inscripcion->update(['user_id' => $this->usuario->id]);
        $this->inscripcion->comentar('Reasignación', true);

        $this->assertEquals('asignada', $this->inscripcion->estado);
        $this->assertEquals($this->usuario->id, $this->inscripcion->user_id);
        $this->assertStringContainsString('Reasignación', $this->inscripcion->notas);
    }

    public function test_cambio_directo_de_tutor_agrega_nota_en_notas()
    {
        // Asegurar que inicialmente no hay tutor
        $this->assertNull($this->inscripcion->user_id);

        // Cambiar tutor directamente (no usando asignarA) para que el observer añada la nota
        $this->inscripcion->update(['user_id' => $this->usuario->id]);

        $this->inscripcion->refresh();

        $this->assertEquals($this->usuario->id, $this->inscripcion->user_id);

        // El observer añade una nota con el texto 'Asignado a {name} por Sistema'
        $expected = "Asignado a {$this->usuario->name}";
        $this->assertStringContainsString($expected, $this->inscripcion->notas);
    }

    /**
     * Esta prueba verifica que el comportamiento de asignación funciona correctamente
     * cuando las notificaciones están disponibles (caso normal)
     */
    public function test_asignar_a_usuario_establece_ultima_notificacion_correctamente()
    {
        $motivoAsignacion = 'Asignación de prueba normal';
        // Verificar estado inicial
        $nombre = $this->inscripcion->nombre_usuario_asignado;
        $this->assertNull($nombre);

        $this->inscripcion->update(['user_id' => $this->usuario->id]);
        $this->inscripcion->comentar($motivoAsignacion, true);

        // Verificar que todos los campos se establecen correctamente incluyendo ultima_notificacion
        $this->assertEquals('asignada', $this->inscripcion->estado);
        $this->assertEquals($this->usuario->id, $this->inscripcion->user_id);
        $this->assertNotNull($this->inscripcion->fecha_asignacion);
        $this->assertNotNull($this->inscripcion->ultima_actividad);
        // Verificar que ultima_notificacion es reciente (dentro del último minuto)
        $this->assertNotNull($this->inscripcion->ultima_notificacion, 'ultima_notificacion debe establecerse cuando la notificación se procesa exitosamente');
        $this->assertGreaterThanOrEqual(
            now()->subMinute(),
            $this->inscripcion->ultima_notificacion,
            'ultima_notificacion debe ser una fecha reciente'
        );

        // Verificar que se registra el comentario de asignación
        $this->assertStringContainsString($motivoAsignacion, $this->inscripcion->notas);
    }

    // asignaciones

     public function test_assign_sends_notification_and_adds_note_and_updates_fecha_asignacion()
    {
        Notification::fake();

        // Reusar la inscripción y el usuario creados en setUp
        $actor = $this->usuario;
        $tutor = User::factory()->create(['name' => 'Tutor']);

        $ins = $this->inscripcion;

        $this->actingAs($actor);

        $ins->update(['user_id' => $tutor->id]);
        $ins->refresh();

        $this->assertNotNull($ins->fecha_asignacion);
        $this->assertStringContainsString("Asignado a {$tutor->name}", $ins->notas);
        Notification::assertSentTo($tutor, InscripcionAsignada::class);
    }

    public function test_reassign_updates_note_and_fecha()
    {
        Notification::fake();

        $actor1 = $this->usuario;
        $actor2 = User::factory()->create(['name' => 'Actor2']);
        $tutor1 = User::factory()->create(['name' => 'Tutor1']);
        $tutor2 = User::factory()->create(['name' => 'Tutor2']);

        $ins = $this->inscripcion;

        $this->actingAs($actor1);
        $ins->update(['user_id' => $tutor1->id]);

        $this->actingAs($actor2);
        $ins->update(['user_id' => $tutor2->id]);
        $ins->refresh();

        $this->assertNotNull($ins->fecha_asignacion);
        $this->assertStringContainsString("Reasignado de {$tutor1->name} a {$tutor2->name}", $ins->notas);
        Notification::assertSentTo($tutor2, InscripcionAsignada::class);
    }

    public function test_unassign_adds_desasignado_note()
    {
        Notification::fake();

        $actor = $this->usuario;
        $tutor = User::factory()->create(['name' => 'TutorU']);

        $ins = $this->inscripcion;

        $this->actingAs($actor);
        $ins->update(['user_id' => $tutor->id]);

        // Ahora desasignar
        $ins->update(['user_id' => null]);
        $ins->refresh();

        $this->assertStringContainsString("Desasignado (antes {$tutor->name})", $ins->notas);
        // Se envía notificación al asignar; aseguramos que la asignación sí notificó
        Notification::assertSentTo($tutor, InscripcionAsignada::class);
    }

    public function test_state_change_adds_note_and_updates_ultima_actividad()
    {
        Notification::fake();

        $actor = $this->usuario;

        $ins = $this->inscripcion;

        $this->actingAs($actor);
        $estadoAnterior = $ins->estado;
        $ins->update(['estado' => 'asignada']);
        $ins->refresh();

        $this->assertNotNull($ins->ultima_actividad);
        $this->assertStringContainsString("Cambiado de '{$estadoAnterior}' a 'asignada'", $ins->notas);
    }

    public function test_suppress_assignment_notifications_global_prevents_notifications_but_keeps_notes()
    {
        Notification::fake();

        $actor = $this->usuario;
        $tutor = User::factory()->create(['name' => 'TutorM']);

        $ins = $this->inscripcion;

        $this->actingAs($actor);

        Inscripcion::suppressAssignmentNotifications(function () use ($ins, $tutor) {
            $ins->update(['user_id' => $tutor->id]);
        });

        $ins->refresh();

        $this->assertStringContainsString("Asignado a {$tutor->name}", $ins->notas);
        Notification::assertNothingSent();
    }

    public function test_assigning_tutor_from_nueva_sets_estado_asignada()
    {
        Notification::fake();

        $actor = $this->usuario;
        $tutor = User::factory()->create(['name' => 'TutorAuto']);

        $ins = $this->inscripcion;

        // Asegurarnos de que el estado inicial es 'nueva'
        $this->assertEquals('nueva', $ins->estado);

        $this->actingAs($actor);

        // Asignar directamente mediante update() (no usando asignarA)
        $ins->update(['user_id' => $tutor->id]);
        $ins->refresh();

        $this->assertEquals('asignada', $ins->estado, 'El estado debería pasar a asignada al asignar tutor desde nueva');
        $this->assertNotNull($ins->fecha_asignacion, 'fecha_asignacion debe establecerse al asignar tutor');
        $this->assertStringContainsString("Cambiado de 'nueva' a 'asignada'", $ins->notas);
        $this->assertStringContainsString("Asignado a {$tutor->name}", $ins->notas);
        Notification::assertSentTo($tutor, InscripcionAsignada::class);
    }

    public function test_reassign_sends_reassigned_notification_to_previous_tutor()
    {
        Notification::fake();

        $actor1 = $this->usuario;
        $actor2 = User::factory()->create(['name' => 'ActorReasignador']);
        $tutor1 = User::factory()->create(['name' => 'TutorPrev']);
        $tutor2 = User::factory()->create(['name' => 'TutorNext']);

        $ins = $this->inscripcion;

        $this->actingAs($actor1);
        $ins->update(['user_id' => $tutor1->id]);

        $this->actingAs($actor2);
        $ins->update(['user_id' => $tutor2->id]);
        $ins->refresh();

        // El tutor anterior (tutor1) debe recibir InscripcionReasignada
        Notification::assertSentTo($tutor1, \App\Notifications\InscripcionReasignada::class);
    }
}
