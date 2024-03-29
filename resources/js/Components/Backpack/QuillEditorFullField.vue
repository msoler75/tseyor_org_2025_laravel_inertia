<template>
    <div :editingHtml="editingHtml">
        <input type="hidden" :name="name" v-model="contenidoMD" />

        <Modal :show="showMediaManager" @close="showMediaManager = false" maxWidth="4xl">
            <div class="flex flex-col">
                <FileManager :ruta="mediaFolder" @image="onInsertImage" :modo-insertar="true"
                    class="min-h-[calc(100vh-160px)] max-h-[calc(100vh-160px)] h-[calc(100vh-160px)] overflow-y-auto" />
                <div class="p-3 flex justify-end border-t border-gray-500 border-opacity-25">
                    <button @click.prevent="showMediaManager = false" class="btn btn-neutral">Cerrar</button>
                </div>
            </div>
        </Modal>

        <div v-show="!editingMarkdown" ref="quillwrapper" class="bg-base-100 text-base-content"
            :class="inFullScreen ? 'fullscreen' : ''">
            <QuillEditor ref="qeditor" theme="snow" v-model:content="contenidoHtml" contentType="html"
                :modules="modules" :toolbar="`#toolbar_quill_${name}`" @ready="onQuillReady">
                <template #toolbar>
                    <div :id="`toolbar_quill_${name}`" quill__toolbar>
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
                            <!-- Note a missing, thus falsy value, is used to reset to default -->
                            <!-- <select class="ql-size">
                                <option value="small"></option>
                                <option selected></option>
                                <option value="large"></option>
                                <option value="huge"></option>
                            </select>
                            -->

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
                            <button type="button" class="ql-image transform scale-125">Image</button>
                            <button class="ql-file-manager" @click.prevent="showMediaManager = true">
                                <Icon icon="ph:folder-notch-open-duotone" class="transform scale-110" />
                            </button>
                        </span>
                        <span class="ql-formats">
                            <button type="button" class="ql-clean">
                                <Icon icon="ph:eraser-duotone" />
                            </button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-md" @click.prevent="switchToMarkdown">
                                <Icon icon="teenyicons:markdown-solid" class="transform scale-125" />
                            </button>
                        </span>
                        <span class="ql-formats ql-no-hide">
                            <button class="ql-html" @click.prevent="onHtml">
                                <Icon icon="mdi:application-brackets-outline" class="transform scale-125" />
                            </button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-fullscreen" @click.prevent="toggleFullscreen">
                                <Icon icon="fluent:arrow-expand-24-filled" v-if="!inFullScreen" />
                                <Icon icon="fluent:arrow-minimize-24-regular" v-else />
                            </button>
                        </span>
                    </div>
                </template>
            </QuillEditor>
        </div>

        <div v-show="editingMarkdown" @click="editingMarkdown = false"
            class="w-fit flex gap-3 my-2 items-center btn btn-neutral">
            <Icon icon="ph:arrow-left-duotone" />Volver al Editor normal
        </div>

        <MdEditor v-model="contenidoMD" v-show="editingMarkdown"
            :toolbarsExclude="['save', 'sub', 'sup', 'katex', 'mermaid', 'htmlPreview', 'catalog', 'github', 'revoke', 'next', 'image']"
            :footers="[]" :preview="false" />
    </div>
</template>


<script setup>
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import BlotFormatter from 'quill-blot-formatter'
import ImageUploader from 'quill-image-uploader'

import { createTooltip } from 'floating-vue'
import 'floating-vue/dist/style.css'

import { MdEditor } from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';

import { HtmlToMarkdown, MarkdownToHtml, detectFormat } from '@/composables/markdown.js'

import screenfull from 'screenfull'

import { onThemeChange, updateTheme } from '@/composables/themeAdapter'

// PROPS

const props = defineProps({
    name: String,
    content: { type: String, default: '' },
    mediaFolder: { type: String, default: 'medios' },
})

const emit = defineEmits(['update:modelValue', 'change'])


// MOUNTED

onMounted(() => {
    prepareHtmlButton()
    installToolTips()
    updateTheme()
})


// COLOR MODE

onThemeChange().to(updateTheme)


// QUILL EDITOR

const modules = ref([
    {
        name: 'blotFormatter',
        module: BlotFormatter,
        options: {/* options */ }
    },
    {
        name: 'imageUploader',
        module: ImageUploader,
        options: {
            upload: file => {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append("image", file);
                    formData.append("destinationPath", `/imagenes`)
                    // console.log('upload')
                    axios.post('/files/upload/image', formData)
                        .then(res => {
                            // console.log('upload result', res)
                            resolve(res.data.data.filePath);
                        })
                        .catch(err => {
                            reject("Upload failed");
                            // console.error("Error:", err)
                        })
                })
            }
        }
    }
])

function onQuillReady() {
    console.log('Quill Ready!')
    // Obtener los atajos de teclado actuales
    const quill = qeditor.value.getQuill()
    bindings = quill.getModule('keyboard').options.bindings;
}


// CONVERT MD <-> HTML

const format = ['md', 'ambiguous'].includes(detectFormat(props.content).format) ? 'md' : 'html'

const contenidoMD = ref(format == 'md' ? props.content : HtmlToMarkdown(props.content))
const contenidoHtml = ref(format == 'html' ? props.content : MarkdownToHtml(props.content))



watch(contenidoHtml, (value) => {
    contenidoMD.value = HtmlToMarkdown(replaceQuillEditorClasses(value))
    if (props.format == 'md') {
        emit('change', contenidoMD.value)
        emit('update:modelValue', contenidoMD.value)
    }
    else {
        emit('change', contenidoHtml.value)
        emit('update:modelValue', contenidoHtml.value)
    }
})


/*
// Intercepta los cambios en el valor contenidoMD
const updateMD = (newValue) => {
    console.log('updateMD', newValue)
    contenidoMD.value = newValue;
    contenidoHtml.value = MarkdownToHtml(newValue)
    console.log('HTML:', contenidoHtml.value)
    qeditor.value.setHTML(contenidoHtml.value)
};

// Intercepta los cambios en el valor contenidoMD
const updateHtml = (x) => {
    const newValue = qeditor.value.getHTML()
    console.log('updateHtml', newValue)
    // contenidoHtml.value = newValue;
    contenidoMD.value = HtmlToMarkdown(newValue)
    console.log("MD:", contenidoMD.value)
};
*/

const qeditor = ref(null)

const showMediaManager = ref(false)

function onInsertImage(src) {
    console.log('onInsertImage', src)
    const quill = qeditor.value.getQuill()
    // console.log({ quill })
    // quill.insertText(0, `![](${src})`);
    var range = quill.getSelection();
    quill.insertEmbed(range.index, 'image', src.replace(/\s/g, '%20'));

    showMediaManager.value = false
}


var quillEd_txtArea_1 = null;

function prepareHtmlButton() {
    // Custom Html Button
    // https://codepen.io/viT-1/pen/GQVaoB
    const quill = qeditor.value.getQuill()
    quillEd_txtArea_1 = document.createElement('textarea');
    var attrQuillTxtArea = document.createAttribute('quill__html');
    quillEd_txtArea_1.setAttributeNode(attrQuillTxtArea);

    var quillCustomDiv = quill.addContainer('ql-custom');
    quillCustomDiv.appendChild(quillEd_txtArea_1);

    // Update the Quill editor text with the textarea value
    quillEd_txtArea_1.addEventListener('input', () => { quill.root.innerHTML = quillEd_txtArea_1.value.replace(/>\n/g, '>') })
}



const editingHtml = ref(false)
const editingMarkdown = ref(false)

watch(editingMarkdown, (value) => {
    if (value)
        contenidoMD.value = HtmlToMarkdown(contenidoHtml.value)
    else
        contenidoHtml.value = MarkdownToHtml(contenidoMD.value)
})


/*watch(contenidoHtml, (value) => {
    contenidoMD.value = HtmlToMarkdown(contenidoHtml.value)
})*/



function onHtml(evt) {
    const quill = qeditor.value.getQuill()
    var wasActiveTxtArea_1 = (quillEd_txtArea_1.getAttribute('quill__html').indexOf('-active-') > -1);
    // console.log({ wasActiveTxtArea_1 })
    editingHtml.value = !wasActiveTxtArea_1

    if (wasActiveTxtArea_1) {
        //html editor to quill
        quill.root.innerHtml = quillEd_txtArea_1.value.replace(/>\n/g, '>')
        evt.target.classList.remove('ql-active');
    } else {
        //quill to html editor
        // quill.root.innerHtml.replace(
        quillEd_txtArea_1.value = qeditor.value.getHTML().replace(
            /<(ol|ul|table|tr|thead|tbody|blockquote)>|<\/(p|h\d|br|table|tr|thead|tbody|ul|ol|li|div|img|blockquote)>/ig,
            '$&\n')
        evt.target.classList.add('ql-active');
    }

    quillEd_txtArea_1.setAttribute('quill__html', wasActiveTxtArea_1 ? '' : '-active-');
}






// FULLSCREEN

const inFullScreen = ref(false)
const quillwrapper = ref(null)
function toggleFullscreen() {
    const element = quillwrapper.value
    if (screenfull.isEnabled) {
        screenfull.toggle(element);
        inFullScreen.value = !inFullScreen.value
    }
}


if (screenfull.isEnabled) {
    screenfull.on('change', () => {
        inFullScreen.value = screenfull.isFullscreen
    });
}

function switchToMarkdown() {
    editingMarkdown.value = true
    if (inFullScreen.value) // quita el fullscreen
        toggleFullscreen()
}


// TOOLTIPS

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
    'md': 'Editor de Markdown',
    'html': 'Editor de Html',
    'fullscreen': 'Pantalla completa'
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



function installToolTips() {

    // tooltips
    // https://github.com/quilljs/quill/issues/2078#issuecomment-1031579345
    var elems = document.querySelectorAll('#toolbar_1 .ql-toolbar .ql-formats .ql-picker .ql-picker-label')
    for (var e of elems) {
        let classes = e.parentNode.className
        // console.log('classes', classes)

        if (classes.match('tooltip')) continue

        let button = classes
            .replace('ql-color-picker', '')
            .replace('ql-color', 'font-color')
            .replace('ql-background', 'highlight-color')
            .replace('transform', '')
            .replace(/scale-\d+/, '')
            .replace('ql-icon-picker', '')
            .replace('ql-active', '')
            .replace('ql-picker', '').trim()
            .replace('ql-', '').replace('-', ' ')
        classes = button
            .replace(/(^\w{1})|(\s+\w{1})/g, letter => letter.toUpperCase());

        var value = e.parentNode.getAttribute("value")
        if (value)
            classes += " " + value

        /*new bootstrap.Tooltip(e, {
            title: nombreBoton(classes) + " " + getShortcut(button),
        });*/
        /*e.directiveName = {
            value: 'valor de la directiva', // Puedes pasar cualquier valor aquí
            arg: null, // El valor de arg depende de la directiva que estés usando
            modifiers: {}, // Los modificadores también dependen de la directiva
          };
        setAttribute("v-tooltip*/
        const tooltip = createTooltip(e, {
            triggers: ['hover'],
            content: nombreBoton(classes) + " " + getShortcut(button)
        })
    }



    elems = document.querySelectorAll('.ql-toolbar [class*="ql-"]')
    // console.log('elems', elems)
    for (var e of elems) {
        //console.log(e.tagName)
        if (!['BUTTON', 'SPAN'].includes(e.tagName)) continue

        let classes = e.getAttribute("class")
        if (classes.match(/ql-formats|ql-picker-label/)) continue

        if (classes.match('tooltip')) continue

        // console.log('adding tooltip for ', classes)
        let button = classes.replace('ql-active', '')
            .replace('transform', '')
            .replace(/scale-\d+/, '')
            .replace(/ql-picker|ql-icon-picker|ql-color-picker/g, '')
            .replace('ql-', '').replace('-', ' ')

        classes = button.trim()
            .replace(/(^\w{1})|(\s+\w{1})/g, letter =>
                letter.toUpperCase());

        var value = e.getAttribute("value")
        if (value)
            classes += " " + value


        const tooltip = createTooltip(e, {
            triggers: ['hover'],
            content: nombreBoton(classes) + " " + getShortcut(button),
        })
    }

    // console.log('tooltips installed')
}

</script>


<style>
/* @import url(//cdn.quilljs.com/1.3.6/quill.snow.css); */

.quilleditor_field_class {
    display: none;
}


.ql-editor {
    max-height: 80vh;
}

.fullscreen .ql-editor {
    max-height: calc(100vh - 41px)
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

*[editingHtml='true'] .ql-formats {
    display: none
}

*[editingHtml='true'] .ql-no-hide {
    display: block
}

.ql-formats *[quill__toolbar]:not(.ql-snow) {
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
