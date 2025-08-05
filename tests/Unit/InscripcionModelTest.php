<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Inscripcion;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        $this->inscripcion->asignarA($this->usuario, $motivo);

        $this->assertEquals('asignada', $this->inscripcion->estado);
        $this->assertEquals($this->usuario->id, $this->inscripcion->user_id);
        $this->assertNotNull($this->inscripcion->fecha_asignacion);
        $this->assertNotNull($this->inscripcion->ultima_actividad);
        $this->assertNotNull($this->inscripcion->ultima_notificacion, 'ultima_notificacion debe establecerse cuando se envía notificación exitosamente');
        $this->assertEquals($this->usuario->name, $this->inscripcion->asignado);
        $this->assertStringContainsString($motivo, $this->inscripcion->notas);
    }

    public function test_rebotar_actualiza_estado_y_limpia_asignacion()
    {
        // Primero asignar
        $this->inscripcion->asignarA($this->usuario);

        $motivo = 'No responde a emails';
        $this->inscripcion->rebotar($motivo);

        $this->assertEquals('rebotada', $this->inscripcion->estado);
        $this->assertNull($this->inscripcion->user_id);
        $this->assertNull($this->inscripcion->fecha_asignacion);
        $this->assertNull($this->inscripcion->ultima_notificacion);
        $this->assertNotNull($this->inscripcion->ultima_actividad);
        $this->assertStringContainsString($motivo, $this->inscripcion->notas);
        $this->assertStringContainsString('Usuario Test', $this->inscripcion->notas);
    }

    public function test_actualizar_estado_cambia_estado_y_actualiza_actividad()
    {
        $estadoAnterior = $this->inscripcion->estado;
        $nuevoEstado = 'contactado';
        $comentario = 'Primer contacto realizado';

        $this->inscripcion->actualizarEstado($nuevoEstado, $comentario);

        $this->assertEquals($nuevoEstado, $this->inscripcion->estado);
        $this->assertNotNull($this->inscripcion->ultima_actividad);
        $this->assertStringContainsString($estadoAnterior, $this->inscripcion->notas);
        $this->assertStringContainsString($nuevoEstado, $this->inscripcion->notas);
        $this->assertStringContainsString($comentario, $this->inscripcion->notas);
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

        $diasIntervalo = config('inscripciones.notificaciones.dias_intervalo_asignada');
        $fechaEsperada = $this->inscripcion->fecha_asignacion->addDays($diasIntervalo);

        $this->assertEquals(
            $fechaEsperada->format('Y-m-d'),
            $proximaNotificacion->format('Y-m-d')
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
        $this->inscripcion->asignado = 'Usuario Legacy';

        $nombre = $this->inscripcion->nombre_usuario_asignado;

        $this->assertEquals('Usuario Legacy', $nombre);
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
        $this->inscripcion->asignarA($this->usuario, 'Primera asignación');

        // Reasignar al mismo usuario
        $this->inscripcion->asignarA($this->usuario, 'Reasignación');

        $this->assertEquals('asignada', $this->inscripcion->estado);
        $this->assertEquals($this->usuario->id, $this->inscripcion->user_id);
        $this->assertStringContainsString('Reasignación', $this->inscripcion->notas);
    }

    /**
     * Esta prueba verifica que el comportamiento de asignación funciona correctamente
     * cuando las notificaciones están disponibles (caso normal)
     */
    public function test_asignar_a_usuario_establece_ultima_notificacion_correctamente()
    {
        $motivoAsignacion = 'Asignación de prueba normal';

        // Verificar estado inicial
        $this->assertNull($this->inscripcion->ultima_notificacion);

        $this->inscripcion->asignarA($this->usuario, $motivoAsignacion);

        // Verificar que todos los campos se establecen correctamente incluyendo ultima_notificacion
        $this->assertEquals('asignada', $this->inscripcion->estado);
        $this->assertEquals($this->usuario->id, $this->inscripcion->user_id);
        $this->assertNotNull($this->inscripcion->fecha_asignacion);
        $this->assertNotNull($this->inscripcion->ultima_actividad);
        $this->assertNotNull($this->inscripcion->ultima_notificacion, 'ultima_notificacion debe establecerse cuando la notificación se procesa exitosamente');

        // Verificar que ultima_notificacion es reciente (dentro del último minuto)
        $this->assertTrue(
            $this->inscripcion->ultima_notificacion->greaterThanOrEqualTo(now()->subMinute()),
            'ultima_notificacion debe ser una fecha reciente'
        );

        // Verificar que se registra el comentario de asignación
        $this->assertStringContainsString($motivoAsignacion, $this->inscripcion->notas);
    }
}
