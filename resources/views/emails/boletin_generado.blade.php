@component('mail::message')
@if (!empty($greeting))
# {{ $greeting }}
@else
# {{ $boletin->titulo }}
@endif

@if(isset($intro))
{{ $intro }}
@endif

**Contenido del boletín:**

@if(isset($boletin->texto))
---
{!! $boletin->texto !!}
---
@endif

@if(isset($actionText))
@component('mail::button', ['url' => $actionUrl])
{{ $actionText }}
@endcomponent
@endif

Atentamente,<br>
El equipo de {{ config('app.name') }}

[{{ config('app.name') }}]({{ config('app.url') }})
@endcomponent
