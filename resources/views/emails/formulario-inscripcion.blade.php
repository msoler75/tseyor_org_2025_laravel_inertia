@extends('emails.master-secretaria')

@section('titulo')
Inscripción al Curso Holístico
@endsection


@section('subtitulo')
{{$inscripcion->nombre}}
@endsection

@section('contenido')

Nombre: {{$inscripcion->nombre}}

@if($inscripcion->fecha_nacimiento)
Fecha de nacimiento: {{ \Carbon\Carbon::parse($inscripcion->fecha_nacimiento)->format('d/m/Y') }} ({{ \Carbon\Carbon::parse($inscripcion->fecha_nacimiento)->age }} años)
@else
Fecha de nacimiento: -
@endif

Ciudad: {{$inscripcion->ciudad}}

Región: {{$inscripcion->region}}

Pais: {{$inscripcion->pais}}

Correo: {{$inscripcion->email}}

Teléfono:  {{$inscripcion->telefono}}

Comentario: {{$inscripcion->comentario}}

<br><br>
<a href="{{$enlace_gestion}}" style="color:#fff;background:#007bff;padding:8px 16px;border-radius:4px;text-decoration:none;" target="_blank">Gestionar esta inscripción</a>

@endsection

