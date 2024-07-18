@component('mail::message')

{{$nombre}}

Debes validar tu dirección de correo electrónico {{$email}}.

@component('mail::button', ['url' => $url])
Validar Correo
@endcomponent

@endcomponent
