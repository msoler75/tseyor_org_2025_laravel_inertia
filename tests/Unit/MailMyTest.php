<?php

namespace Tests\Unit;

use App\Mail\LimitedMailable;

class MailMyTest extends LimitedMailable
{
    protected string $jobType = 'test_job';
    public function __construct()
    {
        // No se requiere llamar al constructor de la clase base
    }

    public function build()
    {
        // Simula la construcción del correo
        return $this->view('emails.test');
    }
}
