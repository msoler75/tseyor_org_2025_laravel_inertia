<template>
    <!-- Stub vacío para SSR - TipTapEditor solo se carga en el cliente -->
    <div class="tiptap-editor-wrapper"></div>
</template>

<script setup>
// Componente stub para SSR
// El componente real TipTapEditor solo se carga en el cliente via ClientOnly
defineProps({
    modelValue: String,
    full: Boolean,
    fullEditor: Boolean,
    format: String,
    mediaFolder: String,
    id: String
})
</script>
