<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\EmailRateLimiter;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Middleware\RateLimited;

abstract class ThrottleMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 5; // Número máximo de intentos
    public $backoff = 30; // Tiempo en segundos entre intentos

    protected string $jobType = "default";

    public function middleware()
    {
        return [new RateLimited];
    }

}
