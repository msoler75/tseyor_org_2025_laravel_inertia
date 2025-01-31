<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearImageCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imagecache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear image cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cachePath = storage_path('framework/image_cache');
        if (file_exists($cachePath)) {
            $files = scandir($cachePath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    unlink($cachePath . '/' . $file);
                }
            }
            $this->info('Image cache cleared successfully.');
        } else {
            $this->info('Image cache directory does not exist.');
        }
        return 0;
    }
}
