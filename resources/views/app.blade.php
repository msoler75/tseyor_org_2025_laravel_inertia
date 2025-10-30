<!DOCTYPE html>@php use Illuminate\Support\Facades\Cookie; @endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth"
    data-theme="{{ Cookie::get('theme', 'light') }}"
    style="--text-base: {{ Cookie::get('fontSize', 22) }}px;">
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
    <link rel="manifest" href="/build/tseyor-manifest.v2.json">
    <!-- Fallback clásico de Windows/Escritorio: favicon.ico (mejorará el icono del acceso directo/shortcut) -->
    <link rel="shortcut icon" href="/ic/windows11/StoreLogo.scale-100.png">
    <link rel="icon" type="image/png" sizes="256x256" href="/ic/windows11/Square150x150Logo.scale-100.png">
    <!-- Meta Tile image for older Windows/Edge uses -->
    <meta name="msapplication-TileImage" content="/ic/windows11/StoreLogo.scale-100.png">

    <!-- Microsoft / Windows PWA fallbacks (helps Edge/Windows pick correct tile & splash icons) -->
    <meta name="msapplication-TileColor" content="#0a2245">
    <meta name="msapplication-square70x70logo" content="/ic/windows11/Square44x44Logo.targetsize-44.png">
    <meta name="msapplication-square150x150logo" content="/ic/windows11/Square150x150Logo.scale-100.png">
    <meta name="msapplication-wide310x150logo" content="/ic/windows11/Wide310x150Logo.scale-100.png">
    <meta name="msapplication-square310x310logo" content="/ic/windows11/LargeTile.scale-100.png">
    <meta name="msapplication-square44x44logo" content="/ic/windows11/Square44x44Logo.targetsize-44.png">
    <!-- Explicit SplashScreen (helps some Windows/Edge variants) -->
    <meta name="msapplication-SplashScreen" content="/ic/windows11/SplashScreen.scale-100.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Figtree:wght@400;800&display=fallback">

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


    <style>
.loading-circle {
    mask-image: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cg transform-origin='center'%3E%3Ccircle cx='12' cy='12' r='9' fill='none' stroke='black' stroke-width='3' opacity='0.25'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 12 12' to='360 12 12' dur='1s' repeatCount='indefinite'/%3E%3C/circle%3E%3Ccircle cx='12' cy='12' r='9' fill='none' stroke='black' stroke-width='3' stroke-dasharray='10 30 0'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 12 12' to='360 12 12' dur='.75s' repeatCount='indefinite'/%3E%3C/circle%3E%3C/g%3E%3C/svg%3E");
}
</style>
</head>

<body class="font-sans antialiased">
    <!-- PWA Initial Loader - se muestra desde JavaScript si es PWA y hay estado para restaurar -->
    <div id="pwa-initial-loader" class="fixed inset-0 z-50 flex flex-col gap-4 items-center justify-center bg-base-100" style="display: none;">
        <div class="loading loading-circle loading-md text-primary"></div>
        <div id="pwa-loading-message" class="text-center text-lg text-base-content/70 transition-opacity duration-300" style="opacity: 0">
            Cargando estado previo...
        </div>
    </div>

    <script>
        // Mostrar loader inicial si es PWA y hay estado para restaurar
        (function() {
            // Función para detectar si es PWA
            function isPWA() {
                if (typeof window === 'undefined') return false;
                if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) return true;
                if (window.navigator && window.navigator.standalone) return true;
                if (document.referrer && document.referrer.includes('android-app://')) return true;
                return false;
            }

            // Función para verificar si hay estado PWA guardado
            function hasPWAState() {
                try {
                    // Verificar si hay URL guardada
                    return localStorage.getItem('pwa-last-url') !== null;
                } catch (e) {
                    return false;
                }
            }

            // Si es PWA y hay estado para restaurar, mostrar el loader
            if (isPWA() && hasPWAState()) {
                var loader = document.getElementById('pwa-initial-loader');
                if (loader) {
                    loader.style.display = 'flex';
                }
                // Setear cookie para futuras requests SSR
                document.cookie = 'is_pwa=true; path=/; max-age=31536000';
            }
        })();
    </script>


    @inertia


</body>

</html>
