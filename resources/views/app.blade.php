<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="view-transition" content="same-origin">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    @if (request()->path() == '/')
        <link rel="preload" fetchpriority="high" as="image" href="/almacen/medios/paginas/galaxy.webp"
            type="image/webp">
        <link rel="preload" fetchpriority="high" as="image" href="/almacen/medios/paginas/nebula-space.webp"
            type="image/webp">
    @endif

    {!! seo($seo ?? null) !!}

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead

</head>

<body class="font-sans antialiased">
    @inertia

    <script>
        (function() {
          var css = document.createElement('link');
          css.href = 'https://fonts.bunny.net/css?family=Figtree';
          css.rel = 'stylesheet';
          css.type = 'text/css';
          document.getElementsByTagName('head')[0].appendChild(css);
        })();
      </script>
</body>

</html>
