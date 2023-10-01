@extends('emails.master')

@section('titulo')
Tu mensaje fue enviado
@endsection

@section('subtitulo')
@endsection

@section('contenido'    )

{{ ucfirst(explode(' ', $nombre)[0]) }}, te informamos que tu mensaje ha sido enviado.

Ten paciencia, pronto recibirás respuesta.

@component('mail::subcopy')

Mensaje enviado:

Nombre: {{$nombre}}

Pais: {{$pais}}

Correo: {{$correo}}

Teléfono:  {{$telefono}}

Comentario: {{$comentario}}

@endcomponent

@endsection
