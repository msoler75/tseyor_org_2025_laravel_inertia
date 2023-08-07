<template>
    <div>
        <span v-if="editingMarkdown" class="btn mb-2" @click="editingMarkdown = false">
            <Icon icon="ph:arrow-left" />Volver al editor normal
        </span>
        <div v-show="!editingMarkdown">
            <Editor :api-key="key" :init="{
                height: height,
                language_url: '/assets/js/tiny.es.js',
                language: 'es',
                menubar: false,
                setup: editorSetup,
                plugins: [
                    'a11ychecker', 'advlist', 'advcode', 'advtable', 'autolink', 'checklist', 'export',
                    'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks',
                    'powerpaste', 'fullscreen', 'formatpainter', 'insertdatetime', 'media', 'table', 'help', 'wordcount',
                    'emoticons'
                ],
                toolbar: toolbarButtons,
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
            }" :initial-value="modelValue" @change="onChange" />
        </div>


        <Modal :show="showMediaManager" @close="showMediaManager = false" maxWidth="4xl">
            <div class="flex flex-col">
                <FileManager :url="mediaFolder" class="max-h-[90vh] flex-grow" @image="insertImage"
                    content-class="max-h-[calc(100vh-240px)] overflow-y-auto" />
                <div class="p-3 flex justify-end">
                    <button @click.prevent="showMediaManager = false" class="btn btn-neutral">Cerrar</button>
                </div>
            </div>
        </Modal>

        <!-- Modal Upload -->
        <Modal :show="modalSubirArchivos" @close="modalSubirArchivos = true">

            <div class="p-5 flex flex-col gap-5 items-center">
                <Dropzone class="w-full" id="dropzone" :options="dropzoneOptions" :useCustomSlot=true
                    v-on:vdropzone-sending="sendingEvent" v-on:vdropzone-success="successEvent">
                    <div class="flex flex-col items-center">
                        <Icon icon="mdi:cloud-upload-outline" class="text-5xl" />
                        <span>Arrastra la imagen aquí o haz clic para elegirla</span>
                    </div>
                </Dropzone>


                <button @click.prevent="modalSubirArchivos = false" type="button" class="btn btn-neutral">
                    Cerrar
                </button>
            </div>

        </Modal>


        <MdEditor v-model="contenidoMD" v-show="editingMarkdown"
            :toolbarsExclude="['save', 'sub', 'sup', 'katex', 'mermaid', 'htmlPreview', 'catalog', 'github', 'revoke', 'next', 'image']"
            :footers="[]" :preview="false" />

        MD:: {{ contenidoMD }}

    </div>
</template>

<script setup>
import Editor from '@tinymce/tinymce-vue'

import { MdEditor } from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';

import MarkdownIt from 'markdown-it'
import TurndownService from 'turndown'
import { gfm } from 'turndown-plugin-gfm'

import Dropzone from 'vue2-dropzone-vue3'

const key = ref(import.meta.env.VITE_TINY_API_KEY)

const emit = defineEmits(['update:modelValue', 'change'])

const props = defineProps({
    modelValue: { type: String },
    height: { type: Number, default: 500 },
    mediaFolder: { type: String, default: '/media' },
    toolbar: { type: String, default: '' },
    fullEditor: { type: Boolean, default: false }
})

const toolbarButtons = computed(() => {
    if (props.toolbar)
        return props.toolbar
    if (props.fullEditor)
        return 'undo redo | bold italic underline strikethrough | styles | alignleft aligncenter alignright alignjustify | blockquote numlist bullist | fontsizes fonts | forecolor backcolor | image insertimage mediamanager | codesample table | markdown customDateButton | fullscreen'
    return 'undo redo | blocks | bold italic | forecolor backcolor emoticons'
})

// CONVERT MD <-> HTML

const contenidoMD = ref(props.modelValue)
const contenidoHtml = ref(MarkdownToHtml(props.modelValue))

watch(contenidoHtml, (value) => {
    contenidoMD.value = HtmlToMarkdown(contenidoHtml.value)
})


const turndownService = new TurndownService({ bulletListMarker: '-', headingStyle: 'atx' })
turndownService.use(gfm)

function HtmlToMarkdown(html) {
    // convertimos cualquier clase dentro de párrafo p en un marcaje especial
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


// SUBIR IMAGEN

const modalSubirArchivos = ref(false)
const page = usePage()

const dropzoneOptions = ref({
    url: '/api/files/upload/image',
    thumbnailWidth: 150,
    maxFilesize: 50,
    multiple: false,
    headers: {
        'X-CSRF-Token': page.props ? page.props.csrf_token : document.querySelector('meta[name="csrf-token"]').content,
    },
})

function sendingEvent(file, xhr, formData) {
    formData.append('destinationPath', props.mediaFolder);
}

var someUploaded = ref(false)
function successEvent(file, response) {
    if (response.data.filePath) {
        someUploaded.value = true
        insertImage(response.data.filePath)
        modalSubirArchivos.value = false
    }
}

watch(modalSubirArchivos, (value) => {
    if (value)
        someUploaded.value = false
    else if (someUploaded.value) {
        someUploaded.value = false
        // recargamos la vista
        // reloadPage()
    }
})


// MEDIA MANAGER

const showMediaManager = ref(false)

function insertImage(src) {
    tinymce.activeEditor.insertContent(`<img src=${src}>`)
}


// TINYMCE SETUP

function editorSetup(editor) {

    if(!props.fullEditor) return

    //add icons:
    editor.ui.registry.addIcon('markdown', '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M7 15V9l2 2l2-2v6m3-2l2 2l2-2m-2 2V9"/></g></svg>')
    editor.ui.registry.addIcon('mediamanager', '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><g fill="none"><path fill="currentColor" d="M3 17V5h7l2 2h9v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z" opacity=".16"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17V5h7l2 2h9v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 19l7-7l7 7"/><path stroke="currentColor" stroke-linejoin="round" stroke-width="3" d="M7.5 9.5h.01v.01H7.5z"/></g></svg>')

    editor.ui.registry.addButton('insertimage', {
        icon: 'image',
        tooltip: 'Elegir una imagen para insertar',
        onAction: (_) => modalSubirArchivos.value = true
    });

    editor.ui.registry.addButton('mediamanager', {
        icon: 'mediamanager',
        tooltip: 'Carpeta de medios',
        onAction: (_) => showMediaManager.value = true
    });

    editor.ui.registry.addButton('markdown', {
        icon: 'markdown',
        tooltip: 'Editar en markdown',
        onAction: (_) => editingMarkdown.value = true
    });
}


function onChange() {
    contenidoHtml.value = tinymce.activeEditor.getContent()
    contenidoMD.value = HtmlToMarkdown(contenidoHtml.value)

    emit('change', contenidoHtml.value)
    emit('update:modelValue', contenidoHtml.value)
}


// SWITCH EDITORS

const editingMarkdown = ref(false)

watch(editingMarkdown, (value) => {
    if (value)
        contenidoMD.value = HtmlToMarkdown(contenidoHtml.value)
    else {
        contenidoHtml.value = MarkdownToHtml(contenidoMD.value)
        tinymce.activeEditor.setContent(contenidoHtml.value)
    }
})



</script>

<style>
.vue-dropzone {
    @apply bg-base-100;
    border-radius: 5px;
    border: 2px dashed rgb(0, 135, 247);
    border-image: none;
    margin-left: auto;
    margin-right: auto;
}

.vue-dropzone>.dz-preview .dz-success-mark,
.vue-dropzone>.dz-preview .dz-error-mark {
    width: unset;
    left: calc(50% - 25px);
}
</style>
