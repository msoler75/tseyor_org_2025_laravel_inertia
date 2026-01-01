<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class EmailRateLimitReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:rate-limit-reset {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the email rate limiter cache to unblock pending jobs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will reset the email rate limiter cache. Continue?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Limpiar las claves de rate limiting
        $rateLimitKey = 'email_rate_limit';
        $pendingKey = 'email_rate_limit_pending';

        // Mostrar estadísticas antes de limpiar
        $timestamps = Cache::get($rateLimitKey, []);
        $pending = Cache::get($pendingKey, []);

        $this->info('Current state:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Tracked timestamps', count($timestamps)],
                ['Pending jobs', count($pending)],
            ]
        );

        // Limpiar
        Cache::forget($rateLimitKey);
        Cache::forget($pendingKey);

        $this->info('✓ Email rate limiter cache has been reset.');
        $this->info('✓ Pending jobs will now be processed according to the rate limits.');

        return Command::SUCCESS;
    }
}
