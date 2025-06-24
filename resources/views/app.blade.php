<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth"
    @if(\Illuminate\Support\Facades\Cookie::get('theme') === 'dark')
        data-theme="dark"
    @endif
    >

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
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead

<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "s4eie0h4o2");
</script>
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
