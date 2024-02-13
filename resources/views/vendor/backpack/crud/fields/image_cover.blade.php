{{-- image_cover_field field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp



@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!} &nbsp;<small>(imagen de portada)</small></label>
    @include('crud::fields.inc.translatable_icon')

    <ImageCoverField from="{{ $field['attributes']['from'] ?? 'texto' }}" folder="{{ $field['attributes']['folder'] ?? 'medios' }}" name="{{$field['name']}}" value="{{$field['value']}}"/>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

