<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error {{ $codigo }} - {{ config('app.name', 'TSEYOR.org') }}</title>
    <meta name="description" content="{{ config('seo.description.fallback', 'ONG Mundo Armónico Tseyor') }}">
    <meta name="robots" content="noindex, nofollow">

    <!-- Open Graph básico -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name', 'TSEYOR.org') }}">
    <meta property="og:title" content="Error {{ $codigo }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
            background: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d32f2f;
            margin-bottom: 20px;
        }
        .code {
            font-size: 48px;
            font-weight: bold;
            color: #999;
            margin-bottom: 10px;
        }
        .message {
            color: #666;
            margin-bottom: 30px;
        }
        .back-link {
            color: #1976d2;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">{{ $codigo }}</div>
        <h1>Error en el servidor</h1>
        <p class="message">
            @if($codigo == 404)
                El contenido que buscas no está disponible.
            @elseif($codigo == 403)
                No tienes permiso para acceder a este contenido.
            @elseif($codigo == 429)
                Demasiadas peticiones. Por favor, intenta de nuevo más tarde.
            @elseif($codigo == 503)
                El servicio no está disponible temporalmente.
            @else
                Ha ocurrido un error inesperado.
            @endif
        </p>
        <p>
            <a href="{{ config('app.url', 'https://tseyor.org') }}" class="back-link">
                ← Volver al inicio
            </a>
        </p>
    </div>
</body>
</html>
