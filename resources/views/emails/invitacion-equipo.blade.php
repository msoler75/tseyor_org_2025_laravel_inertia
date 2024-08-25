@component('mail::message')
@if ($nombreUsuario)
# Hola, {{ $nombreUsuario }}

@else
# ¡Hola!

@endif

Has sido invitado al equipo {{ $equipo->nombre }} de tseyor.org

@if ($nombreUsuario)
Puedes aceptar esta invitación haciendo clic en el siguiente botón:

@component('mail::button', ['url' => $aceptarUrl])
Aceptar Invitación
@endcomponent

@else
Si no dispones de una cuenta en el sitio tseyor.org, necesitas seguir los siguientes pasos para unirte al equipo:

Paso 1: Crea una cuenta haciendo clic en el siguiente botón:
@component('mail::button', ['url' => route('register')])
Crear cuenta
@endcomponent

Paso 2: Después de crear tu cuenta, puedes hacer clic en el siguiente botón para aceptar la invitación:
@component('mail::button', ['url' => $aceptarUrl])
Aceptar Invitación
@endcomponent

@endif


Si no deseas aceptar la invitación, puedes declinarla haciendo clic en el siguiente enlace:
[Declinar Invitación]({{ $declinarUrl }})

Atentamente,
Coordinadores de [{{ $equipo->nombre }}]({{ route('equipo', $equipo->slug) }})


[tseyor.org](https://tseyor.org)
@endcomponent
