<template>
    <div :editingHtml="editingHtml">
        <input type="hidden" :name="fieldName" v-model="contenidoMD" />

        <Modal :show="showMediaManager" @close="showMediaManager = false" maxWidth="4xl">
            <div class="flex flex-col">
                <FileManager url="/imagenes/portada" class="max-h-[90vh] flex-grow" @image="onInsertImage"
                    content-class="max-h-[calc(100vh-240px)] overflow-y-auto" />
                <div class="p-3 flex justify-end">
                    <button @click.prevent="showMediaManager = false" class="btn btn-neutral">Cerrar</button>
                </div>
            </div>
        </Modal>

        <div v-show="!editingMarkdown">
            <QuillEditor ref="qeditor" theme="snow" :value="contenidoHtml" @input="updateHtml" contentType="html"
                :modules="modules" toolbar="#toolbar_1"
                @ready="onQuillReady">
                <template #toolbar>
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
                            <button class="ql-file-manager" @click.prevent="showMediaManager = true">
                                <Icon icon="ph:folder-notch-open-duotone" />
                            </button>
                        </span>
                        <span class="ql-formats">
                            <button type="button" class="ql-clean">
                                <Icon icon="ph:eraser-duotone" />
                            </button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-md" @click.prevent="editingMarkdown = !editingMarkdown">
                                <Icon icon="teenyicons:markdown-solid" />
                            </button>
                        </span>
                        <span class="ql-formats ql-no-hide">
                            <button class="ql-html" @click.prevent="onHtml">
                                <Icon icon="ph:brackets-angle-bold" />
                            </button>
                        </span>
                    </div>
                </template>
            </QuillEditor>
        </div>

        <button v-show="editingMarkdown" @click.prevent="editingMarkdown=false">Volver</button>

        <MdEditor :value="contenidoMD" @onChange="updateMD" v-show="editingMarkdown" noMermaid noKatex noUploadImg
        :toolbarsExclude="['save', 'sub', 'sup', 'katex', 'mermaid', 'htmlPreview', 'catalog', 'github', 'revoke', 'next']"
        />
    </div>
</template>


<script setup>
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import BlotFormatter from 'quill-blot-formatter'
import ImageUploader from 'quill-image-uploader'

import { MdEditor } from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';

import MarkdownIt from 'markdown-it'
import TurndownService from 'turndown'

const props = defineProps({
    fieldName: String,
    content: { type: String, default: 'Hola mundo 2' }
})

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
                    console.log('upload')
                    axios.post('/api/files/upload/image', formData)
                        .then(res => {
                            console.log('upload result', res)
                            resolve(res.data.data.filePath);
                        })
                        .catch(err => {
                            reject("Upload failed");
                            console.error("Error:", err)
                        })
                })
            }
        }
    }
])

function onQuillReady() {
    console.log('Quill Ready!')
}

const contenidoHtml = ref(props.content)
const contenidoMD = ref(HtmlToMarkdown(props.content))

// Intercepta los cambios en el valor contenidoMD
const updateMD = (newValue) => {
    console.log('updateMD', newValue)
    contenidoMD.value = newValue;
    contenidoHtml.value = MarkdownToHtml(newValue)
    console.log('HTML:', contenidoHtml.value)
};

// Intercepta los cambios en el valor contenidoMD
const updateHtml = () => {
    const newValue = qeditor.value.getHTML()
    console.log('updateHtml', newValue)
    // contenidoHtml.value = newValue;
    contenidoMD.value = HtmlToMarkdown(newValue)
    console.log("MD:", contenidoMD.value)
};

const qeditor = ref(null)

const showMediaManager = ref(false)

function onInsertImage(src) {
    // console.log('onInsertImage', img)
    const quill = qeditor.value.getQuill()
    console.log({ quill })
    // quill.insertText(0, `![](${src})`);
    var range = quill.getSelection();
    quill.insertEmbed(range.index, 'image', src);
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
}

const editingHtml = ref(false)
const editingMarkdown = ref(false)

function onHtml(evt) {
    const quill = qeditor.value.getQuill()
    var wasActiveTxtArea_1 = (quillEd_txtArea_1.getAttribute('quill__html').indexOf('-active-') > -1);
    console.log({ wasActiveTxtArea_1 })
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

onMounted(() => prepareHtmlButton())



function HtmlToMarkdown(html) {
    const turndownService = new TurndownService()
    return turndownService.turndown(html.replace(/<p class=["']([^>]*)["'][^>]*>/g, "$&{class:$1}"));
}

// cambia los caracteres codificados de < y > a su valor real
function DecodeHtml(html) {
    return html.replace(/&gt;/g, '>').replace(/&lt;/g, '<')
}

function MarkdownToHtml(raw_markdown) {
    // vamos a renderizar el markdown, y sustituimos las clases de p
    var md = new MarkdownIt({
        html: true,
        linkify: true
    });

    return md.render(raw_markdown).replace(/<p>{class:([^}]*)}/g, "<p class='$1'>").replace(
        /<p>\s+<\/p>\n?/g, '').replace(/\n/g, '')
}

</script>



<style>
/* @import url(//cdn.quilljs.com/1.3.6/quill.snow.css); */

.quilleditor_field_class {
    display: none;
}

.ql-editor {
    max-height: 80vh;
    background: red;
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
