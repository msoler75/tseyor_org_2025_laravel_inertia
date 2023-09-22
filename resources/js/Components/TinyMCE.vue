<template>
    <div class="bg-base-100">
        <span v-if="editingMarkdown" class="btn mb-2" @click="editingMarkdown = false">
            <Icon icon="ph:arrow-left" />Volver al editor normal
        </span>

        <!--
    <div>MD: {{contenidoMD}}</div>
    <div>HTML: {{ contenidoHtml }}</div>
-->

        <div v-show="!editingMarkdown">

            <TinyEditor v-if="editando" :api-key="key" :init="{
                height: height,
                language_url: '/assets/js/tiny.es.js',
                language: 'es',
                menubar: false,
                statusbar: false,
                setup: editorSetup,
                relative_urls: false,
                block_formats: 'Paragraph=p; Header 1=h1; Header 2=h2; Header 3=h3',
                plugins: [
                    'advlist', 'autolink',
                    'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks',
                    'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'help', 'wordcount',
                    'emoticons'
                ],
                toolbar: toolbarButtons,
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
            }" :initial-value="contenidoHtml" @change="onChange" />
            <div v-else >
                <template v-if="contenidoHtml">
                    <span class="text-xs opacity-70">Vista previa:</span>
                    <Content :content="contenidoHtml" class="max-h-[30ch] overflow-y-scroll border p-3" :format="format"/>
                </template>
                <div v-else class="text-sm opacity-70">(no hay texto)</div>
                <div class="btn btn-primary btn-sm mt-2" @click="editando = true">Editar</div>
            </div>

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

        <!-- Modal Upload Image -->
        <ModalDropZone v-model="modalSubirArchivos" @uploaded="uploadedImage($event)" :mediaFolder="mediaFolder"
            placeholder="Arrastra la imagen aquí o haz clic" url="/api/files/upload/image" :options="{
                maxFiles: 1,
                acceptedFiles: 'image/*'
            }" />

        <!-- Editor Markdown MDEditor -->
        <MdEditor v-model="contenidoMD" v-show="editingMarkdown"
            :toolbarsExclude="['save', 'sub', 'sup', 'katex', 'mermaid', 'htmlPreview', 'catalog', 'github', 'revoke', 'next', 'image']"
            :footers="[]" :preview="false" />

    </div>
</template>

<script setup>
import TinyEditor from '@tinymce/tinymce-vue'

import { MdEditor } from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';

import { HtmlToMarkdown, MarkdownToHtml, detectFormat } from '@/composables/markdown.js'

import { onThemeChange, updateTheme } from '@/composables/themeadapter'

const key = ref(import.meta.env.VITE_TINY_API_KEY)

const emit = defineEmits(['update:modelValue', 'change'])

const props = defineProps({
    modelValue: { type: String },
    format: { type: String, default: 'detect' }, // 'md', 'html'
    height: { type: Number, default: 500 },
    mediaFolder: { type: String, default: '/media' },
    toolbar: { type: String, default: '' },
    fullEditor: { type: Boolean, default: false }
})

const editando = ref(false)

const toolbarButtons = computed(() => {
    if (props.toolbar)
        return props.toolbar
    if (props.fullEditor)
        return 'undo redo | bold italic underline strikethrough | styles | alignleft aligncenter alignright alignjustify | blockquote numlist bullist | fontsizes fonts | forecolor backcolor | insertimage mediamanager | codesample table emoticons | code markdown customDateButton | fullscreen'
    return 'undo redo | blocks | bold italic | forecolor backcolor | emoticons'
})


// UPLOAD IMAGE


const modalSubirArchivos = ref(false)

function uploadedImage(src) {
    // console.log('uploadedImage', src)
    insertImage(src)
    modalSubirArchivos.value = false
}

// COLOR MODE

onThemeChange().to(updateTheme)

// updates in body element
updateTheme()

// CONVERT MD <-> HTML


const format = computed(() => props.format != 'detect' ? props.format :
    ['md', 'ambiguous'].includes(detectFormat(props.modelValue).format) ? 'md' : 'html')

const contenidoMD = ref(format.value.toLowerCase() == 'md' ? props.modelValue : HtmlToMarkdown(props.modelValue))
const contenidoHtml = ref(format.value.toLowerCase() == 'html' ? props.modelValue : MarkdownToHtml(props.modelValue))

watch(contenidoHtml, (value) => {
    contenidoMD.value = HtmlToMarkdown(value)
})



// MEDIA MANAGER

const showMediaManager = ref(false)

function insertImage(src) {
    console.log('insertImage', src)
    tinymce.activeEditor.insertContent(`<img src=${src}>`)
}


// TINYMCE SETUP

function editorSetup(editor) {

    if (!props.fullEditor) return

    //add icons:
    editor.ui.registry.addIcon('markdown', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M7 15V9l2 2l2-2v6m3-2l2 2l2-2m-2 2V9"/></g></svg>')
    editor.ui.registry.addIcon('insertimage', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M18 20H4V6h9V4H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-9h-2v9zm-7.79-3.17l-1.96-2.36L5.5 18h11l-3.54-4.71zM20 4V1h-2v3h-3c.01.01 0 2 0 2h3v2.99c.01.01 2 0 2 0V6h3V4h-3z"/></svg>')
    editor.ui.registry.addIcon('mediamanager', '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M18 13v7H4V6h5.02c.05-.71.22-1.38.48-2H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-5l-2-2zm-1.5 5h-11l2.75-3.53l1.96 2.36l2.75-3.54zm2.8-9.11c.44-.7.7-1.51.7-2.39C20 4.01 17.99 2 15.5 2S11 4.01 11 6.5s2.01 4.5 4.49 4.5c.88 0 1.7-.26 2.39-.7L21 13.42L22.42 12L19.3 8.89zM15.5 9a2.5 2.5 0 0 1 0-5a2.5 2.5 0 0 1 0 5z"/></svg>')

    editor.ui.registry.addButton('insertimage', {
        icon: 'insertimage',
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

    if (props.format == 'md') {
        emit('change', contenidoMD.value)
        emit('update:modelValue', contenidoMD.value)
    }
    else {
        emit('change', contenidoHtml.value)
        emit('update:modelValue', contenidoHtml.value)
    }
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
