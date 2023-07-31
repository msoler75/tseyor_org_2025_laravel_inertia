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

<input id="quill-input" type="hidden" name="{{ $field['name'] }}" data-init-function="bpFieldInitDummyFieldElement"
    @include('crud::fields.inc.attributes') value="{{ $decodedHtml }}" />

<div id="toolbar_1" quill__toolbar>
    <span class="ql-formats">
        <button type="button" class="ql-header" value="1">Heading 1</button>
        <button type="button" class="ql-header" value="2">Heading 2</button>
        <button type="button" class="ql-bold">Bold</button>
        <button type="button" class="ql-italic">Italic</button>
        <button type="button" class="ql-underline">Underline</button>
        <button type="button" class="ql-strike">Strike</button>
    </span>

    <span class="ql-formats">
        <select class="ql-align">
            <option selected="selected"></option>
            <option value="center"></option>
            <option value="right"></option>
            <option value="justify"></option>
        </select>
    </span>

    <span class="ql-formats">
        <button type="button" class="ql-blockquote">Block Quote</button>
        <!-- <button type="button" class="ql-code-block">Code Block</button> -->
        <button type="button" class="ql-list" value="ordered">Ordered List</button>
        <button type="button" class="ql-list" value="bullet">Bullet List</button>
        <!--
        <button type="button" class="ql-indent" value="-1">Indent -1</button>
        <button type="button" class="ql-indent" value="+1">Indent +1</button>
        -->
    </span>

    <span class="ql-formats">
        <!-- Add font size dropdown -->
        <select class="ql-size">
            <option value="small"></option>
            <!-- Note a missing, thus falsy value, is used to reset to default -->
            <option selected></option>
            <option value="large"></option>
            <option value="huge"></option>
        </select>

        <select class="ql-header">
            <option value="1"></option>
            <option value="2"></option>
            <option value="3"></option>
            <option value="4"></option>
            <option selected="selected"></option>
        </select>
    </span>

    <span class="ql-formats">
        <select class="ql-color">
            <option value="rgb(230, 0, 0)">maroon</option>
            <option value="rgb(255, 153, 0)">orange</option>
            <option value="rgb(255, 255, 0)">yellow</option>
            <option value="rgb(0, 138, 0)">green</option>
            <option value="rgb(0, 102, 204)">blue</option>
            <option value="rgb(153, 51, 255)">purple</option>
            <option value="rgb(255, 255, 255)">white</option>
            <option value="rgb(250, 204, 204)">light red</option>
            <option value="rgb(255, 235, 204)">light orange</option>
            <option value="rgb(255, 255, 204)">light yellow</option>
            <option value="rgb(204, 232, 204)">light green</option>
            <option value="rgb(204, 224, 245)">light blue</option>
            <option value="rgb(235, 214, 255)">light purple</option>
            <option value="rgb(187, 187, 187)">gray</option>
            <option value="rgb(240, 102, 102)">light maroon</option>
            <option value="rgb(255, 194, 102)">light orange 2</option>
            <option value="rgb(255, 255, 102)">light yellow 2</option>
            <option value="rgb(102, 185, 102)">light green 2</option>
            <option value="rgb(102, 163, 224)">light blue 2</option>
            <option value="rgb(194, 133, 255)">light purple 2</option>
            <option value="rgb(136, 136, 136)">gray 2</option>
            <option value="rgb(161, 0, 0)">dark red</option>
            <option value="rgb(178, 107, 0)">dark orange</option>
            <option value="rgb(178, 178, 0)">dark yellow</option>
            <option value="rgb(0, 97, 0)">dark green</option>
            <option value="rgb(0, 71, 178)">dark blue</option>
            <option value="rgb(107, 36, 178)">dark purple</option>
            <option value="rgb(68, 68, 68)">dark gray</option>
            <option value="rgb(92, 0, 0)">darker red</option>
            <option value="rgb(102, 61, 0)">darker orange</option>
            <option value="rgb(102, 102, 0)">darker yellow</option>
            <option value="rgb(0, 55, 0)">darker green</option>
            <option value="rgb(0, 41, 102)">darker blue</option>
            <option value="rgb(61, 20, 102)">darker purple</option>
            <option value="rgb(0, 0, 0)">black</option>
          </select>

        <select class="ql-background">
            <option value="rgb(255, 255, 255)">white</option>
            <option value="rgb(0, 0, 0)">black</option>
            <option value="rgb(128, 0, 0)">maroon</option>
            <option value="rgb(0, 128, 0)">green</option>
            <option value="rgb(128, 128, 0)">olive</option>
            <option value="rgb(128, 0, 128)">purple</option>
            <option value="rgb(0, 128, 128)">teal</option>
            <option value="rgb(192, 192, 192)">silver</option>
            <option value="rgb(255, 0, 0)">red</option>
            <option value="rgb(0, 255, 0)">lime</option>
            <option value="rgb(255, 255, 0)">yellow</option>
            <option value="rgb(0, 0, 255)">blue</option>
            <option value="rgb(255, 0, 255)">fuchsia</option>
            <option value="rgb(0, 255, 255)">aqua</option>
        </select>
    </span>
    <span class="ql-formats">
        <button type="button" class="ql-image">Image</button>
        <button type="button" class="ql-clean">Clean</button>
    </span>
    <span class="ql-formats">
        <button class="ql-html">H</button>
    </span>
</div>

<div id="editor">{{ $decodedHtml }}</div>


{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

{{-- CUSTOM CSS --}}
@push('crud_fields_styles')
    {{-- How to load a CSS file? --}}
    @basset('quilleditorFieldStyle.css')

    {{-- How to add some CSS? --}}
    @bassetBlock('backpack/crud/fields/quilleditor_field-style.css')
        <style>
            /* @import url(//cdn.quilljs.com/1.3.6/quill.snow.css); */
            @import url(https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.0-dev.3/quill.snow.min.css);
            @import url(https://unpkg.com/quill-table-ui@1.0.5/dist/index.css);

            .quilleditor_field_class {
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

            /* Estilos generales de la tabla */
            .ql-editor table {
                border-collapse: collapse;
                width: auto;
                /* Combinar bordes de celdas adyacentes */
            }

            /* Estilos para las celdas <td> */
            .ql-editor td {
                padding: 8px;
                /* Espacio interno (padding) de 8px en todas las celdas */
                border: 1px solid #ccc;
                /* Borde de 1px y color gris claro */
                text-align: center;
                /* Alineación centrada del contenido */
            }

            /* Estilos para el encabezado <th> */
            .ql-editor th {
                padding: 8px;
                /* Espacio interno (padding) de 8px en los encabezados */
                border: 1px solid #ccc;
                /* Borde de 1px y color gris claro */
                background-color: #f2f2f2;
                /* Color de fondo gris claro para los encabezados */
                text-align: center;
                /* Alineación centrada del contenido */
            }

            *[quill__toolbar]:not(.ql-snow) {
                display: none
            }

            *[quill__html] {
                display: none;

                width: 100%;
                margin: 0;
                background: rgb(29, 29, 29);
                box-sizing: border-box;
                color: rgb(204, 204, 204);
                outline: none;
                padding: 12px 15px;
                line-height: 24px;
                font-family: Consolas, Menlo, Monaco, "Courier New", monospace;
                position: absolute;
                top: 0;
                bottom: 0;
                border: none;
            }

            *[quill__html *='-active-'] {
                display: initial;
            }
        </style>
    @endBassetBlock
@endpush

{{-- CUSTOM JS --}}
@push('crud_fields_scripts')
    <!-- Quill Editor -->
    <!-- curated things about quill -->
    <!-- https://github.com/quilljs/awesome-quill -->
    <!-- <script src="//cdn.quilljs.com/1.3.7/quill.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quill/2.0.0-dev.3/quill.min.js"></script>
    <!-- table for Quill -->
    <!-- https://codepen.io/volser/pen/QWWpOpr  -->
    <script src="https://unpkg.com/quill-table-ui@1.0.5/dist/umd/index.js"></script>
    <!-- https://github.com/T-vK/DynamicQuillTools -->
    <!-- para añadir dropdown en Quill -->
    <script src="https://cdn.jsdelivr.net/gh/T-vK/DynamicQuillTools@master/DynamicQuillTools.js"></script>
    <!-- Quill image resize module -->
    <!-- <script src="https://cdn.rawgit.com/kensnyder/quill-image-resize-module/3411c9a7/image-resize.min.js"></script> -->

    <!-- Quill image Uploader -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/quill-plugin-image-upload@0.0.6/src/index.min.js"></script> -->

    <!-- markdown converters -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/to-markdown/3.0.4/to-markdown.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/markdown-it/8.3.1/markdown-it.min.js"></script>


    {{-- How to load a JS file? --}}
    @basset('quilleditorFieldScript.js')

    {{-- How to add some JS to the field? --}}
    @bassetBlock('path/to/script.js')
        <script>
            // configuración original de la barra de botones
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
                'color': 'Color del texto',
                'font color': 'Color del texto',
                'highlight color': 'Color de fondo',
                'background': 'Color de fondo',
                'code block': 'Bloque de código',
                'list ordered': 'Lista ordenada',
                'list bullet': 'Lista de puntos',
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

            function HtmlToMarkdown(html) {
                return toMarkdown(html.replace(/<p class=["']([^>]*)["'][^>]*>/g, "$&{class:$1}"));
            }

            // cambia los caracteres codificados de < y > a su valor real
            function DecodeHtml(html) {
                return html.replace(/&gt;/g, '>').replace(/&lt;/g, '<')
            }

            function MarkdownToHtml(raw_markdown) {
                // vamos a renderizar el markdown, y sustituimos las clases de p
                var md = window.markdownit({
                    html: true,
                    linkify: true
                });

                return md.render(raw_markdown).replace(/<p>{class:([^}]*)}/g, "<p class='$1'>").replace(
                    /<p>\s+<\/p>\n?/g, '').replace(/\n/g, '')
            }

            function bpFieldInitDummyFieldElement(element) {
                // this function will be called on pageload, because it's
                // present as data-init-function in the HTML above; the
                // element parameter here will be the jQuery wrapped
                // element where init function was defined
                // console.log(element.val());


                // carga el contenido del  campo
                // reemplazamos los caracteres para incorporar fragmentos de HTML en el markdown
                var raw_markdown = DecodeHtml(document.querySelector('#editor').innerHTML)


                // inicializa el editor
                Quill.register({
                    'modules/tableUI': quillTableUI.default
                }, true)

                // Quill.register('modules/imageUpload', ImageUpload);

                var quill = new Quill('#editor', {
                    modules: {
                        // toolbar: toolbarOptions,
                        toolbar: '#toolbar_1',
                        table: true,
                        tableUI: true,
                        /*imageResize: {
                            displaySize: true
                        },*/

                        // ...
                        /*
                         imageUpload: {
                            upload: file => {
                                // return a Promise that resolves in a link to the uploaded image
                                return new Promise((resolve, reject) => {
                                    // ajax("/").then(data => resolve(data.imageUrl));
                                    console.log('ok!!')
                                    resolve(file)
                                });
                            }
                        },
                        */
                    },
                    theme: 'snow'
                });

                // Handlers can also be added post initialization
                var toolbar = quill.getModule('toolbar');
                // toolbar.addHandler('image', showImageUI);
                /*toolbar.addHandler('image', () => {
                    alert("yes!")
                });*/



                // const table = snow.getModule('table');

                // renderizamos
                var contentHTML = MarkdownToHtml(raw_markdown)

                /* console.log({
                    raw_markdown
                })

                console.log({
                    contentHTML
                })
                */

                // pega el contenido
                // quill.clipboard.dangerouslyPasteHTML(contentHTML + "\n");
                quill.root.innerHTML = contentHTML

                // cada vez que se modifique el texto, guardamos el contenido en el campo input de tipo hidden
                quill.on('text-change', function(delta, oldDelta, source) {
                    var editorInput = document.querySelector('#quill-input');
                    // hacemos un pequeño reemplazo para guardar las clases en p
                    editorInput.value = HtmlToMarkdown(quill.root.innerHTML);
                });

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

                // Custom HTML Button
                // https://codepen.io/viT-1/pen/GQVaoB
                var quillEd_txtArea_1 = document.createElement('textarea');
                var attrQuillTxtArea = document.createAttribute('quill__html');
                quillEd_txtArea_1.setAttributeNode(attrQuillTxtArea);

                var quillCustomDiv = quill.addContainer('ql-custom');
                quillCustomDiv.appendChild(quillEd_txtArea_1);

                var quillsHtmlBtns = document.querySelectorAll('.ql-html');
                for (var i = 0; i < quillsHtmlBtns.length; i++) {
                    quillsHtmlBtns[i].addEventListener('click', function(evt) {
                        var wasActiveTxtArea_1 =
                            (quillEd_txtArea_1.getAttribute('quill__html').indexOf('-active-') > -1);

                        if (wasActiveTxtArea_1) {
                            //html editor to quill
                            quill.root.innerHTML = quillEd_txtArea_1.value.replace(/>\n/g, '>')
                            evt.target.classList.remove('ql-active');
                        } else {
                            //quill to html editor
                            quillEd_txtArea_1.value = quill.root.innerHTML.replace(
                                /<(ol|ul|table|tr|thead|tbody|blockquote)>|<\/(p|h\d|br|table|tr|thead|tbody|ul|ol|li|div|img|blockquote)>/ig,
                                '$&\n')
                            evt.target.classList.add('ql-active');
                        }

                        quillEd_txtArea_1.setAttribute('quill__html', wasActiveTxtArea_1 ? '' : '-active-');
                    });
                }

                /*

                // Ejemplo: añadir botón con dropdown

                const dropDownItems = {
                    'Mike Smith': 'mike.smith@gmail.com',
                    'Jonathan Dyke': 'jonathan.dyke@yahoo.com',
                    'Max Anderson': 'max.anderson@gmail.com'
                }

                const myDropDown = new QuillToolbarDropDown({
                    label: "Email Addresses",
                    rememberSelection: false
                })

                myDropDown.setItems(dropDownItems)

                myDropDown.onSelect = function(label, value, quill) {
                    // Do whatever you want with the new dropdown selection here

                    // For example, insert the value of the dropdown selection:
                    const {
                        index,
                        length
                    } = quill.selection.savedRange
                    quill.deleteText(index, length)
                    quill.insertText(index, value)
                    quill.setSelection(index + value.length)
                }

                myDropDown.attach(quill)
                */

            }
        </script>
    @endBassetBlock
@endpush
