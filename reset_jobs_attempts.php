<?php

use Illuminate\Support\Facades\DB;

// Cambiar la ruta del autoload para versiones modernas de Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Reseteando los intentos en la tabla jobs...\n";

try {
    // Actualizar los intentos a 0
    DB::table('jobs')->update(['attempts' => 0]);
    echo "Intentos reseteados correctamente.\n";
} catch (\Exception $e) {
    echo "Error al resetear los intentos: " . $e->getMessage() . "\n";
}
