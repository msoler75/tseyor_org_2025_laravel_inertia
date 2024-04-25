@extends('emails.master-secretaria')

@section('titulo')
Mensaje de {{$nombre}}
@endsection

@section('subtitulo')
Desde el formulario de contacto
@endsection

@section('contenido')

Nombre: {{$nombre}}

Pais: {{$pais}}

Correo: {{$email}}

Teléfono:  {{$telefono}}

Comentario: {{$comentario}}

@endsection

