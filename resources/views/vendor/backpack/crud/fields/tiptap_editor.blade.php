{{-- markdown_full_field field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')




@php
    // Suponiendo que $field['value'] contiene el HTML con entidades HTML como "&lt;" y "&gt;"
    $htmlCode = $field['value'];

    // decodificamos las etiquetas HTML
    $decodedHtml = str_replace(['&lt;', '&gt;'], ['<', '>'], $htmlCode);
@endphp

<TipTapEditorFullField name="{{$field['name']}}" folder="{{ $field['attributes']['folder'] ?? 'medios'}}" content="{{$decodedHtml}}"/>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

