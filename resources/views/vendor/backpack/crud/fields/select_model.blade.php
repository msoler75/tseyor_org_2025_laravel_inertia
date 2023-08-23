{{-- select --}}
@php
    $current_value = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
    $entity_model = $crud->getRelationModel($field['entity'], -1);
    $field['allows_null'] = $field['allows_null'] ?? $entity_model::isColumnNullable($field['name']);

    //if it's part of a relationship here we have the full related model, we want the key.
if (is_object($current_value) && is_subclass_of(get_class($current_value), 'Illuminate\Database\Eloquent\Model')) {
    $current_value = $current_value->getKey();
}

@endphp


@include('crud::fields.inc.wrapper_start')

<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

<SelectField name="{{ $field['name'] }}" value="{{ $current_value }}" options="{{ isset($field['options']) ? json_encode($field['options']) : '' }}"
    multiple="{{ $field['multiple']  ?? false }}" model="{{ $field['model'] }}"
    label-option="{{ $field['labelOption'] ?? ''}}"
    placeholder="{{$field['placeholder'] ?? 'Buscar...'}}"
    ></SelectField>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif

@include('crud::fields.inc.wrapper_end')
