@component('mail::message')
@if ($nombre)
# ¡Hola, {{ $nombre }}!

@else
# ¡Hola!

@endif

Aquí tienes tu tarjeta de visita.


[tseyor.org](https://tseyor.org)
@endcomponent
