@extends('emails.master-usuario')

@section('titulo')
    {{ $titulo }}
@endsection

@section('contenido')
    {!! nl2br(e($texto)) !!}
@endsection

@section('footerText')
    <div style="font-size: 0.8em; color: #555; margin-top: 20px;">
        <span>Recibes este boletín porque te has suscrito a nuestras comunicaciones. </span>
        <span>Puedes
            <a href="{{ route('boletin.configurar.mostrar', ['token' => $token]) }}">configurar la frecuencia</a>  de los correos o
            <a href="{{ route('boletin.desuscribir', ['token' => $token]) }}">darte de baja</a>  en cualquier momento.</span>
    </div>
@endsection
