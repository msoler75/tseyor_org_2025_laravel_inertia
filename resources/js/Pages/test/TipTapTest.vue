<template>
    <div class="p-3">
        <div v-show="!editingMarkdown">
            <FullMenuBar v-if="editor" :editor="editor" @toggle-markdown="toggleMarkdown" />
            <EditorContent :editor="editor" class="tiptap-editor" />
        </div>
        <button v-show="editingMarkdown" @click="toggleMarkdown" class="btn btn-neutral btn-xs mb-2">
            <Icon icon="mdi-close" /> Cerrar editor Markdown
        </button>
        <MdEditor v-model="contenidoMD" v-show="editingMarkdown"
            :toolbarsExclude="['save', 'sub', 'sup', 'katex', 'mermaid', 'htmlPreview', 'catalog', 'github', 'revoke', 'next', 'image']"
            :footers="[]" :preview="false" />
        {{ contenidoMD }}
    </div>
</template>

<script setup>
import { Extension } from '@tiptap/core';
import { Plugin, PluginKey } from 'prosemirror-state';

import Highlight from '@tiptap/extension-highlight'
import StarterKit from '@tiptap/starter-kit'
import TextAlign from '@tiptap/extension-text-align'
import TextStyle from '@tiptap/extension-text-style'
import Underline from '@tiptap/extension-underline'
import Image from '@tiptap/extension-image'
import { Color } from '@tiptap/extension-color'
import Superscript from '@tiptap/extension-superscript'
// import ImageResize from 'tiptap-extension-resize-image';
import Link from '@tiptap/extension-link'
import FullMenuBar from "./FullMenuBar.vue";
import { useEditor, EditorContent } from "@tiptap/vue-3";

import { MdEditor } from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';

import { HtmlToMarkdown, MarkdownToHtml, detectFormat } from '@/composables/markdown.js'

import screenfull from 'screenfull'

// insertHTml
import { Node } from '@tiptap/core'

import { DOMParser } from 'prosemirror-model'

const props = defineProps({
    name: String,
    content: {
        type: String, default: // '<p><span style="color: red"><u>Describe</u> </span> aquí...</p> <p><img src="/almacen/medios/fruto%20castano.jpg" style="width:155px; height:235.87px"></p> Aleph<a href="#note-1"><sup>1</sup></a></p> <hr/> <p><sup id="note-1">1</sup> Aleph</p>'
            `<p style="text-align: center">antes: <img src="/almacen/medios/fruto%20castano.jpg" width='103px' height="156.766px"> después</p>  <p><sup id='pepe'>1</sup >con ID</p>
            `
    },
    mediaFolder: { type: String, default: 'medios' },
})



const emit = defineEmits(['update:modelValue', 'change'])

// CONVERT MD <-> HTML

const format = ['md', 'ambiguous'].includes(detectFormat(props.content).format) ? 'md' : 'html'

const contenidoMD = ref(format == 'md' ? props.content : HtmlToMarkdown(props.content))
const contenidoHtml = ref(format == 'html' ? props.content : MarkdownToHtml(props.content))



watch(contenidoHtml, (value) => {
    contenidoMD.value = HtmlToMarkdown(value)
    if (props.format == 'md') {
        emit('change', contenidoMD.value)
        emit('update:modelValue', contenidoMD.value)
    }
    else {
        emit('change', contenidoHtml.value)
        emit('update:modelValue', contenidoHtml.value)
    }
})


// MARKDOWN EDIT

const editingHtml = ref(false)
const editingMarkdown = ref(false)

watch(editingMarkdown, (value) => {
    if (value)
        contenidoMD.value = HtmlToMarkdown(contenidoHtml.value)
    else {
        contenidoHtml.value = MarkdownToHtml(contenidoMD.value)
        editor.value.commands.setContent(contenidoHtml.value)
    }
})

function toggleMarkdown() {
    console.log('toggleMarkdown')
    editingMarkdown.value = !editingMarkdown.value
    if (!editingMarkdown.value) {

    }
}


// TIPTAP


// /* eslint-disable @typescript-eslint/naming-convention */


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
                                renderer.onImagePaste(files);

                                return true;
                            }
                        }
                        return false;
                    },

                    handleDrop(_view, event) {
                        if (!options.disableImagePaste && event.dataTransfer?.files?.length && renderer?.onImageDrop) {
                            const files = mapDataItems(event.dataTransfer.items, options.fileMatchRegex);

                            if (files.length) {
                                renderer.onImageDrop(files);

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


const ImageResize = Image.extend({
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

            $container.appendChild($img);
            $img.setAttribute('src', src);
            $img.setAttribute('alt', alt);
            $img.setAttribute('style', style);
            if(width!==null)$img.setAttribute('width', width);
            if(height!==null)$img.setAttribute('height', height);
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
                            if (isResizing) {
                                isResizing = false;
                            }
                            if (typeof getPos === 'function') {
                                const newAttrs = {
                                    ...node.attrs,
                                    style: `${$img.style.cssText}`,
                                };
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
                        for (let i = 0; i < 4; i++) {
                            $container.removeChild($container.lastChild);
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
        /*Image.configure({
            allowBase64: true,
        }),*/
        /* Image.configure({
             inline: true,
             allowBase64: true,
         }),
         */
        ImageResize
        .configure({
                inline: true,
                allowBase64: true,
            })

            ,
        /*ImagePaste.configure({
            fileMatchRegex: /^image\/(gif|jpe?g|a?png|svg|webp|bmp)/i,
            disableImagePaste: false,
            render: () => {
                return {
                    onImagePaste: files => {
                        files.forEach(file => {
                            onInsertImage(file)
                        });
                    },
                    onImageDrop: files => {
                        files.forEach(file => {
                            onInsertImage(file)
                        });
                    },
                    onDisabledImagePaste: text => {
                        console.log('onDisabledImagePaste', text)
                        // add text to editor if you want, or display an error/upselll message
                    }
                };
            },
        })*/
    ],
    onUpdate: ({ editor }) => {
        contenidoHtml.value = editor.getHTML()
        // send the content to an API here
    },
})




function onInsertImage(file) {
    console.log('onInsertImage', file)
    const url = 'https://tseyor.org/images/biblioteca-tseyor3.jpg'
    // // editor.value.chain().focus().insertContent(`<img src="${url}"/>`).run()
    //editor.value.chain().focus().insertContent (`<span>hola</span>`).run()
    editor.value.commands.insertContent(`<img src="${url}"/>`)
}
</script>

<style scoped>
/* Basic editor styles */


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

pre  >code {
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
}

.tiptap-editor:deep(img) {
    display: inline-block
}
</style>
