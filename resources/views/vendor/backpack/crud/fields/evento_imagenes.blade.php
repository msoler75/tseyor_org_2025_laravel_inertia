{{-- evento_imagenes field --}}
@php
    $currentImagen = '';
    $currentImagenesStr = '';

    if (isset($entry)) {
        $currentImagen = $entry->imagen ?? '';
        $currentImagenes = $entry->imagenes ?? [];
        if (is_array($currentImagenes)) {
            $currentImagenesStr = implode(',', $currentImagenes);
        }
    }
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!} &nbsp;<small>{!! $field['attributes']['sublabel'] ?? '' !!}</small></label>
    @include('crud::fields.inc.translatable_icon')

    <EventoImagenesField
        name="{{ $field['name'] }}"
        cover-name="{{ $field['attributes']['cover-name'] ?? 'imagen' }}"
        folder="{{ $field['attributes']['folder'] ?? 'medios' }}"
        value="{{ $currentImagen }}"
        initial-images="{{ $currentImagenesStr }}"
    />

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')
