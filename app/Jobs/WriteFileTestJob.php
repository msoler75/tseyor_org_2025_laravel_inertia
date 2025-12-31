<?php

namespace App\Jobs;

use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class WriteFileTestJob implements ShouldQueue
{
    use Queueable;

    public string $content;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function middleware(): array
    {
        return [
            new \App\Jobs\Middleware\EmailRateLimited(),
        ];
    }

    public function handle(): void
    {
        $path = storage_path('logs/test-email-job.txt');
        $line = date('c') . ' ' . $this->content . PHP_EOL;
        file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
    }
}
