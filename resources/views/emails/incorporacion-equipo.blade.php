@component('mail::message')
# ¡Hola, {{ $nombreUsuario }}!

@if($incorporacion)
¡Has sido incorporado al equipo `{{ $equipo->nombre }}` de tseyor.org!

@else
Tu solicitud para ingresar al equipo `{{ $equipo->nombre }}` de tseyor.org ha sido rechazada.

Si lo deseas puedes volver a solicitar ser parte del equipo.
@endif

Puedes visitar la página del equipo en el siguiente botón:

@component('mail::button', ['url' => $equipoUrl])
{{ $equipo->nombre }}
@endcomponent


Atentamente,
Coordinadores de [{{ $equipo->nombre }}]({{ route('equipo', $equipo->slug) }})


[tseyor.org](https://tseyor.org)
@endcomponent
