<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\Inscripcion;
use App\Models\User;
use App\Notifications\InscripcionesSeguimiento;
use App\Notifications\InscripcionAsignada;
use App\Notifications\InscripcionesReporte;
use Illuminate\Testing\Assert;

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

    private function crearInscripcion(array $data = []): Inscripcion
    {
        $defaults = [
            'nombre' => 'Inscripcion Test',
            'email' => 'test@tseyor.org',
            'estado' => 'asignada',
            'user_id' => User::first()->id ?? null,
            'fecha_asignacion' => now()->subDays(11),
            'fecha_nacimiento' => '1990-01-01',
            'ciudad' => 'Ciudad Test',
            'region' => 'Región Test',
            'pais' => 'España',
        ];
        return Inscripcion::create(array_merge($defaults, $data));
    }
    // use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::table('inscripciones')->truncate();
        // Crear usuario de prueba si no existe
        if (!User::where('name', 'usuario1')->exists()) {
            User::create([
                'name' => 'usuario1',
                'email' => 'usuario1@tseyor.org',
                'password' => bcrypt('password'),
            ]);
        }
    }

    public function test_asignacion_y_notificacion_seguimiento()
    {
        Notification::fake();
        $usuario = User::first();
        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'asignada',
            'fecha_asignacion' => now()->subDays(11),
        ]);

        Artisan::call('inscripciones:gestionar');

        Notification::assertSentTo(
            [$usuario],
            InscripcionesSeguimiento::class
        );
        $inscripcion->refresh();
        $this->assertNotNull($inscripcion->ultima_notificacion);
    }

    public function test_reporte_administrador()
    {
        Notification::fake();
        config(['inscripciones.reportes.admin_email' => 'admin@tseyor.org']);
        $this->crearInscripcion(['estado' => 'asignada']);
        $this->crearInscripcion(['estado' => 'asignada']);
        $this->crearInscripcion(['estado' => 'asignada']);

        Artisan::call('inscripciones:gestionar');

        Notification::assertSentTo(
            new AdminNotifiable(),
            InscripcionesReporte::class
        );
    }

    public function test_opcion_solo_seguimiento()
    {
        Notification::fake();
        $usuario = User::first();
        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'asignada',
            'fecha_asignacion' => now()->subDays(11),
        ]);
        config(['inscripciones.reportes.admin_email' => 'admin@tseyor.org']);

        Artisan::call('inscripciones:gestionar', ['--solo-seguimiento' => true]);

        Notification::assertSentTo(
            [$usuario],
            InscripcionesSeguimiento::class
        );
        Notification::assertNotSentTo(
            new AdminNotifiable(),
            InscripcionesReporte::class
        );
    }

    public function test_opcion_solo_reporte()
    {
        Notification::fake();
        $usuario = User::first();
        $dias_intervalo_asignada = config('inscripciones.notificaciones.dias_intervalo_asignada');
        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'nueva',
            'fecha_asignacion' => now()->subDays($dias_intervalo_asignada),
            'ultima_notificacion' => null,
        ]);
        config(['inscripciones.reportes.admin_email' => 'admin@tseyor.org']);

        Artisan::call('inscripciones:gestionar', ['--solo-reporte' => true]);

        Notification::assertSentTo(new AdminNotifiable(), InscripcionesReporte::class);
        Notification::assertNotSentTo([$usuario], InscripcionesSeguimiento::class);
    }

    public function test_notificacion_al_rebotar_inscripcion()
    {
        Notification::fake();
        $usuario = User::first();
        $inscripcion = $this->crearInscripcion([
            'user_id' => $usuario->id,
            'estado' => 'asignada',
            'fecha_asignacion' => now()->subDays(5),
        ]);
        // Simular rebote
        $inscripcion->estado = 'rebotada';
        $inscripcion->notas = ($inscripcion->notas ?? '') . "\nRebotada por motivo de prueba";
        $inscripcion->save();

        Artisan::call('inscripciones:gestionar');

        // El reporte debe incluir la inscripcion rebotada
        Notification::assertSentTo(
            new AdminNotifiable(),
            InscripcionesReporte::class,
            function ($notification) use ($inscripcion) {
                return in_array($inscripcion->nombre, array_column($notification->estadisticas['rebotadas_recientes'], 'nombre'));
            }
        );
    }

    /*public function test_notificacion_seguimiento_intervalos_asignada()
    {
        Notification::fake();
        config(['inscripciones.reportes.admin_email' => 'admin@tseyor.org']);
        $usuario = User::first();
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
        Notification::fake();
        config(['inscripciones.reportes.admin_email' => 'admin@tseyor.org']);
        $usuario = User::first();
        $intervalo_asignada = config('inscripciones.notificaciones.dias_intervalo_asignada');

        // Caso 1: primer seguimiento (sin ultima_notificacion)
        for ($dias = 0; $dias <= $intervalo_asignada + 1; $dias++) {
            Notification::fake();
            $inscripcion = $this->crearInscripcion([
                'user_id' => $usuario->id,
                'estado' => 'asignada',
                'fecha_asignacion' => now()->subDays($dias),
                'ultima_notificacion' => null,
            ]);
            Artisan::call('inscripciones:gestionar', ['--solo-seguimiento' => true]);
            if ($dias < $intervalo_asignada) {
                Notification::assertNotSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class);
            } else {
                Notification::assertSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class);
            }
        }

        // Caso 2: seguimientos posteriores (con ultima_notificacion)
        $inscripcion->update([
            'ultima_notificacion' => now(),
            'fecha_asignacion' => now()->subDays($intervalo_asignada),
        ]);
        for ($dias = 0; $dias <= $intervalo_asignada + 1; $dias++) {
            Notification::fake();
            $inscripcion->update([
                'ultima_notificacion' => now()->subDays($dias),
            ]);
            Artisan::call('inscripciones:gestionar', ['--solo-seguimiento' => true]);
            if ($dias < $intervalo_asignada) {
                Notification::assertNotSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class);
            } else {
                Notification::assertSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class);
            }
        }
    }



    public function test_seguimiento_estado_contactado_intervalo()
    {
               Notification::fake();
        config(['inscripciones.reportes.admin_email' => 'admin@tseyor.org']);
        $usuario = User::first();
        $intervalo_general = config('inscripciones.notificaciones.dias_intervalo');

        // Caso 1: primer seguimiento (sin ultima_notificacion)
        for ($dias = 0; $dias <= $intervalo_general + 1; $dias++) {
            Notification::fake();
            $inscripcion = $this->crearInscripcion([
                'user_id' => $usuario->id,
                'estado' => 'contactado',
                'fecha_asignacion' => now()->subDays($dias),
                'ultima_notificacion' => null,
            ]);
            Artisan::call('inscripciones:gestionar', ['--solo-seguimiento' => true]);
            if ($dias < $intervalo_general) {
                Notification::assertNotSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class);
            } else {
                Notification::assertSentTo([
                    $usuario
                ], InscripcionesSeguimiento::class);
            }
        }

    }
}
