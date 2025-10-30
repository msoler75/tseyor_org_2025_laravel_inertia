<template>
    <div class="tiptap-editor-wrapper">
        <div v-show="!editingMarkdown">
            <template v-if="editor">
                <TipTapFullMenuBar v-if="full" :editor="editor" :media-folder="mediaFolder"
                    @toggle-markdown="toggleMarkdown" />
                <TipTapSimpleMenuBar v-else :editor="editor" :media-folder="mediaFolder" />
            </template>
            <EditorContent :editor="editor" class="tiptap-editor border border-gray-700 p-1" />
        </div>
        <div v-if="editingMarkdown && !dynamicComponent"
            class="flex gap-4 text-xl justify-center items-start w-full h-[532px]">
            <Spinner /> Cargando...
        </div>
        <span v-else-if="editingMarkdown && dynamicComponent" @click="toggleMarkdown"
            class="btn btn-neutral btn-xs mb-2 text-uppercase">
            <Icon icon="mdi-close" /> Cerrar editor Markdown
        </span>
        <component :is="dynamicComponent" v-if="full && editingMarkdown" v-model="contenidoMD"
            :toolbarsExclude="['save', 'sub', 'sup', 'katex', 'mermaid', 'htmlPreview', 'catalog', 'github', 'revoke', 'next', 'image']"
            :footers="[]" :preview="false" :noMermaid="true" :noKatex="true" />
    </div>
</template>

<script setup>
import {shallowRef} from 'vue'
import { Extension } from '@tiptap/core';
import { Plugin, PluginKey } from 'prosemirror-state';
//import {defineAsyncComponent } from 'vue'

// Definimos un componente asíncrono usando defineAsyncComponent
//const MdEditor = defineAsyncComponent(() => import('md-editor-v3'))
// import { MdEditor } from 'md-editor-v3';

import Highlight from '@tiptap/extension-highlight'
import StarterKit from '@tiptap/starter-kit'
import TextAlign from '@tiptap/extension-text-align'
import TextStyle from '@tiptap/extension-text-style'
import Underline from '@tiptap/extension-underline'
import ImageExtension from '@tiptap/extension-image'
import { Color } from '@tiptap/extension-color'
import Superscript from '@tiptap/extension-superscript'
import Table from "@tiptap/extension-table";
import TableCell from "@tiptap/extension-table-cell";
import TableHeader from "@tiptap/extension-table-header";
import TableRow from "@tiptap/extension-table-row";
import Link from '@tiptap/extension-link'
import TipTapFullMenuBar from "./TipTapFullMenuBar.vue";
import { useEditor, EditorContent } from "@tiptap/vue-3";

//import { MdEditor } from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';

import { HtmlToMarkdown, MarkdownToHtml, detectFormat } from '@/composables/markdown.js'

const props = defineProps({
    //name: String,
    //content: {
    //  type: String, default: ''
    //},
    modelValue: { type: String },
    format: { type: String, default: 'md' },
    mediaFolder: { type: String, default: 'medios' },
    full: { type: Boolean, default: false }
})


const emit = defineEmits(['update:modelValue', 'change'])

// CONVERT MD <-> HTML

const format = computed(() => props.format != 'detect' ? props.format : ['md', 'ambiguous'].includes(detectFormat(props.modelValue).format) ? 'md' : 'html')

const contenidoMD = ref(format.value.toLowerCase() == 'md' ? props.modelValue : HtmlToMarkdown(props.modelValue))
const contenidoHtml = ref(format.value.toLowerCase() == 'html' ? props.modelValue : MarkdownToHtml(props.modelValue))

console.log('props.format', props.format)
console.log('format', format.value)

watch(contenidoHtml, (value) => {
    if(editingMarkdown.value) return // venimos de watch contenidoMD
    console.log('watch contenidoHtml', value)
    contenidoMD.value = HtmlToMarkdown(value)
    console.log('format', format.value)
    emitChange()
})

function emitChange() {
    if (format.value == 'md') {
        console.log('emit change', contenidoMD.value)
        emit('change', contenidoMD.value)
        emit('update:modelValue', contenidoMD.value)
    }
    else {
        console.log('emit change', contenidoHtml.value)
        emit('change', contenidoHtml.value)
        emit('update:modelValue', contenidoHtml.value)
    }
}

// MARKDOWN EDIT

// const editingHtml = ref(false)
const editingMarkdown = ref(false)


watch(editingMarkdown, (value) => {
    if (value && dynamicComponent.value == null) {
        loadMdEditor()
    }
    if (value)
        contenidoMD.value = HtmlToMarkdown(contenidoHtml.value)
    else {
        contenidoHtml.value = MarkdownToHtml(contenidoMD.value)
        editor.value.commands.setContent(contenidoHtml.value)
    }
})

watch(contenidoMD, (value) => {
    if(!editingMarkdown.value) return
    contenidoHtml.value = MarkdownToHtml(value)
    emitChange()
})


// MDEDITOR
const dynamicComponent = shallowRef (null)

const loadMdEditor = () => {
    import('md-editor-v3').then(module => {
        dynamicComponent.value = module.MdEditor;
    });
};


function toggleMarkdown() {
    editingMarkdown.value = !editingMarkdown.value
}


// TIPTAP


const mapDataItems = (data, fileMatchRegex) => {
    return Array.from(data)
        .map(item => (item.type.match(fileMatchRegex) ? item.getAsFile() : null))
        .filter(item => item !== null);
};

const mapTextItems = async (data) => {
    const fullSet = Array.from(data);
    const plainText = fullSet.find(item => item.type === 'text/plain');
    const richText = fullSet.find(item => item.type === 'text/html');
    return new Promise(resolve => {
        (richText || plainText).getAsString(html => {
            const value = html.replace(/<img[^>]*>/g, '');
            resolve(value);
        });
    });
};

const ImagePaste = Extension.create({
    name: 'imagePaste',

    defaultOptions: {
        fileMatchRegex: /^image\/(gif|jpe?g|a?png|svg|webp|bmp)/i,
        disableImagePaste: false,
    },

    addProseMirrorPlugins() {
        const options = this.options;
        const renderer = this.options.render?.();

        return [
            new Plugin({
                key: new PluginKey('imagePasteHandler'),
                props: {
                    handlePaste(_view, event) {
                        console.log('handlePaste', event)
                        if (options.disableImagePaste && renderer?.onDisabledImagePaste) {
                            mapTextItems(event.clipboardData.items).then(value => {
                                if (value) {
                                    renderer.onDisabledImagePaste(value);
                                }
                            });

                            return true;
                        }

                        if (!options.disableImagePaste && renderer?.onImagePaste && event.clipboardData?.items?.length) {
                            const files = mapDataItems(event.clipboardData.items, options.fileMatchRegex);

                            if (files.length) {
                                renderer.onImagePaste(files, _view);

                                return true;
                            }
                        }
                        return false;
                    },

                    handleDrop(_view, event, slice, moved) {
                        console.log('handleDrop', { moved }, event)
                        if (moved) {
                            return false
                        }
                        if (!options.disableImagePaste && event.dataTransfer?.files?.length && renderer?.onImageDrop) {
                            const files = mapDataItems(event.dataTransfer.items, options.fileMatchRegex);

                            if (files.length) {
                                renderer.onImageDrop(files, _view);

                                return true;
                            }
                        }

                        return false;
                    },
                },
            }),
        ];
    },
})


const ImageResize = ImageExtension.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            src: {
                default: null,
            },
            alt: {
                default: null,
            },
            style: {
                default: 'cursor: pointer;',
            },
            width: {
                default: null,
            },
            height: {
                default: null
            }
        };
    },
    addNodeView() {
        return ({ node, editor, getPos }) => {
            const {
                view,
                options: { editable },
            } = editor;
            const { src, alt, style, width, height } = node.attrs;
            const $container = document.createElement('span');
            const $img = document.createElement('img');

            $container.style.position = "relative"
            $container.style.display = "inline-block"
            $container.appendChild($img);
            // debugger
            const item = uploaded_files.find(f => f.blobUrl == src)
            if (item && !item.finished) {
                const $progress = document.createElement('div');
                $progress.style.position = "absolute";
                $progress.style.top = "0";
                $progress.style.left = "0";
                $progress.style.right = "0";
                $progress.style.bottom = "0";
                $progress.style.backgroundColor = "rgba(200,200,200,0.75)";
                $progress.style.zIndex = "10";
                $progress.style.mixBlendMode = "saturation"
                $container.appendChild($progress);

                const $label = document.createElement('span');
                $label.style.position = "absolute";
                $label.style.top = "5px";
                $label.style.left = "5px";
                $label.style.fontWeight = "bold";
                $label.style.color = "white"
                $label.style.mixBlendMode = "difference"
                $container.appendChild($label)

                item.node = node
                item.getPos = getPos
                //item.img = $img
                item.progress = $progress
                item.label = $label
            }
            //const graySrc = await grayImage(src)
            //$img.setAttribute('src', graySrc);
            $img.setAttribute('src', src);
            $img.setAttribute('alt', alt);
            $img.setAttribute('style', style);
            if (width !== null) $img.setAttribute('width', width);
            if (height !== null) $img.setAttribute('height', height);
            $img.setAttribute('draggable', 'true');

            if (!editable) return { dom: $img };

            const dotsPosition = [
                'top: -4px; left: -4px; cursor: nwse-resize;',
                'top: -4px; right: -4px; cursor: nesw-resize;',
                'bottom: -4px; left: -4px; cursor: nesw-resize;',
                'bottom: -4px; right: -4px; cursor: nwse-resize;',
            ];

            let isResizing = false;
            let startX, startWidth, startHeight;

            $container.addEventListener('click', () => {
                const align = 'center' // $container.style.textAlign
                console.log('align: ', align)
                $container.setAttribute(
                    'style',
                    `display: inline-block; position: relative; outline: 2px dashed #6C6C6C; ${style}; cursor: pointer; width:fit-content` +
                    (align ? '; text-align: ' + align : ''),
                );

                Array.from({ length: 4 }, (_, index) => {
                    const $dot = document.createElement('div');
                    $dot.setAttribute(
                        'style',
                        `padding: 0; position: absolute; width: 9px; height: 9px; border: 1.5px solid #6C6C6C; border-radius: 50%; ${dotsPosition[index]}`,
                    );

                    $dot.addEventListener('mousedown', e => {
                        e.preventDefault();
                        isResizing = true;
                        startX = e.clientX;
                        startWidth = $container.offsetWidth;
                        startHeight = $container.offsetHeight;

                        const onMouseMove = (e) => {
                            if (!isResizing) return;

                            const deltaX = e.clientX - startX;

                            const aspectRatio = startWidth / startHeight;
                            const newWidth = startWidth + deltaX;
                            const newHeight = newWidth / aspectRatio;

                            $container.style.width = newWidth + 'px';
                            $container.style.height = newHeight + 'px';

                            $img.style.width = newWidth + 'px';
                            $img.style.height = newHeight + 'px';
                        };

                        const onMouseUp = () => {
                            isResizing = false;
                            if (typeof getPos === 'function') {
                                const newAttrs = {
                                    ...node.attrs,
                                    style: `${$img.style.cssText}`,
                                };
                                console.log('resize ', { newAttrs })
                                view.dispatch(view.state.tr.setNodeMarkup(getPos(), null, newAttrs));
                            }

                            document.removeEventListener('mousemove', onMouseMove);
                            document.removeEventListener('mouseup', onMouseUp);
                        };

                        document.addEventListener('mousemove', onMouseMove);
                        document.addEventListener('mouseup', onMouseUp);
                    });
                    $container.appendChild($dot);
                });
            });

            document.addEventListener('click', (e) => {
                if (!$container.contains(e.target)) {
                    $container.removeAttribute('style');
                    if ($container.childElementCount > 2) {
                        try {
                            for (let i = 0; i < 4; i++) {
                                $container.removeChild($container.lastChild);
                            }
                        }
                        catch (err) {
                            console.warn(err)
                        }
                    }
                }
            });

            return {
                dom: $container,
            };
        };
    },
});



const editor = useEditor({
    content: contenidoHtml.value,
    extensions: [
        // HtmlMyNode,
        StarterKit,
        Document,
        TextStyle,
        Color,
        Superscript.extend({
            addAttributes() {
                return {
                    id: { // permitimos el atributo id, para las notas al pie
                        default: null,
                    }
                };
            }
        }),
        Underline,
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
        Highlight,
        Link.configure({
            openOnClick: false,
        }),
        Table.configure({
            resizable: true
        }),
        TableRow,
        TableHeader,
        TableCell,
        //ImageExtension,
        ImageResize
            .configure({
                inline: true,
                allowBase64: true,
            }),
        ImagePaste.configure({
            fileMatchRegex: /^image\/(gif|jpe?g|a?png|svg|webp|bmp)/i,
            disableImagePaste: false,
            render: () => {
                return {
                    onImagePaste: (files, _view) => {
                        console.log('onImagePaste')
                        files.forEach(file => {
                            onInsertImage(file, _view)
                        });
                    },
                    onImageDrop: (files, _view) => {
                        console.log('onImageDrop')
                        files.forEach(file => {
                            onInsertImage(file, _view)
                        });
                    },
                    onDisabledImagePaste: text => {
                        console.log('onDisabledImagePaste', text)
                        // add text to editor if you want, or display an error/upselll message
                    }
                };
            },
        })
    ],
    onUpdate: ({ editor }) => {
        console.log('onUpdate')
        contenidoHtml.value = editor.getHTML()
        // contenidoMD.value = HtmlToMarkdown(editor.getHTML())
        // send the content to an API here
    },
})

async function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
        reader.readAsDataURL(file);
    });
}

const uploaded_files = []

// Función para encontrar la posición de un nodo de imagen en el documento
function findImageNodePos(src) {
    const { doc } = editor.value.state;
    let node = null
    let pos = null
    doc.descendants((nodeFound, posFound) => {
        if (nodeFound.type.name === "image" && nodeFound.attrs.src === src) {
            pos = posFound;
            node = nodeFound
            return false; // detener la búsqueda
        }
    });
    return { node, pos };
}

// cambiamos el src del nodo
function replaceImage(fromSrc, toSrc, _view) {
    // 1. Obtén una referencia al nodo de imagen y su posición
    // const { node, pos } = findImageNodePos(fromSrc)

    // 2. Crea una nueva transacción
    /*const transaction = editor.value.state.tr;
    // 3. Reemplaza el nodo de imagen original
    transaction.replaceWith(
        pos,
        pos + node.nodeSize,
        editor.value.schema.nodes.image.create({ src: toSrc })
    );*/

    // 4. Aplica la transacción
    // _view.dispatch(transaction);

    //_view.dispatch(_view.state.tr.setNodeMarkup(pos, null, { src: toSrc }));
}

function uploadImage(file, blobUrl, _view) {
    console.log('uploadImage', file, blobUrl)
    // guardamos los archivos uploading
    uploaded_files.push({ blobUrl, file, url: null, img: null, error: null })
    const item = uploaded_files[uploaded_files.length - 1]
    const startTime = new Date()

    const formData = new FormData();
    formData.append('file', file);
    formData.append('destinationPath', props.mediaFolder);

    const req = new XMLHttpRequest(); // Initialize request
    req.open('POST', '/files/upload/image');
    req.upload.addEventListener('progress', function (event) {
        if (event.lengthComputable) {
            const progress = Math.floor((event.loaded / event.total) * 100 / 2)
            // console.log(`Progreso: ${progress}%`);
            item.progress.style.left = progress + '%'
            item.label.innerText = progress + '%'
            console.log('uploading', item.progress.style.left)
        }
    })

    // añadir header:
    req.setRequestHeader('X-CSRF-Token', document.querySelector('meta[name="csrf-token"]').content)

    // cuando termine el request:
    req.addEventListener('load', async function () {
        console.log('response:', req.response)
        const data = JSON.parse(req.response)
        if (data.error) {
            // si hay algun error, aplica la codificacion base64 en la imagen
            console.error(data.error)
            item.url = await fileToBase64(file)
            item.finished = true
        }
        else {
            item.url = data.data.filePath
        }

        const img = new Image()
        img.src = item.url

        const endUploadTime = new Date()
        const time = endUploadTime - startTime
        console.log('time elapsed in ms', time)

        const lapso = 200
        const avance = 50 / time * lapso * 1.4 // la descarga es más rápida que la subida
        let progress = 50.0

        function muestraProgreso() {
            progress += avance
            progress = Math.min(progress, 99.9)
            const p = Math.round(progress)
            item.progress.style.left = p + '%'
            item.label.innerText = p + '%'
            console.log('downloading', item.progress.style.left)
        }

        // ya da un paso antes
        muestraProgreso()

        // simulamos el progreso de descarga, durará más o menos igual que la subida
        const downloadInterval = setInterval(muestraProgreso, lapso)

        img.onload = () => {
            item.progress.remove()
            item.label.remove()
            // replaceImage(item.blobUrl, item.url, _view)

            // reemplaza el src de la imagen
            _view.dispatch(_view.state.tr.setNodeMarkup(item.getPos(), null, { src: item.url }));

            clearInterval(downloadInterval)
        }
    })

    req.send(formData)
}



async function onInsertImage(file, _view) {
    console.log('onInsertImage', file)
    // const url = 'https://tseyor.org/images/biblioteca-tseyor3.jpg'
    // generar imagen en URI de base64:
    // const url = await grayImage(URL.createObjectURL(file))
    const url = URL.createObjectURL(file)

    uploadImage(file, url, _view)

    //await fileToBase64(file);
    // // editor.value.chain().focus().insertContent(`<img src="${url}"/>`).run()
    //editor.value.chain().focus().insertContent (`<span>hola</span>`).run()

    editor.value.commands.insertContent(`<img src="${url}"/>`)

    // to-do: upload image
}

if (typeof window !== 'undefined') {
    window.getTipTapEditorInstance = () => editor.value;
}



</script>

<style scoped>
/* Basic editor styles */
@reference "../../css/app.css";

ul,
ol {
    padding: 0 1rem;
}

h1,
h2,
h3,
h4,
h5,
h6 {
    line-height: 1.1;
}

code {
    background-color: rgba(#616161, 0.1);
    color: #616161;
}

pre {
    background: #0D0D0D;
    color: #FFF;
    font-family: 'JetBrainsMono', monospace;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;


}

pre>code {
    color: inherit;
    padding: 0;
    background: none;
    font-size: 0.8rem;
}

img {
    max-width: 100%;
    height: auto;
}

blockquote {
    padding-left: 1rem;
    border-left: 2px solid rgba(#0D0D0D, 0.1);
}

hr {
    margin: 2rem;
    opacity: .5;
    border-top: 2px solid rgba(#777, 0.2);
}

.tiptap-editor:deep(.ProseMirror) {
    padding: .5rem;
    @apply min-h-[300px] overflow-auto;
    background-color: var(--tblr-bg-forms);
}

.tiptap-editor:deep(.ProseMirror:focus) {
    box-shadow: 0 0 transparent, 0 0 0 0.25rem rgba(var(--tblr-primary-rgb), .25);
}

.tiptap-editor:deep(img) {
    display: inline-block
}


.tiptap-editor:deep(table) {
    border-collapse: collapse;
    margin: 0;
    overflow: hidden;
    table-layout: fixed;
}

.tiptap-editor:deep(td, th) {
    border: 2px solid #ced4da;
    box-sizing: border-box;
    min-width: 1em;
    padding: 3px 5px;
    position: relative;
}

.tiptap-editor:deep(th) {
    background-color: #f1f3f5;
    font-weight: bold;
    text-align: left;
}

.tiptap-editor:deep(.selectedCell:after) {
    background: rgba(200, 200, 255, 0.4);
    content: "";
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    pointer-events: none;
    position: absolute;
    z-index: 2;
}

.tiptap-editor:deep(.column-resize-handle) {
    background-color: #adf;
    bottom: -2px;
    position: absolute;
    right: -2px;
    pointer-events: none;
    top: 0;
    width: 4px;
}


.tiptap-editor:deep(.tableWrapper) {
    padding: 1rem 0;
    overflow-x: auto;
}

.tiptap-editor:deep(.resize-cursor) {
    cursor: ew-resize;
    cursor: col-resize;
}

.tiptap-editor:deep(table) p {
    margin: .1rem 0;
}

.tiptap-editor:deep(h1) {
    @apply text-4xl mb-9 font-bold leading-tight;
}

.tiptap-editor:deep(h2) {
    @apply text-3xl mb-7 font-semibold;
}

.tiptap-editor:deep(h3) {
    @apply text-2xl mb-5 font-semibold;
}

.tiptap-editor:deep(h4) {
    @apply text-xl mb-4 font-semibold;
}


.tiptap-editor:deep(p) {
    @apply my-6 text-base;
}

.tiptap-editor:deep(ul) {
    @apply list-disc pl-8;
}

.tiptap-editor:deep(p) a {
    @apply text-blue-500 underline;
}

.tiptap-editor:deep(p) a:hover {
    @apply text-blue-600;
}
</style>
