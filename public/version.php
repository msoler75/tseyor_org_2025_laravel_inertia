<?php
/**
 * version.php - Muestra la versión desplegada del repositorio.
 *
 * Acceso: https://tu-dominio/version.php
 * Lee el commit actual desde la carpeta .git del proyecto.
 */

$candidates = [
    __DIR__,              // si está en la raíz del proyecto
    dirname(__DIR__),     // si está en public/
    dirname(dirname(__DIR__)),
];

$root = null;
foreach ($candidates as $c) {
    if (is_dir($c . '/.git')) {
        $root = $c;
        break;
    }
}

header('Content-Type: text/plain; charset=utf-8');

if (!$root) {
    // Fallback: leer .git/HEAD directamente (por si exec está deshabilitado)
    $headFile = __DIR__ . '/../.git/HEAD';
    if (file_exists($headFile)) {
        $ref = trim(file_get_contents($headFile));
        if (str_starts_with($ref, 'ref: ')) {
            $refFile = __DIR__ . '/../.git/' . substr($ref, 5);
            if (file_exists($refFile)) {
                $ref = trim(file_get_contents($refFile));
            }
        }
        echo "Commit: " . $ref . "\n";
    } else {
        echo "No se encontró la carpeta .git\n";
    }
    exit;
}

function run($cmd, $cwd) {
    $out = [];
    $code = 0;
    exec('cd ' . escapeshellarg($cwd) . ' && ' . $cmd . ' 2>&1', $out, $code);
    return [$code, trim(implode("\n", $out))];
}

[$c1, $hash]   = run('git rev-parse --short HEAD', $root);
[$c2, $full]   = run('git rev-parse HEAD', $root);
[$c3, $date]   = run('git log -1 --format=%ci', $root);
[$c4, $msg]    = run('git log -1 --format=%s', $root);
[$c5, $branch] = run('git rev-parse --abbrev-ref HEAD', $root);
[$c6, $remote] = run('git rev-parse origin/main', $root);

echo "Proyecto: " . basename($root) . "\n";
echo "Rama:     " . $branch . "\n";
echo "Commit:   " . $hash . " (" . $full . ")\n";
echo "Fecha:    " . $date . "\n";
echo "Mensaje:  " . $msg . "\n";
echo "Remoto:   " . $remote . "\n";
echo "\n";
echo ($hash === substr($remote, 0, strlen($hash))
    ? "ESTADO: al dia con origin/main\n"
    : "ESTADO: hay cambios sin desplegar (HEAD != origin/main)\n");
