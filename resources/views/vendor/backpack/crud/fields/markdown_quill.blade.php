{{-- quilleditor_field field --}}
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


<QuillEditorFullField name="{{ $field['name'] }}" folder="{{ $field['attributes']['folder'] ?? 'medios'}}" content="{{ $decodedHtml }}" />


{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')

    {{-- How to add some CSS? --}}
    @bassetBlock('backpack/crud/fields/quilleditor_field-style.css')
        <style>
        </style>
    @endBassetBlock
@endpush

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')



    {{-- How to add some JS to the field? --}}
    @bassetBlock('backpack/crud/fields/quilleditor.js')
        <script>
            function bpFieldInitDummyFieldElement(element) {
                // this function will be called on pageload, because it's
                // present as data-init-function in the HTML above; the
                // element parameter here will be the jQuery wrapped
                // element where init function was defined
                // console.log(element.val());


                // console.log('going to mount vue')
                /*axios.get('http://tseyor.org/any.js')

                const { createApp, ref } = Vue

                const app = window.init_vue(create_app);
                app.mount("#app")*/
                //window.init_vue();
                //setTimeout(()=>{
                // window.app.mount("#app");
                //}, 2000)

                // carga el contenido del  campo
                // reemplazamos los caracteres para incorporar fragmentos de HTML en el markdown
                // var raw_markdown = DecodeHtml(document.querySelector('#editor').innerHTML)



                // renderizamos
                // var contentHTML = MarkdownToHtml(raw_markdown)


                // quill.root.innerHTML = contentHTML

            }
        </script>
    @endBassetBlock
@endpush
