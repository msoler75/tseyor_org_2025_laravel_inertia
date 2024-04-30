@extends(backpack_view('blank'))

@section('content')

@php
$rutaLog = storage_path('logs/laravel.log');
$contenidoLog = file_get_contents($rutaLog);
@endphp

<textarea id="mytextarea"  style="font-size: .7rem; width: 100%; height: calc(100vh - 120px);  min-height: 100%;">
    {{$contenidoLog}}
</textarea>

<script>
var textarea = document.getElementById('mytextarea');
textarea.scrollTop = textarea.scrollHeight;
</script>

@endsection
