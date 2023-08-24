<!-- resources/views/vendor/backpack/crud/fields/json.blade.php -->

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

@php
    $value = ($field['value']);
    $value = preg_replace("/\\\\r/", "", $value);
    $value = preg_replace("/\\\\n/", "", $value);
    $value = preg_replace("/\\\\\"/", '"', $value);
    $value = preg_replace("/^\"|\"$/", '', $value);
    //$value = preg_replace("/\\n/", "\n", $value);
    //var_dump($value);
    //die;
@endphp

<JSONEditorField name="{{ $field['name'] }}" value="{{ $value }}"></JSONEditorField>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')


