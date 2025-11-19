<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProfileZiggy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:profile-ziggy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Asegurar que la clase esté cargada y el cache limpio para medir solo el constructor
        new \Tightenco\Ziggy\Ziggy();
        \Tightenco\Ziggy\Ziggy::clearRoutes();

        // Ahora medir el constructor (que cargará las rutas)
        $start = microtime(true);
        $ziggy = new \Tightenco\Ziggy\Ziggy();
        $end = microtime(true);
        $time = ($end - $start) * 1000;
        $this->line(number_format($time, 2));
    }
}
