<template>
    <QuillEditor theme="snow" v-model:content="contenidoHtml" contentType="html" :toolbar="toolbar" />
</template>


<script setup>
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

import { HtmlToMarkdown, MarkdownToHtml, detectFormat } from '@/composables/markdown.js'

const emit = defineEmits(['update:modelValue', 'change'])

const props = defineProps({
    modelValue: { type: String },
    format: { type: String, default: 'detect' },
    toolbar: { default: null } // null, 'essential' , 'minimal', 'full'   or   https://quilljs.com/docs/modules/toolbar/
})


// CONVERT MD <-> HTML

const format = computed(() => props.format != 'detect' ? props.format:
['Markdown', 'Ambiguous'].includes(detectFormat(props.modelValue).format) ? 'md' : 'html')

const contenidoMD = ref(format.value.toLowerCase() == 'md' ? props.modelValue : HtmlToMarkdown(props.modelValue))
const contenidoHtml = ref(format.value.toLowerCase() == 'html' ? props.modelValue : MarkdownToHtml(props.modelValue))

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



</script>
