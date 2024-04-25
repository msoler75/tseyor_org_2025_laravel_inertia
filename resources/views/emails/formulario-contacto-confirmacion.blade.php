@extends('emails.master-usuario')

@section('titulo')
Tu mensaje fue enviado
@endsection

@section('subtitulo')
@endsection

@section('contenido')

{{ ucfirst(explode(' ', $nombre)[0]) }}, te informamos que tu mensaje ha sido enviado.

Ten paciencia, pronto recibirás respuesta.

@component('mail::subcopy')

Mensaje enviado:

Nombre: {{$nombre}}

Pais: {{$pais}}

Correo: {{$email}}

Teléfono:  {{$telefono}}

Comentario: {{$comentario}}

@endcomponent

@slot('footerText')
Recibes este correo porque has solicitado la inscripción al Curso Holístico de Tseyor.
@endslot

@endsection
