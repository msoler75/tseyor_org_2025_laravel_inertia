<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\EmailRateLimiter;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Tests\Unit\MailMyTest;
use Illuminate\Support\Facades\Config;


class LimitedMailableTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::clear(); // Clear cache before each test

        # establecemos unos limites de envío
        Config::set('mail.rate_limit.max.overall', 3);
        Config::set('mail.rate_limit.window', 5); // 5 seconds window

        // Start MailHog Docker container
        // shell_exec('docker run -d -p 1025:1025 -p 8025:8025 --name mailhog mailhog/mailhog');

        // Configure SMTP settings to use MailHog
        config(['mail.default' => 'smtp']);
        config(['mail.mailers.smtp.host' => 'localhost']);
        config(['mail.mailers.smtp.port' => 1025]);
        config(['mail.mailers.smtp.username' => null]);
        config(['mail.mailers.smtp.password' => null]);

        Log::info('Asegurate que el contenedor de MailHog esté corriendo. Puedes iniciar el contenedor con:');
        Log::info('docker run -d -p 1025:1025 -p 8025:8025 --name mailhog mailhog/mailhog');
        Log::info('Accede a la interfaz web de MailHog en: http://localhost:8025');
    }

    public function tearDown(): void
    {
        // No detener el contenedor de MailHog tras los tests
        parent::tearDown();

        // pero avisar que el servicio docker no se ha detenido
        Log::info('MailHog Docker container is still running. Please stop it manually if needed.');
        Log::info('To stop the container, run: docker stop mailhog');
    }


    public function testSendEmailsExceedingLimit()
    {
        $rateLimiter = new EmailRateLimiter();
        $mailable = new MailMyTest();

        $user = User::where('name', 'admin')->firstOrFail(); // Usamos el usuario con nombre "admin"
        $this->assertNotEmpty($user->email, 'El usuario admin debe tener un email válido.');

        for ($i = 0; $i < 3; $i++) { // Límite reducido a 3 según la configuración
            $mailable->to($user->email); // Asignamos el destinatario
            $mailable->send(app('mailer')); // Enviamos el correo
            sleep(1); // Esperamos 1 segundo entre envíos
        }

        $this->assertFalse($rateLimiter->canSend('test_job'));

        sleep(3); // Esperamos 3 segundos para verificar que se puede enviar de nuevo según la ventana configurada
        $this->assertTrue($rateLimiter->canSend('test_job'));
    }
}
