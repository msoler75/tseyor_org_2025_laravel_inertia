<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Esta página solo se muestra a bots sociales, no a usuarios humanos -->

    <!-- Metadatos básicos -->
    <title>{{ $enlace->titulo ?: $enlace->url_original }}</title>
    <meta name="description" content="{{ $enlace->descripcion ?: 'Enlace corto - ' . $enlace->url_original }}">

    <!-- SEO básico -->
    @if($enlace->meta_titulo)
        <meta name="title" content="{{ $enlace->meta_titulo }}">
    @endif

    @if($enlace->meta_descripcion)
        <meta name="description" content="{{ $enlace->meta_descripcion }}">
    @endif

    @if($enlace->meta_keywords)
        <meta name="keywords" content="{{ $enlace->meta_keywords }}">
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $enlace->og_tipo ?: 'website' }}">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="{{ $enlace->og_titulo ?: $enlace->titulo ?: $enlace->url_original }}">
    <meta property="og:description" content="{{ $enlace->og_descripcion ?: $enlace->descripcion ?: 'Enlace corto' }}">

    @if($enlace->og_imagen)
        <meta property="og:image" content="{{ $enlace->og_imagen }}">
    @endif

    <!-- Twitter -->
    <meta property="twitter:card" content="{{ $enlace->twitter_card ?: 'summary' }}">
    <meta property="twitter:url" content="{{ request()->url() }}">
    <meta property="twitter:title" content="{{ $enlace->twitter_titulo ?: $enlace->titulo ?: $enlace->url_original }}">
    <meta property="twitter:description" content="{{ $enlace->twitter_descripcion ?: $enlace->descripcion ?: 'Enlace corto' }}">

    @if($enlace->twitter_imagen)
        <meta property="twitter:image" content="{{ $enlace->twitter_imagen }}">
    @endif

    <!-- Canonical URL -->
    @if($enlace->canonical_url)
        <link rel="canonical" href="{{ $enlace->canonical_url }}">
    @else
        <link rel="canonical" href="{{ $url_destino }}">
    @endif

    <!-- CSS mínimo solo para bots que renderizan (raro, pero posible) -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        .content {
            max-width: 600px;
        }
        .url {
            color: #0066cc;
            word-break: break-all;
        }
        .preview-banner {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            color: #856404;
        }
        .preview-banner h2 {
            margin-top: 0;
            color: #856404;
        }
        .preview-banner code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    @if(isset($preview_mode) && $preview_mode)
        <!-- Banner visible solo en modo preview manual -->
        <div class="preview-banner">
            <h2>🔍 Modo Preview - Testing SEO</h2>
            <p><strong>Esta es la vista previa</strong> que ven los bots de redes sociales (Facebook, Twitter, etc.)</p>
            <p>Para ver la redirección normal, accede sin el parámetro <code>?preview=1</code></p>
            <p>URL destino: <a href="{{ $url_destino }}">{{ $url_destino }}</a></p>
        </div>
    @endif

    <!-- Contenido visible para bots que renderizan HTML -->
    <div class="content">
        <h1>{{ $enlace->titulo ?: 'Contenido compartido' }}</h1>

        @if($enlace->descripcion)
            <p>{{ $enlace->descripcion }}</p>
        @endif

        @if($enlace->og_imagen)
            <img src="{{ $enlace->og_imagen }}" alt="{{ $enlace->titulo }}" style="max-width: 100%; height: auto;">
        @endif

        <p>
            Ver contenido completo en:
            <a href="{{ $url_destino }}" class="url">{{ $url_destino }}</a>
        </p>
    </div>
</body>
</html>
