@extends('emails.master-usuario')

@section('titulo')
Curso Holístico Tseyor
@endsection

@section('subtitulo')
Tu inscripción fue enviada
@endsection

@section('contenido')

{{ ucfirst(explode(' ', $nombre)[0]) }}, te informamos que tu inscripción al Curso Holístico Tseyor fue enviada con éxito.

Próximamente nos pondremos en contacto contigo.

@component('mail::subcopy')

Datos de inscripción:

Nombre: {{$nombre}}

Fecha de nacimiento: {{$fecha}}

Ciudad: {{$ciudad}}

Región: {{$region}}

Pais: {{$pais}}

Correo: {{$email}}

Teléfono:  {{$telefono}}

Comentario: {{$comentario}}

@endcomponent

@endsection

@section('footerText')
<span>Se te ha enviado este correo por petición tuya.</span>
@endsection
