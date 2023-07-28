<template>
    <Prose ref="container">
        {{ images }}
        <Markdown v-if="isMarkdown" :source="content" />
        <div v-else v-html="content" />
    </Prose>
</template>

<script setup>
import { v3ImgPreviewFn } from 'v3-img-preview'
import Markdown from 'vue3-markdown-it';

const props = defineProps({
    content: {
        type: String,
        required: true,
    },
});

const isMarkdown = computed(() => true)
const container = ref(null)
const images = ref([])

onMounted(() => {
    nextTick(() => {
        console.log('container:', container.value.$el)
        const imgElements = container.value.$el.querySelectorAll('img');
        images.value = Array.from(imgElements)
        for (const index in images.value)
            images.value[index].addEventListener('click', () => handlePreview(index))
    });
});

function handlePreview(index) {
    console.log('clicked ', index, images.value[index])
    v3ImgPreviewFn({ images:images.value.map(img=>img.src), index })
}

</script>


<style scoped>
:deep(img) {
    @apply max-w-full mx-auto mb-3 block;
}
</style>
