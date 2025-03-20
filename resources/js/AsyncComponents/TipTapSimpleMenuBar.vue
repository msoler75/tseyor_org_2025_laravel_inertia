<script setup>
const props = defineProps({
    editor: Object,
    mediaFolder: { type: String, default: 'medios' }
});
const emit = defineEmits(['toggle-markdown'])
const items = ref([
    {
        icon: "mdi-format-bold",
        title: "Negrita (Ctrl+B)",
        action: () => props.editor.chain().focus().toggleBold().run(),
        isActive: () => props.editor.isActive("bold"),
    },
    {
        icon: "mdi-format-italic",
        title: "Cursiva (Ctrl+I)",
        action: () => props.editor.chain().focus().toggleItalic().run(),
        isActive: () => props.editor.isActive("italic"),
    },
    {
        icon: "mdi:format-underline",
        title: "Subrayado (Ctrl+U)",
        action: () => props.editor.chain().focus().toggleUnderline().run(),
        isActive: () => props.editor.isActive("underline"),
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
    {
        icon: computed(() => props.editor.isActive("link") ? "mdi:link-variant-off" : "mdi:link-variant"),
        title: computed(() => props.editor.isActive("link") ? "Remover enlace" : "Insertar enlace"),
        action: () => props.editor.isActive("link") ? props.editor.chain().focus().unsetLink().run() : setLink(),
        isActive: () => props.editor.isActive("link"),
        isDisabled: computed(() => props.editor.state.selection.empty)
    },

]);



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
    <div class="flex flex-wrap items-start justify-left gap-0.5 p-1 bg-gray-700">
        <div v-for="item in items">
            <div class="mx-2 flex align-center h-full" v-if="item.type === 'divider'"></div>
            <div v-else-if="item.type == 'color'" class="relative" :title="item.title">
                <input type="color" class="opacity-0 absolute mt-0.5"
                    @input="editor.chain().focus().setColor($event.target.value).run()"
                    :value="editor.getAttributes('textStyle').color">
                <span class="but pointer-events-none z-10 left-0 top-0"
                    :style="{ color: editor.getAttributes('textStyle').color }">
                    <Icon :icon="item.icon"  />
                </span>
            </div>
                <span v-else class="but" :title="item.title" :class="item.isActive && item.isActive() ? 'btn-active' : ''"
                    @click="item.action" :disabled="item.isDisabled">
                    <Icon :icon="item.icon" />
                </span>
        </div>
    </div>
</template>

<style scoped>
@reference "../../css/app.css";

.btn-active {
    @apply text-primary;
}
span.but {
    @apply inline-block m-0.5 text-xl;
}
span.but:hover {
    @apply bg-gray-400;
}
</style>
