<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0">

    <!-- IMPORTANTE: noindex para evitar que Google indexe esta página de preview -->
    <!-- Esta página es SOLO para bots sociales (Facebook, Twitter, etc.) -->
    <!-- Google y otros buscadores reciben redirect 301 al contenido real -->
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

    <!-- Esta página se muestra a bots sociales y es indexable por Google -->

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

    <!-- Canonical apunta al contenido original para indicar la fuente real -->
    <link rel="canonical" href="{{ $url_destino }}">

    <!-- Structured Data (Schema.org) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "{{ addslashes($enlace->titulo ?: $enlace->url_original) }}",
        "description": "{{ addslashes($enlace->descripcion ?: 'Enlace compartido de Tseyor.org') }}",
        "url": "{{ request()->url() }}",
        @if($enlace->og_imagen)
        "image": "{{ $enlace->og_imagen }}",
        @endif
        "mainEntity": {
            "@type": "Thing",
            "url": "{{ $url_destino }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "Tseyor",
            "url": "https://tseyor.org"
        }
    }
    </script>

    <!-- CSS mínimo solo para bots que renderizan (raro, pero posible) -->
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }
        .content {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        article {
            margin: 0;
        }
        header h1 {
            margin-top: 0;
            font-size: 2rem;
            color: #1a1a1a;
            line-height: 1.2;
        }
        section {
            margin: 30px 0;
        }
        h2 {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        h3 {
            font-size: 1.2rem;
            color: #34495e;
            margin-top: 20px;
        }
        p {
            margin: 15px 0;
            font-size: 1rem;
        }
        figure {
            margin: 20px 0;
            text-align: center;
        }
        figure img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .url {
            color: #0066cc;
            word-break: break-all;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .url:hover {
            text-decoration: underline;
        }
        footer {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
            color: #666;
        }
        footer a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        footer a:hover {
            text-decoration: underline;
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
            font-size: 1.3rem;
        }
        .preview-banner code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px;
            }
            header h1 {
                font-size: 1.5rem;
            }
            h2 {
                font-size: 1.3rem;
            }
            h3 {
                font-size: 1.1rem;
            }
        }

        /* Touch targets for mobile */
        a {
            min-height: 48px;
            display: inline-block;
            padding: 10px 0;
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
    <main class="content" role="main">
        <article>
            <header>
                <h1>{{ $enlace->titulo ?: 'Contenido compartido de Tseyor' }}</h1>
            </header>

            @if($enlace->og_imagen)
                <figure>
                    <img src="{{ $enlace->og_imagen }}"
                         alt="{{ $enlace->titulo ?: 'Imagen del contenido' }}"
                         style="max-width: 100%; height: auto; border-radius: 8px;">
                </figure>
            @endif

            @if($enlace->descripcion)
                <section>
                    <h2>Descripción</h2>
                    <p>{{ $enlace->descripcion }}</p>
                </section>
            @else
                <section>
                    <p>Contenido compartido desde la plataforma Tseyor. Accede al enlace completo para ver toda la información disponible.</p>
                </section>
            @endif

            <section>
                <h2>Acceder al Contenido</h2>
                <p>Este es un enlace corto que te redirige automáticamente al contenido completo.</p>
                <p>
                    <strong>URL de destino:</strong><br>
                    <a href="{{ $url_destino }}" class="url" rel="noopener">{{ $url_destino }}</a>
                </p>
            </section>

            <footer>
                <h3>Sobre Tseyor</h3>
                <p>
                    La ONG Mundo Armónico Tseyor es una organización dedicada a la divulgación del autodescubrimiento espiritual,
                    el desarrollo personal y la creación de un mundo más armónico.
                    A través de comunicados, talleres, meditaciones y otros recursos,
                    compartimos herramientas para el crecimiento interior y la transformación consciente.
                </p>
                <p>
                    <a href="https://tseyor.org" rel="noopener">Visitar Tseyor.org</a>
                </p>
            </footer>
        </article>
    </main>
</body>
</html>
