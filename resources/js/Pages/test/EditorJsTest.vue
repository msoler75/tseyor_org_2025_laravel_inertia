<template>
    <EditorJs v-model="content" />
</template>

<script setup>
import EditorJs from '@/Components/EditorJs/EditorJs.vue'

const content = ref("hola")
</script>
