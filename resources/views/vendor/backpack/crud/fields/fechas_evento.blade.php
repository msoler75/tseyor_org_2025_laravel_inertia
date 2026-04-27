@php
    // pasar el valor crudo al componente; el componente se encargará del parseo
    $raw = old($field['name']) ?? ($field['value'] ?? ($entry->{$field['name']} ?? null));
@endphp
<label>{!! $field['label'] !!}</label>
<div class="form-group col-sm-12">
    <fechas-evento-field field-name="{{ $field['name'] }}" :initial-value='@json($raw)' label="{{ $field['label'] ?? 'Fechas' }}"></fechas-evento-field>
</div>

