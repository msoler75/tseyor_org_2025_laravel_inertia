@component('mail::message')

{{$texto}}

@component('mail::button', ['url' => $url])
{{$action}}
@endcomponent

@endcomponent
