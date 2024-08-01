@component('mail::message')
# ¡Hola, {{ $nombreUsuario }}!

@if($incorporacion)
@if($esCoordinador)
¡Eres coordinador@ de `{{ $equipo->nombre }}` de tseyor.org!
@else
¡Has sido incorporado al equipo `{{ $equipo->nombre }}` de tseyor.org!
@endif

@else
Tu solicitud para ingresar al equipo `{{ $equipo->nombre }}` de tseyor.org ha sido rechazada.

Si lo deseas puedes volver a solicitar ser parte del equipo.
@endif

Puedes visitar la página del equipo en el siguiente botón:

@component('mail::button', ['url' => route('equipo', $equipo->slug)])
{{ $equipo->nombre }}
@endcomponent


@if(!$esCoordinador)
Atentamente,
Equipo de coordinadores
@endif

@endcomponent
