<x-mail::layout>
{{-- Header --}}
<x-slot:header>
    <x-mail::header :url="config('app.url')">
        <img src="{{config('app.url')}}/storage/imagenes/logos/sello_64.png" width=40 height=40 style="transform: translateY(2px)"> <span style="color:   #18419d; font-style:italic; font-family: 'Times New Roman', Times, serif; font-size: 54px; letter-spacing: -1pt">{{ config('app.name') }}</span> <span style="font-style:italic">.org</span>
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
    Te hemos enviado este correo electrónico para informarte de cambios importantes en tu cuenta y en los servicios de Tseyor.


© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
