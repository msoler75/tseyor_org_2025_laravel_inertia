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
    </style>
</head>
<body>
    <!-- Contenido visible solo para bots que renderizan HTML -->
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
