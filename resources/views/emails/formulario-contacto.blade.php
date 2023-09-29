@extends('emails.master')

@section('titulo')
Mensaje de {{$nombre}}
@endsection

@section('subtitulo')
Desde el formulario de contacto
@endsection

@section('contenido')

Nombre: {{$nombre}}

Pais: {{$pais}}

Correo: {{$correo}}

Teléfono:  {{$telefono}}

Comentario: {{$comentario}}

@endsection

