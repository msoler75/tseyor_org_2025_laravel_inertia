{{-- mymarkdown_field field --}}
@php
    $field['value'] = old_empty_or_null($field['name'], '') ?? ($field['value'] ?? ($field['default'] ?? ''));
@endphp

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')


@php
    // Suponiendo que $field['value'] contiene el HTML con entidades HTML como "&lt;" y "&gt;"
    $htmlCode = $field['value'];

    $decodedHtml = str_replace(['&lt;', '&gt;'], ['<', '>'], $htmlCode);

    // Convertir las entidades HTML a caracteres regulares
    //$decodedHtml = html_entity_decode($htmlCode);

    // Reemplazar las comillas dobles con su entidad HTML para que no rompan la plantilla
    // $decodedHtml = str_replace('"', '&quot;', $decodedHtml);

@endphp

<input id="quill-input" type="hidden" name="{{ $field['name'] }}" data-init-function="bpFieldInitDummyFieldElement"
    @include('crud::fields.inc.attributes') value="{{ $decodedHtml }}" />

<div id="editor">{{ $decodedHtml }}</div>

<textarea>{{ $field['value'] }}</textarea>



{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    {{-- How to load a CSS file? --}}
    @basset('mymarkdownFieldStyle.css')

    {{-- How to add some CSS? --}}
    @bassetBlock('backpack/crud/fields/mymarkdown_field-style.css')
        <style>
            @import url(//cdn.quilljs.com/1.3.6/quill.snow.css);

            .mymarkdown_field_class {
                display: none;
            }

            #editor {
                max-height: 80vh;
            }

            .ql-editor {
                max-height: 80vh;
            }

            .ql-editor p {
                margin: 1rem 0;
            }

            .ql-editor img {
                display: block;
                margin: 1rem auto;
            }
        </style>
    @endBassetBlock
@endpush

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')
    <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/to-markdown/3.0.4/to-markdown.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/markdown-it/8.3.1/markdown-it.min.js"></script>

    {{-- How to load a JS file? --}}
    @basset('mymarkdownFieldScript.js')

    {{-- How to add some JS to the field? --}}
    @bassetBlock('path/to/script.js')
        <script>
            // Mapeo de traducciones de botones de Quill Editor
            const translations = {
                bold: 'Negrita',
                italic: 'Cursiva',
                underline: 'Subrayado',
                strike: 'Tachado',
                image: 'Imagen',
                video: 'Video',
                link: 'Enlace',
                code: 'Código',
                align: 'Alinear',
                header: 'Título',
                'header 1': 'Título 1',
                'header 2': 'Título 2',
                'header 3': 'Título 3',
                script: 'Superíndice/Subíndice',
                'blockquote': 'Cita en bloque',
                'code-block': 'Bloque de código',
                'clean': 'Borrar formato',
                'formula': 'Fórmula',
                'list': 'Lista',
                'indent': 'Indentar',
                'indent -1': 'Indentar -1',
                'indent +1': 'Indentar +1',
                'size': 'Tamaño',
                'font color': 'Color del texto',
                'highlight color': 'Color de fondo',
                'background': 'Color de fondo',
                'code block': 'Bloque de código',
                'list ordered': 'Lista ordenada',
                'list bullet': 'Lista',
            }

            // Función para obtener el atajo de teclado humano-legible
            var bindings = null;

            function getShortcut(buttonName) {
                if (!bindings) return "";
                var shortcut = null;

                if (bindings.hasOwnProperty(buttonName)) {
                    var binding = bindings[buttonName];

                    if (typeof binding.key == 'string') {
                        var key = binding.key.toUpperCase()

                        shortcut = binding.shortKey ? 'CTRL+' + key : key;

                        if (binding.shiftKey) {
                            shortcut = 'SHIFT+' + shortcut;
                        }

                        if (binding.altKey) {
                            shortcut = 'ALT+' + shortcut;
                        }

                        if (binding.metaKey) {
                            shortcut = 'CMD+' + shortcut;
                        }
                    }
                }

                return shortcut ? "(" + shortcut + ")" : "";
            }

            function nombreBoton(buttonName) {
                // Traducir el nombre del comando si está en el mapeo de traducciones
                if (translations.hasOwnProperty(buttonName.toLowerCase())) {
                    buttonName = translations[buttonName.toLowerCase()];
                }
                return buttonName
            }

            function bpFieldInitDummyFieldElement(element) {
                // this function will be called on pageload, because it's
                // present as data-init-function in the HTML above; the
                // element parameter here will be the jQuery wrapped
                // element where init function was defined
                // console.log(element.val());


                // carga el contenido del  campo
                // reemplazamos los caracteres para incorporar fragmentos de HTML en el markdown
                var raw_markdown = document.querySelector('#editor').innerHTML.replace(/&gt;/g, '>').replace(/&lt;/g, '<')


                // prepara la barra de botones
                var toolbarOptions = [
                    [{
                        'header': 1
                    }, {
                        'header': 2
                    }], // custom button values

                    ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                    ['blockquote', 'code-block'],

                    [{
                        'align': []
                    }],

                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    /* [{
                         'script': 'sub'
                     }, {
                         'script': 'super'
                     }], // superscript/subscript
                     */
                    [{
                        'indent': '-1'
                    }, {
                        'indent': '+1'
                    }], // outdent/indent
                    /* [{
                        'direction': 'rtl'
                    }],*/ // text direction

                    [{
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }],

                    [{
                        'size': ['small', false, 'large', 'huge']
                    }], // custom dropdown


                    [{
                        'color': []
                    }, {
                        'background': []
                    }], // dropdown with defaults from theme
                    /*[{
                        'font': []
                    }],*/


                    ['clean'] // remove formatting button
                ];

                // inicializa el editor
                var quill = new Quill('#editor', {
                    modules: {
                        toolbar: toolbarOptions
                    },
                    theme: 'snow'
                });


                // cada vez que se modifique el texto, guardamos el contenido en el campo input de tipo hidden
                quill.on('text-change', function(delta, oldDelta, source) {
                    var editorInput = document.querySelector('#quill-input');
                    // hacemos un pequeño reemplazo para guardar las clases en p
                    editorInput.value = toMarkdown(quill.root.innerHTML.replace(/<p class=["']([^>]*)["'][^>]*>/g, "$&{class:$1}"));
                });




                // vamos a renderizar el markdown
                var md = window.markdownit({
                    html: true,
                    linkify: true
                });

                // renderizamos, y sustituimos las clases de p
                var contentHTML = md.render(raw_markdown).replace(/<p>{class:([^}]*)}/g, "<p class='$1'>").replace(/<p>\s+<\/p>\n?/g, '').replace(/\n/g,'')

                console.log({
                    raw_markdown
                })

                console.log({
                    contentHTML
                })

                // pega el contenido
                // quill.clipboard.dangerouslyPasteHTML(contentHTML + "\n");
                quill.root.innerHTML = contentHTML

                // Obtener los atajos de teclado actuales
                bindings = quill.getModule('keyboard').options.bindings;

                // tooltips
                // https://github.com/quilljs/quill/issues/2078#issuecomment-1031579345
                $('.ql-toolbar .ql-formats .ql-picker .ql-picker-label').each(function(i, e) {
                    let classes = $(e).parent().attr('class');

                    let button = classes.replace('ql-color-picker', '').replace('ql-color', 'font-color')
                        .replace('ql-background', 'highlight-color')
                        .replace('ql-icon-picker', '')
                        .replace('ql-active', '')
                        .replace('ql-picker', '').trim()
                        .replace('ql-', '').replace('-', ' ')
                    classes = button
                        .replace(/(^\w{1})|(\s+\w{1})/g, letter => letter.toUpperCase());

                    if ($(e).parent().attr("value"))
                        classes += " " + $(e).parent().attr("value")

                    new bootstrap.Tooltip(e, {
                        title: nombreBoton(classes) + " " + getShortcut(button),
                    });
                });

                $('.ql-toolbar button[class*="ql-"]').each(function(i, e) {
                    let classes = $(e).attr('class');

                    let button = classes.replace('ql-active', '').replace('ql-', '').replace('-', ' ')

                    classes = button.trim()
                        .replace(/(^\w{1})|(\s+\w{1})/g, letter =>
                            letter.toUpperCase());

                    if ($(e).attr("value"))
                        classes += " " + $(e).attr("value")

                    new bootstrap.Tooltip(e, {
                        title: nombreBoton(classes) + " " + getShortcut(button),
                    });
                });
            }
        </script>
    @endBassetBlock
@endpush
