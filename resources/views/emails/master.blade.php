@component('mail::message')

<h1 style="text-align: center">@yield('titulo')</h1>

<h2 style="text-align: center">@yield('subtitulo')</h2>

@component('mail::subcopy')

@yield('contenido')

@endcomponent

@endcomponent
