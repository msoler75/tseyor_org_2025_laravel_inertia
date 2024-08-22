<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\TestJob;

class DispatchTestJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:test-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encola 5 instancias del trabajo de prueba TestJob';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        for ($i = 0; $i < 15; $i++) {
            TestJob::dispatch();
            $this->info("Trabajo de prueba $i encolado.");
        }

        return 0;
    }
}