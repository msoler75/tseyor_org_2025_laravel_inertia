@extends('emails.master-secretaria')

@section('titulo')
Inscripción al Curso Holístico
@endsection

@section('subtitulo')
{{$nombre}}
@endsection

@section('contenido')

Nombre: {{$nombre}}

Fecha de nacimiento: {{$fecha}} ({{$edad}} años)

Ciudad: {{$ciudad}}

Región: {{$region}}

Pais: {{$pais}}

Correo: {{$email}}

Teléfono:  {{$telefono}}

Comentario: {{$comentario}}

@endsection

