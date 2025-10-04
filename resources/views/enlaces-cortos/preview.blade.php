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

    <!-- Hreflang tags para indicar idioma español -->
    <link rel="alternate" hreflang="es" href="{{ request()->url() }}">
    <link rel="alternate" hreflang="x-default" href="{{ request()->url() }}">

    <!-- Structured Data (Schema.org) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": {{ json_encode($enlace->titulo ?: $enlace->url_original) }},
        "description": {{ json_encode($enlace->descripcion ?: 'Contenido compartido desde Tseyor - ONG Mundo Armónico Tseyor') }},
        "url": "{{ request()->url() }}",
        "inLanguage": "es-ES",
        @if($enlace->og_imagen)
        "image": {
            "@type": "ImageObject",
            "url": "{{ $enlace->og_imagen }}",
            "width": 1200,
            "height": 630
        },
        @endif
        "mainEntity": {
            "@type": "Thing",
            "url": "{{ $url_destino }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "ONG Mundo Armónico Tseyor",
            "url": "https://tseyor.org",
            "logo": {
                "@type": "ImageObject",
                "url": "https://tseyor.org/logo.png"
            },
            "sameAs": [
                "https://www.facebook.com/tseyor",
                "https://twitter.com/TSEYOR",
                "https://www.youtube.com/@tseyor",
                "https://www.instagram.com/tseyor_org"
            ]
        },
        "breadcrumb": {
            "@type": "BreadcrumbList",
            "itemListElement": [{
                "@type": "ListItem",
                "position": 1,
                "name": "Inicio",
                "item": "https://tseyor.org"
            }]
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

        /* Social links styling */
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        .social-links a {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            background: #f0f0f0;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            font-size: 0.95rem;
            transition: background 0.2s;
        }
        .social-links a:hover {
            background: #e0e0e0;
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

            <section>
                <h2>¿Qué es Tseyor?</h2>
                <p>
                    <strong>Tseyor</strong> es una comunidad internacional dedicada al autodescubrimiento y el desarrollo de la consciencia.
                    Fundada con el propósito de promover un mundo más armónico, ofrece herramientas prácticas para el crecimiento personal
                    y espiritual a través de diversas actividades y recursos.
                </p>
                <p>
                    Nuestra misión es facilitar el despertar de la consciencia mediante la comprensión de las leyes universales,
                    el desarrollo de capacidades latentes y la integración de los principios de hermandad, amor y servicio.
                    Trabajamos en la construcción de una sociedad basada en valores elevados y en armonía con el entorno.
                </p>
            </section>

            <section>
                <h2>Nuestras Actividades</h2>
                <h3>Comunicados y Enseñanzas</h3>
                <p>
                    Regularmente publicamos <strong>comunicados interdimensionales</strong> que ofrecen orientación y reflexiones
                    sobre temas espirituales, personales y sociales. Estos mensajes provienen de fuentes elevadas de consciencia
                    y están diseñados para inspirar, guiar y elevar la comprensión de quienes buscan un camino de crecimiento interior.
                </p>

                <h3>Talleres y Cursos</h3>
                <p>
                    Organizamos <strong>talleres prácticos, cursos especializados y retiros</strong> donde los participantes aprenden
                    técnicas de meditación, relajación, visualización creativa y desarrollo de capacidades intuitivas.
                    Estas actividades se realizan tanto presencialmente como en formato virtual, permitiendo la participación
                    de personas de todo el mundo.
                </p>

                <h3>Meditaciones Guiadas</h3>
                <p>
                    Ofrecemos <strong>sesiones de meditación guiada</strong> que facilitan el contacto con estados superiores de consciencia.
                    Estas prácticas incluyen meditaciones holográficas, trabajos energéticos y ejercicios de introspección
                    que ayudan a integrar las enseñanzas en la vida cotidiana.
                </p>

                <h3>Biblioteca y Recursos</h3>
                <p>
                    Nuestra <strong>biblioteca digital</strong> contiene libros, documentos, audios y videos con las enseñanzas
                    y experiencias compartidas a lo largo de los años. Todo este material está disponible gratuitamente
                    para quienes deseen profundizar en su camino de autodescubrimiento.
                </p>
            </section>

            <section>
                <h2>Únete a Nuestra Comunidad</h2>
                <p>
                    Si sientes el llamado al crecimiento personal y espiritual, te invitamos a explorar nuestros recursos,
                    participar en nuestras actividades y formar parte de esta comunidad global de buscadores de la verdad
                    y la armonía. Juntos construimos un mundo mejor, más consciente y fraternal.
                </p>
                <p>
                    <strong>Visita nuestro sitio web principal:</strong><br>
                    <a href="https://tseyor.org" rel="noopener" class="url">https://tseyor.org</a>
                </p>
            </section>

            <footer>
                <h3>Síguenos en Redes Sociales</h3>
                <p>Conéctate con nosotros y mantente actualizado con nuestras últimas publicaciones, eventos y actividades:</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/tseyor" target="_blank" rel="noopener noreferrer">
                        📘 Facebook
                    </a>
                    <a href="https://twitter.com/TSEYOR" target="_blank" rel="noopener noreferrer">
                        🐦 X (Twitter)
                    </a>
                    <a href="https://www.youtube.com/@tseyor" target="_blank" rel="noopener noreferrer">
                        ▶️ YouTube
                    </a>
                    <a href="https://www.instagram.com/tseyor_org" target="_blank" rel="noopener noreferrer">
                        📷 Instagram
                    </a>
                </div>
                <p style="margin-top: 20px;">
                    <small>
                        © {{ date('Y') }} ONG Mundo Armónico Tseyor. Organización sin ánimo de lucro dedicada al desarrollo de la consciencia.
                    </small>
                </p>
            </footer>
        </article>
    </main>
</body>
</html>
