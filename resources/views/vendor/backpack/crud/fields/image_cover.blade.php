{{-- image_cover_field field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));

    $listImages = $field['attributes']['list-images'] ??false;
    $canDelete = $field['attributes']['can-delete'] ??false;
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!} &nbsp;<small>{!! $field['attributes']['sublabel'] ?? '' !!}</small></label>
    @include('crud::fields.inc.translatable_icon')

    <ImageCoverField
    from="{{ $field['attributes']['from'] ?? 'texto' }}"
    folder="{{ $field['attributes']['folder'] ?? 'medios' }}"
    name="{{$field['name']}}"
    value="{{$field['value']}}"
    {{$listImages?'list-images':''}}
    {{$canDelete?'can-delete':''}}
    initial-images="{{$field['attributes']['initial-images'] ?? ''}}"
    />

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

