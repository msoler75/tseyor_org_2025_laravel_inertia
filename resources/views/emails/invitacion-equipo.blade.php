@component('mail::message')
@if ($nombreUsuario)
# Hola, {{ $nombreUsuario }}

@else
# ¡Hola!

@endif

Has sido invitado al equipo '{{ $equipo->nombre }}' de tseyor.org

@if ($nombreUsuario)
Puedes aceptar esta invitación haciendo clic en el siguiente botón:

@component('mail::button', ['url' => $aceptarUrl])
Aceptar Invitación
@endcomponent

@else
Si deseas aceptar esta invitación haz click en el siguiente botón (se te pedirá registrarte):

@component('mail::button', ['url' => $aceptarUrl])
Aceptar Invitación y Registrarme
@endcomponent

@endif


Si no deseas aceptar la invitación, puedes declinarla haciendo clic en el siguiente enlace:
[Declinar Invitación]({{ $declinarUrl }})

Atentamente,
Coordinadores de {{ $equipo->nombre }}


[tseyor.org](https://tseyor.org)
@endcomponent
