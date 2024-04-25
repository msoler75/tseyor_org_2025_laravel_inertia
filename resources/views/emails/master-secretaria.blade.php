@component('mail::layout')
    {{-- Header --}}
    <x-slot:header>

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

    </x-slot:footer>
@endcomponent
