{{-- visibilidad_field field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

<div class="w-full flex gap-3">
    <label class="btn btn-neutral">
        <input type="radio" name="{{ $field['name'] }}" value="B" {{ $field['value'] === 'B' ? 'checked' : '' }}>
        Borrador
    </label>
    <label class="btn btn-success">
        <input type="radio" name="{{ $field['name'] }}" value="P" {{ $field['value'] === 'P' ? 'checked' : '' }}>
        Publicado
    </label>

</div>


{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')
