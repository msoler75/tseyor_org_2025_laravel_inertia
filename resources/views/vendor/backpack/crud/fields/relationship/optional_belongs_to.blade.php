<!-- resources/views/vendor/backpack/crud/fields/relationship/optional_belongs_to.blade.php -->
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

@push('crud_fields_scripts')
<script>
    jQuery(document).ready(function($) {
        var $field = $('#{{ $field['id'] }}');

        $field.select2({
            theme: "bootstrap",
            ajax: {
                url: $field.data('ajax--url'),
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.data,
                        pagination: {
                            more: params.page < data.last_page
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            templateResult: function(item) {
                if (item.create) {
                    return $('<span class="text-primary">Crear contacto para este centro</span>');
                }
                return item.text;
            },
            escapeMarkup: function(markup) {
                return markup;
            },
            language: {
                noResults: function() {
                    return 'Sin resultados';
                },
                searching: function() {
                    return 'Buscando...';
                },
                inputTooShort: function() {
                    return 'Ingresa al menos un carácter';
                }
            },
            createTag: function(params) {
                return {
                    id: 0,
                    text: params.term,
                    create: true
                };
            },
            dropdownCssClass: 'width-100'
        });
    });
</script>
@endpush
