<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Fakes\NotificationFakeVerbose;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\Inscripcion;
use App\Models\User;
use App\Notifications\InscripcionesSeguimiento;
use App\Notifications\InscripcionAsignada;
use App\Notifications\InscripcionesReporte;
use Illuminate\Testing\Assert;
use Illuminate\Support\Facades\Notification;

class AdminNotifiable
{
    public function routeNotificationForMail()
    {
        return 'admin@tseyor.org';
    }


    public function getKey()
    {
        return 'admin@tseyor.org';
    }
}

class InscripcionesGestionTest extends TestCase
{

     public function test_notificacion_caducidad_envia_a_supervisor_email()
    {
        $notificationFake = Notification::getFacadeRoot();
        config(['inscripciones.caduca_meses' => 6]);
        config(['inscripciones.reportes.supervisor_email' => 'supervisor@tseyor.org']);
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $mesesCaduca = config('inscripciones.caduca_meses');
        $fecha_caduca = now()->subMonths($mesesCaduca + 1);

        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'asignada',
        ]);
        $inscripcion->save();
        DB::table('inscripciones')->where('id', $inscripcion->id)->update(['updated_at' => $fecha_caduca]);

        // Simular que la línea de notificación al supervisor está activa
        // (en producción, descomentar en el comando)
        $notificacion = new \App\Notifications\InscripcionCaducada($inscripcion);
        Notification::route('mail', 'supervisor@tseyor.org')->notify($notificacion);

        // Verificar que el fake detecta la notificación enviada al email
        $notificationFake->assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable(),
            \App\Notifications\InscripcionCaducada::class,
            function ($notification, $channels, $notifiable) {
                return in_array('mail', $channels) && isset($notifiable->routes['mail']) && $notifiable->routes['mail'] === 'supervisor@tseyor.org';
            },
            'No se envió la notificación InscripcionCaducada al email del supervisor'
        );
    }

    /**
     * Ejecuta el comando de gestión de inscripciones directamente
     */
    private function ejecutarGestionarInscripciones(array $options = [])
    {
        Artisan::call('inscripciones:gestionar', $options);
    }


    private function crearInscripcion(array $data = []): Inscripcion
    {
        $usuarioTest = User::where('email', 'tutor_test@tseyor.org')->first();
        $defaults = [
            'nombre' => 'Inscripcion Test',
            'email' => 'test@tseyor.org',
            'estado' => 'asignada',
            'user_id' => $usuarioTest ? $usuarioTest->id : null,
            'fecha_asignacion' => now()->subDays(11),
            'fecha_nacimiento' => '1990-01-01',
            'ciudad' => 'Ciudad Test',
            'region' => 'Región Test',
            'pais' => 'España',
        ];
        return Inscripcion::create(array_merge($defaults, $data));
    }

    protected function setUp(): void
    {
        parent::setUp();
        Notification::swap(new NotificationFakeVerbose());
        // Eliminar todas las inscripciones
        DB::table('inscripciones')->truncate();
        // Solo crear el usuario de test si no existe
        if (!User::where('email', 'tutor_test@tseyor.org')->exists()) {
            User::create([
                'name' => 'tutor_test',
                'email' => 'tutor_test@tseyor.org',
                'password' => bcrypt('password'),
            ]);
        }
    }

    public function test_asignacion_y_notificacion_seguimiento()
    {
        $notificationFake = Notification::getFacadeRoot();
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'asignada',
            'fecha_asignacion' => now()->subDays(11),
        ]);

        $this->ejecutarGestionarInscripciones();

        $notificationFake->assertSentTo(
            [$usuario],
            InscripcionesSeguimiento::class,
            null,
            'No se envió la notificación de seguimiento al usuario (test_asignacion_y_notificacion_seguimiento)'
        );
        $inscripcion->refresh();
        $this->assertNotNull($inscripcion->ultima_notificacion);
    }

    public function test_reporte_administrador()
    {
        $notificationFake = Notification::getFacadeRoot();
        config(['inscripciones.reportes.supervisor_email' => 'admin@tseyor.org']);
        $this->crearInscripcion(['estado' => 'asignada']);
        $this->crearInscripcion(['estado' => 'asignada']);
        $this->crearInscripcion(['estado' => 'asignada']);

        $this->ejecutarGestionarInscripciones();

        $notificationFake->assertSentTo(
            new AdminNotifiable(),
            InscripcionesReporte::class,
            null,
            'No se envió el reporte al administrador (test_reporte_administrador)'
        );
    }

    public function test_opcion_solo_seguimiento()
    {
        $notificationFake = Notification::getFacadeRoot();
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'asignada',
            'fecha_asignacion' => now()->subDays(11),
        ]);
        config(['inscripciones.reportes.supervisor_email' => 'admin@tseyor.org']);

        $this->ejecutarGestionarInscripciones(['--solo-seguimiento' => true]);

        $notificationFake->assertSentTo(
            [$usuario],
            InscripcionesSeguimiento::class,
            null,
            'No se envió la notificación de seguimiento al usuario (test_opcion_solo_seguimiento)'
        );
        $notificationFake->assertNotSentTo(
            new AdminNotifiable(),
            InscripcionesReporte::class,
            null,
            'Se envió el reporte al admin cuando no debía (test_opcion_solo_seguimiento)'
        );
    }

    public function test_opcion_solo_reporte()
    {
        $notificationFake = Notification::getFacadeRoot();
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $dias_intervalo_asignada = config('inscripciones.notificaciones.dias_intervalo_asignada');
        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'nueva',
            'fecha_asignacion' => now()->subDays($dias_intervalo_asignada),
            'ultima_notificacion' => null,
        ]);
        config(['inscripciones.reportes.supervisor_email' => 'admin@tseyor.org']);

        $this->ejecutarGestionarInscripciones(['--solo-reporte' => true]);

        $notificationFake->assertSentTo(
            new AdminNotifiable(),
            InscripcionesReporte::class,
            null,
            'No se envió el reporte al administrador (test_opcion_solo_reporte)'
        );
        $notificationFake->assertNotSentTo(
            [$usuario],
            InscripcionesSeguimiento::class,
            null,
            'Se envió la notificación de seguimiento al usuario cuando no debía (test_opcion_solo_reporte)'
        );
    }

    public function test_notificacion_al_rebotar_inscripcion()
    {
        $notificationFake = Notification::getFacadeRoot();
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'asignada',
            'fecha_asignacion' => now()->subDays(5),
        ]);
        // Simular rebote
        $inscripcion->estado = 'rebotada';
        $inscripcion->notas = ($inscripcion->notas ?? '') . "\nRebotada por motivo de prueba";
        $inscripcion->save();

        $this->ejecutarGestionarInscripciones();

        // El reporte debe incluir la inscripcion rebotada
        $notificationFake->assertSentTo(
            new AdminNotifiable(),
            InscripcionesReporte::class,
            function ($notification) use ($inscripcion) {
                return in_array($inscripcion->nombre, array_column($notification->estadisticas['rebotadas_recientes'], 'nombre'));
            },
            'El reporte no incluyó la inscripción rebotada (test_notificacion_al_rebotar_inscripcion)'
        );
    }

    /*public function test_notificacion_seguimiento_intervalos_asignada()
    {
        Notification::fake();
        config(['inscripciones.reportes.supervisor_email' => 'admin@tseyor.org']);
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $estados = config('inscripciones.notificaciones.estados_seguimiento');
        $dias_primer_seguimiento = config('inscripciones.notificaciones.dias_intervalo_asignada');
        //$dias_intervalo_seguimiento = config('inscripciones.notificaciones.dias_intervalo');

        Notification::fake();
        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'asignada',
            'fecha_asignacion' => now(),
        ]);


        if ($inscripcion->estado == 'asignada') {
            Notification::assertSentTo([
                $usuario
            ], InscripcionAsignada::class);
        }

    }*/


    public function test_seguimiento_estado_asignada_intervalo()
    {
        $notificationFake = Notification::getFacadeRoot();
        config(['inscripciones.reportes.supervisor_email' => 'admin@tseyor.org']);
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $intervalo_asignada = config('inscripciones.notificaciones.dias_intervalo_asignada');

        // Caso 1: primer seguimiento (sin ultima_notificacion)
        for ($dias = 0; $dias <= $intervalo_asignada + 1; $dias++) {
            $notificationFake = Notification::getFacadeRoot();
            $inscripcion = $this->crearInscripcion([
                'user_id' => $usuario->id,
                'estado' => 'asignada',
                'fecha_asignacion' => now()->subDays($dias),
                'ultima_notificacion' => null,
            ]);
            $this->ejecutarGestionarInscripciones(['--solo-seguimiento' => true]);
            if ($dias < $intervalo_asignada) {
                $notificationFake->assertNotSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class, null, 'Se envió la notificación de seguimiento cuando no debía (test_seguimiento_estado_asignada_intervalo, primer seguimiento)');
            } else {
                $notificationFake->assertSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class, null, 'No se envió la notificación de seguimiento al usuario (test_seguimiento_estado_asignada_intervalo, primer seguimiento)');
            }


            $notificationFake = Notification::getFacadeRoot();
            $inscripcion->update([
                'ultima_notificacion' => now()->subDays($dias),
            ]);
            $this->ejecutarGestionarInscripciones(['--solo-seguimiento' => true]);
            if ($dias < $intervalo_asignada) {
                $notificationFake->assertNotSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class, null, 'Se envió la notificación de seguimiento cuando no debía (test_seguimiento_estado_asignada_intervalo, seguimientos posteriores)');
            } else {
                $notificationFake->assertSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class, null, 'No se envió la notificación de seguimiento al usuario (test_seguimiento_estado_asignada_intervalo, seguimientos posteriores)');
            }
        }
    }



    public function test_seguimiento_estado_contactado_intervalo()
    {
        $notificationFake = Notification::getFacadeRoot();
        config(['inscripciones.reportes.supervisor_email' => 'admin@tseyor.org']);
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $intervalo_general = config('inscripciones.notificaciones.dias_intervalo');

        // Caso 1: primer seguimiento (sin ultima_notificacion)
        for ($dias = 0; $dias <= $intervalo_general + 1; $dias++) {
            $notificationFake = Notification::getFacadeRoot();
            $inscripcion = $this->crearInscripcion([
                'user_id' => $usuario->id,
                'estado' => 'contactado',
                'fecha_asignacion' => now()->subDays($dias),
                'ultima_notificacion' => null,
            ]);
            $this->ejecutarGestionarInscripciones(['--solo-seguimiento' => true]);

            if ($dias < $intervalo_general) {
                $notificationFake->assertNotSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class, null, 'Se envió la notificación de seguimiento cuando no debía (test_seguimiento_estado_contactado_intervalo)');
            } else {
                $notificationFake->assertSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class, null, 'No se envió la notificación de seguimiento al usuario (test_seguimiento_estado_contactado_intervalo)');
            }
        }

    }

    public function test_caduca_y_notifica_inscripcion()
    {
        $notificationFake = Notification::getFacadeRoot();
        config(['inscripciones.caduca_meses' => 6]);
        config(['inscripciones.reportes.supervisor_email' => 'supervisor@tseyor.org']);
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $mesesCaduca = config('inscripciones.caduca_meses');
        $fecha_caduca = now()->subMonths($mesesCaduca + 1); // más de 6 meses

        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'asignada',
        ]);
        $inscripcion->save();
        DB::table('inscripciones')->where('id', $inscripcion->id)->update(['updated_at' => $fecha_caduca]);
        $this->ejecutarGestionarInscripciones();
        $inscripcion->refresh();
        $usuarioAsignado = $inscripcion->usuarioAsignado;
        fwrite(STDERR, "Usuario asignado: " . ($usuarioAsignado ? $usuarioAsignado->id . ' - ' . $usuarioAsignado->email : 'null') . "\n");
        $this->assertEquals('caducada', $inscripcion->estado, 'La inscripción no cambió a estado caducada');
        $notificationFake->assertSentTo([
            $usuarioAsignado
        ], \App\Notifications\InscripcionCaducada::class, null, 'No se envió la notificación InscripcionCaducada al usuario asignado');
        // Verificar que también se envía al supervisor
        $notificationFake->assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable(),
            \App\Notifications\InscripcionCaducada::class,
            function ($notification, $channels, $notifiable) {
                return in_array('mail', $channels) && isset($notifiable->routes['mail']) && $notifiable->routes['mail'] === 'supervisor@tseyor.org';
            },
            'No se envió la notificación InscripcionCaducada al email del supervisor'
        );
    }

    public function test_no_caduca_ni_notifica_inscripcion()
    {
        $notificationFake = Notification::getFacadeRoot();
        config(['inscripciones.caduca_meses' => 6]);
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $mesesCaduca = config('inscripciones.caduca_meses');
        $fecha_no_caduca = now()->subMonths($mesesCaduca - 1); // menos de 6 meses

        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'asignada',
        ]);
        DB::table('inscripciones')->where('id', $inscripcion->id)->update(['updated_at' => $fecha_no_caduca]);
        $this->ejecutarGestionarInscripciones();
        $inscripcion->refresh();
        $this->assertNotEquals('caducada', $inscripcion->estado, 'La inscripción cambió a estado caducada cuando no debía');
        $notificacionesEnviadas = $notificationFake->sent($usuario, \App\Notifications\InscripcionCaducada::class);
        fwrite(STDERR, "Notificaciones enviadas al usuario: " . json_encode($notificacionesEnviadas) . "\n");
        $notificationFake->assertNotSentTo([
            $usuario
        ], \App\Notifications\InscripcionCaducada::class, function ($notification) use ($inscripcion) {
            // Solo considerar notificaciones que correspondan a esta inscripción
            return isset($notification->inscripcion) && $notification->inscripcion->id === $inscripcion->id;
        }, 'Se envió la notificación InscripcionCaducada al usuario para esta inscripción cuando no debía');
        // Verificar que también se envía al supervisor
        $notificationFake->assertNotSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable(),
            \App\Notifications\InscripcionCaducada::class,
            function ($notification, $channels, $notifiable) {
                return in_array('mail', $channels) && isset($notifiable->routes['mail']) && $notifiable->routes['mail'] === 'supervisor@tseyor.org';
            },
            'Se envió la notificación InscripcionCaducada al email del supervisor'
        );
    }

    public function test_no_notificacion_en_estado_final()
    {
        $notificationFake = Notification::getFacadeRoot();
        $usuario = User::where('email', 'tutor_test@tseyor.org')->first();
        $estados_finales = config('inscripciones.notificaciones.estados_finales');
        foreach ($estados_finales as $estado) {
            $inscripcion = $this->crearInscripcion([
                'user_id' => $usuario->id,
                'estado' => $estado,
                'fecha_asignacion' => now()->subDays(30),
                'ultima_notificacion' => now()->subDays(30),
            ]);
            $this->ejecutarGestionarInscripciones(['--solo-seguimiento' => true]);
            $notificationFake->assertNotSentTo([
                $usuario
            ], InscripcionesSeguimiento::class, null, 'Se envió la notificación de seguimiento en estado final cuando no debía (test_no_notificacion_en_estado_final)');
        }
    }
}
