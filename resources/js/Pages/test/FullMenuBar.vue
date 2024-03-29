<script setup>
const props = defineProps({ editor: Object });
const emit = defineEmits(['toggle-markdown'])
const items = ref([
    {
        icon: "mdi-format-bold",
        title: "Negrita",
        action: () => props.editor.chain().focus().toggleBold().run(),
        isActive: () => props.editor.isActive("bold"),
    },
    {
        icon: "mdi-format-italic",
        title: "Cursiva",
        action: () => props.editor.chain().focus().toggleItalic().run(),
        isActive: () => props.editor.isActive("italic"),
    },
    {
        icon: "mdi:format-underline",
        title: "Subrayado",
        action: () => props.editor.chain().focus().toggleUnderline().run(),
        isActive: () => props.editor.isActive("underline"),
    },
    {
        icon: "mdi-format-strikethrough-variant",
        title: "Tachado",
        action: () => props.editor.chain().focus().toggleStrike().run(),
        isActive: () => props.editor.isActive("strike"),
    },
    {
        icon: "mdi:format-superscript",
        title: "Superíndice",
        action: () => props.editor.chain().focus().toggleSuperscript().run(),
        isActive: () => props.editor.isActive("superscript"),
    },
    /*{
        icon: "mdi-code-tags",
        title: "Code",
        action: () => props.editor.chain().focus().toggleCode().run(),

        isActive: () => props.editor.isActive("code"),
    },*/
    {
        icon: "mdi:format-color-highlight",
        title: "Resaltar",
        action: () => props.editor.chain().focus()?.toggleHighlight().run(),
        isActive: () => props.editor.isActive("highlight"),
    },
    {
        type: "color",
        title: "Color del texto",
        icon: 'mdi:format-color'
    },
    {
        type: "divider",
    },
    {
        icon: "mdi-format-header-1",
        title: "Título 1",
        action: () =>
            props.editor.chain().focus().toggleHeading({ level: 1 }).run(),

        isActive: () => props.editor.isActive("heading", { level: 1 }),
    },
    {
        icon: "mdi-format-header-2",
        title: "Título 2",
        action: () =>
            props.editor.chain().focus().toggleHeading({ level: 2 }).run(),

        isActive: () => props.editor.isActive("heading", { level: 2 }),
    },
    {
        icon: "mdi-format-paragraph",
        title: "Párrafo",
        action: () => props.editor.chain().focus().setParagraph().run(),

        isActive: () => props.editor.isActive("paragraph"),
    },
    {
        type: "divider",
    },
    {
        icon: "mdi:format-align-left",
        title: "Alinear a izquierda",
        action: () => props.editor.chain().focus().setTextAlign('left').run(),
        isActive: () => props.editor.isActive({ textAlign: 'left' }),
    },
    {
        icon: "mdi:format-align-center",
        title: "Alinear a centro",
        action: () => props.editor.chain().focus().setTextAlign('center').run(),
        isActive: () => props.editor.isActive({ textAlign: 'center' }),
    },
    {
        icon: "mdi:format-align-right",
        title: "Alinear a derecha",
        action: () => props.editor.chain().focus().setTextAlign('right').run(),
        isActive: () => props.editor.isActive({ textAlign: 'right' }),
    },
    {
        icon: "mdi:format-align-justify",
        title: "Alinear justificado",
        action: () => props.editor.chain().focus().setTextAlign('justify').run(),
        isActive: () => props.editor.isActive({ textAlign: 'justify' }),
    },

    {
        type: "divider",
    },
    {
        icon: "mdi-format-list-bulleted",
        title: "Lista",
        action: () => props.editor.chain().focus().toggleBulletList().run(),

        isActive: () => props.editor.isActive("bulletList"),
    },
    {
        icon: "mdi-format-list-numbered",
        title: "Lista numerada",
        action: () => props.editor.chain().focus().toggleOrderedList().run(),

        isActive: () => props.editor.isActive("orderedList"),
    },
    /*{
        icon: "mdi-format-list-checks",
        title: "Task List",
        action: () => props.editor.chain().focus()?.toggleTaskList().run(),

        isActive: () => props.editor.isActive("taskList"),
    },*/
    /*
    {
        icon: "mdi-code-braces",
        title: "Code Block",
        action: () => props.editor.chain().focus().toggleCodeBlock().run(),

        isActive: () => props.editor.isActive("codeBlock"),
    },*/
    {
        type: "divider",
    },
    {
        icon: "mdi-format-quote-open",
        title: "Cita en bloque",
        action: () => props.editor.chain().focus().toggleBlockquote().run(),

        isActive: () => props.editor.isActive("blockquote"),
    },
    {
        icon: "mdi-minus",
        title: "Regla horizontal",
        action: () => props.editor.chain().focus().setHorizontalRule().run(),
    },
    {
        icon: computed(() => props.editor.isActive("link") ? "mdi:link-variant-off" : "mdi:link-variant"),
        title: computed(() => props.editor.isActive("link") ? "Remover enlace" : "Insertar enlace"),
        action: () => props.editor.isActive("link") ? props.editor.chain().focus().unsetLink().run() : setLink(),
        isActive: () => props.editor.isActive("link"),
        isDisabled: computed(() => props.editor.state.selection.empty)
    },
    {
        type: "divider",
    },
    {
        icon: "mdi-wrap",
        title: "Salto de línea",
        action: () => props.editor.chain().focus().setHardBreak().run(),
    },
    {
        icon: "mdi-image",
        title: "Insertar imagen",

        action: () => {
            /*const url = window.prompt('URL')
            if (url) {
                this.editor.chain().focus().setImage({ src: url }).run()
            }*/
            showMediaManager.value = true
        },
    },
    {
        title: "Borrar Formato",
        icon: "mdi-format-clear",
        action: () =>
            props.editor.chain().focus().clearNodes().unsetAllMarks().run(),
        isDisabled: computed(() => props.editor.state.selection.empty)
    },
    {
        title: "Editor markdown",
        icon: "mdi:language-markdown-outline",
        action: () =>
            emit('toggle-markdown')
    },
    {
        type: "divider",
    },
    {
        icon: "mdi-undo",
        title: "Deshacer",
        action: () => props.editor.chain().focus().undo().run(),
    },
    {
        icon: "mdi-redo",
        title: "Rehacer",
        action: () => props.editor.chain().focus().redo().run(),
    },
]);


const showMediaManager = ref(false)

function onInsertImage(src) {
    // tiptap insert image here
    console.log('onInsertImage', src)
    // props.editor.chain().focus().insertContent (`<img src="${src}" style='width: auto; height: auto'/>`).run()
    props.editor.chain().focus().insertContent(`<img src="${src}" style='width: auto; height: auto'/>`).run()
    showMediaManager.value = false
}


function setLink() {
    const previousUrl = props.editor.getAttributes('link').href
    const url = window.prompt('URL', previousUrl)

    // cancelled
    if (url === null) {
        return
    }

    // empty
    if (url === '') {
        props.editor
            .chain()
            .focus()
            .extendMarkRange('link')
            .unsetLink()
            .run()

        return
    }

    // update link
    props.editor
        .chain()
        .focus()
        .extendMarkRange('link')
        .setLink({ href: url })
        .run()
}
</script>

<template>
    <div class="!h-fit flex flex-wrap justify-left gap-0.5 p-1 bg-base-100">
        <div v-for="item in items">
            <div class="mx-2 flex align-center h-full" v-if="item.type === 'divider'"></div>
            <div v-else-if="item.type == 'color'" class="tooltip tooltip-bottom relative" :data-tip="item.title">
                <input  type="color" class="opacity-0 absolute mt-0.5"
                @input="editor.chain().focus().setColor($event.target.value).run()"
                :value="editor.getAttributes('textStyle').color">
                    <button class="btn btn-neutral btn-xs pointer-events-none z-10 left-0 top-0"
                    :style="{color: editor.getAttributes('textStyle').color}">
                    <Icon :icon="item.icon" class="text-lg" />
                </button>
            </div>
            <div v-else class="tooltip tooltip-bottom" :data-tip="item.title">
                <button class="btn btn-neutral btn-xs" :class="item.isActive && item.isActive() ? 'btn-active' : ''"
                    @click="item.action" :disabled="item.isDisabled">
                    <Icon :icon="item.icon" class="text-lg" />
                </button>
            </div>
        </div>
    </div>

    <Modal :show="showMediaManager" @close="showMediaManager = false" maxWidth="4xl">
        <div class="flex flex-col">
            <FileManager :ruta="mediaFolder" @image="onInsertImage" :modo-insertar="true"
                class="min-h-[calc(100vh-160px)] max-h-[calc(100vh-160px)] h-[calc(100vh-160px)] overflow-y-auto" />
            <div class="p-3 flex justify-end border-t border-gray-500 border-opacity-25">
                <button @click.prevent="showMediaManager = false" class="btn btn-neutral">Cerrar</button>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.btn-active {
    @apply text-primary;
}
</style>
