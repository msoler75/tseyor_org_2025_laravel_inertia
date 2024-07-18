@component('mail::message')

# {{ $greeting }}

{{ $line1 }}

@if($line2)
{{ $line2 }}
@endif

@if($line3)
{{ $line3 }}
@endif

@component('mail::button', ['url' => $actionUrl])
{{$actionText}}
@endcomponent

@endcomponent
