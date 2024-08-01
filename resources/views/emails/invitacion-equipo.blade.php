@component('mail::message')
@if ($nombreUsuario)
# ?Hola, {{ $nombreUsuario }}!

@else
# ?Hola!

@endif

?Has sido invitado al equipo {{ $equipo->nombre }} de tseyor.org!

@if ($nombreUsuario)
Puedes aceptar esta invitaci?n haciendo clic en el siguiente bot?n:

@component('mail::button', ['url' => $aceptarUrl])
Aceptar Invitaci?n
@endcomponent

@else
Si no dispones de una cuenta en el sitio tseyor.org, necesitas seguir los siguientes pasos para unirte al equipo:

Paso 1: Crea una cuenta haciendo clic en el siguiente bot?n:
@component('mail::button', ['url' => route('register')])
Crear cuenta
@endcomponent

Paso 2: Despu?s de crear tu cuenta, puedes hacer clic en el siguiente bot?n para aceptar la invitaci?n:
@component('mail::button', ['url' => $aceptarUrl])
Aceptar Invitaci?n
@endcomponent

@endif


Si no deseas aceptar la invitaci?n, puedes declinarla haciendo clic en el siguiente enlace:
[Declinar Invitaci?n]({{ $declinarUrl }})

Atentamente,
Coordinadores de [{{ $equipo->nombre }}]({{ route('equipo', $equipo->slug) }})


[tseyor.org](https://tseyor.org)
@endcomponent
