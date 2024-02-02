@extends('emails.master')

@section('titulo')
Tarjeta de Visita generada
@endsection

@section('subtitulo')
{{ $nombre }}
@endsection

@section('contenido')


@if ($nombre)
# ¡Hola, {{ $nombre }}!

@else
# ¡Hola!

@endif

Te adjuntamos tu tarjeta de visita.


Tu dirección de correo público de Tseyor es: {{$correo_tseyor}}

Esto significa que cualquier mensaje enviado a esta dirección llegará a tu buzón personal de correo.

Más información:

@component('mail::button', ['url' => 'https://tseyor.org/muul/correos.tseyor'])
Correos @tseyor.org
@endcomponent

@endsection
