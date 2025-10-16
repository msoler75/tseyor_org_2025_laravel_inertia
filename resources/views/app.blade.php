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

    @if(isset($noindex) && $noindex)
    <meta name="robots" content="noindex">
    @endif

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1e40af">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Tseyor">
    <link rel="apple-touch-icon" href="/ic/ios/180.png">
    <link rel="apple-touch-icon" sizes="57x57" href="/ic/ios/57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/ic/ios/60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/ic/ios/72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/ic/ios/76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/ic/ios/114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/ic/ios/120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/ic/ios/144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/ic/ios/152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/ic/ios/180.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/ic/ios/16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/ic/ios/32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/ic/android/android-launchericon-192-192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/ic/android/android-launchericon-512-512.png">
    <link rel="manifest" href="/build/tseyor-manifest.json">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    @if (false && request()->path() == '/')
        <link rel="preload" fetchpriority="high" as="image" href="/almacen/medios/paginas/galaxy.webp"
            type="image/webp">
        <link rel="preload" fetchpriority="high" as="image" href="/almacen/medios/paginas/nebula-space.webp"
            type="image/webp">
    @endif

    {!! seo($seo ?? null) !!}

    <!-- Scripts -->
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead

    <script>
        window.disableAnalytics = {{ request()->has('noanalytics') ? 'true' : 'false' }};
    </script>

    @if(!request()->has('noanalytics'))
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "s4eie0h4o2");
</script>
    @endif
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
