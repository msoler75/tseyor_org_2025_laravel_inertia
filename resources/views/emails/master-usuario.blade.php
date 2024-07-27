@component('mail::layout')
    {{-- Header --}}
    <x-slot:header>
        <x-mail::header :url="config('app.url')">
            <img src="{{ config('app.url') }}/almacen/medios/logos/sello_tseyor_64.png" width=40 height=40
                style="transform: translateY(2px)"> <span
                style="color:#18419d; font-style:italic; font-family: 'Times New Roman', Times, serif; font-size: 54px; letter-spacing: -1pt">{{ config('app.name') }}</span>
            <span style="font-style:italic">.org</span>
        </x-mail::header>
    </x-slot:header>

    <h1 style="font-size: 1.7em; text-align: center; margin-top: 20px">@yield('titulo')</h1><h2 style="text-align: center">@yield('subtitulo')</h2><hr style="margin: 30px 0; opacity: 0" />

    @yield('contenido')

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
            @yield('footerText')

© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        </x-mail::footer>
    </x-slot:footer>
@endcomponent
