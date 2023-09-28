@component('mail::message')
# Tu mensaje fue enviado

@php ucfirst($nombre) @endphp, te informamos que tu mensaje ha sido enviado.

Ten paciencia, pronto recibirás respuesta.


@component('mail::subcopy')

Mensaje enviado:

Nombre: {{nombre}}
Pais: {{pais}}
Correo: {{correo}}
Teléfono:  {{telefono}}
Comentario: {{comentario}}

@endcomponent

[tseyor.org](https://tseyor.org)
@endcomponent
