{{-- image_cover_field field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp



@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!} &nbsp;<small>(imagen de portada)</small></label>
    @include('crud::fields.inc.translatable_icon')

    <ImageCoverField from="{{ $field['attributes']['from'] ?? 'texto' }}" name="{{$field['name']}}" value="{{$field['value']}}"/>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')



{{-- CUSTOM JS --}}
@push('crud_fields_scripts')


    {{-- How to add some JS to the field? --}}
    @bassetBlock('path/to/script.js')
    <script>
        function bpFieldInitDummyFieldElement(element) {
            // this function will be called on pageload, because it's
            // present as data-init-function in the HTML above; the
            // element parameter here will be the jQuery wrapped
            // element where init function was defined
            console.log(element.val());
        }
    </script>
    @endBassetBlock
@endpush
