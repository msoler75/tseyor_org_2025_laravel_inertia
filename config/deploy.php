<?php

// Valores definidos en este archivo (no se usan env() aquí por petición)
$host = 'localhost';
$protocol = 'http';

return [
    'host' => $host,
    'protocol' => $protocol,

    // Endpoints construidos a partir de host/protocol
    'node_modules_endpoint' => $protocol . '://' . $host . '/_sendnodemodules',
    'front_endpoint' => $protocol . '://' . $host . '/_sendbuild',
    'ssr_endpoint' => $protocol . '://' . $host . '/_sendssr',

    // Umbral por defecto para aceptar archivos (segundos)
    'max_age_seconds' => 86400, // 24 horas

    // Exclusiones para node_modules (usadas por DeployNodeModules y ReleasePrepare)
    'node_modules_exclusions' => [
        '/.cache/',
        '/.bin/',
        '/.yarn-integrity',
        '/esbuild/',
        '*.ts',
        '*.md',
        '*.log',
        '/darwin-',
        '/win32-',
        '/vite',
        '/.vite',
        '/.vite-temp',
        '/jiti',
        '/@splidejs',
        '/@lezer',
        '/prosemirror',
        '/unplugin',
        '/dropzone',
        '/@popperjs',
        '/tippy.js',
        '/terser',
        '/daisyui',
        '/@tiptap',
        '/@codemirror',
        '/@rollup',
        '/md-editor-v3',
        '/tailwindcss',
        '/typescript',
        '/caniuse-lite',
        '/@swc',
        '/autoprefixer',
        '/@types',
        '/sucrase',
        '/culori',
        '/tldts-core',
        '/@jridgewell',
        '/@nodelib',
        '/postcss',
    ],
];


