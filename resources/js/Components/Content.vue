<template>
    <Prose ref="container">
        <Markdown v-if="isMarkdown" :source="content" :html="true" :linkify="true" />
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

const isMarkdown = computed(() => detectFormat(props.content).format == 'Markdown')
const container = ref(null)
const images = ref([])

onMounted(() => {
    nextTick(() => {
        console.log('container:', container.value.$el)
        const imgElements = container.value.$el.querySelectorAll('img');

        // añadimos la clase especial para contenedor de imagenes
        for(const img of imgElements)
            img.parentNode.className='images-wrapper'

        // guardamos el array de imagenes del contenido
        images.value = Array.from(imgElements)
        for (const index in images.value)
            images.value[index].addEventListener('click', () => handlePreview(index))
    });
});

function handlePreview(index) {
    console.log('clicked ', index, images.value[index])
    v3ImgPreviewFn({ images: images.value.map(img => img.src), index })
}


function detectFormat(text) {
    // Contamos la cantidad de etiquetas HTML
    const htmlTagsCount = (text.match(/<\/?[a-z][a-z0-9]*\b[^>]*>/gi) || []).length;

    // Contamos la cantidad de marcadores Markdown
    const markdownMarkersCount = (text.match(/[*#_>\[\]`!-]|\!\[|\]\(/g) || []).length;

    console.log({ htmlTagsCount, markdownMarkersCount })

    // Usamos una expresión regular para detectar si hay algún patrón de HTML en el texto
    const htmlPattern = /<(?:"[^"]*"['"]*|'[^']*'['"]*|[^'">])+>/i;
    const containsHTMLPattern = htmlPattern.test(text);

    // Calculamos la probabilidad de que sea Markdown o HTML
    const totalMarkers = markdownMarkersCount + htmlTagsCount;
    const markdownProbability = markdownMarkersCount / (totalMarkers + 1);
    const htmlProbability = htmlTagsCount / (totalMarkers + 1);

    // Establecemos un umbral de probabilidad para determinar el formato
    const threshold = 0.6;
    if (markdownProbability >= threshold && markdownProbability > htmlProbability) {
        return { format: "Markdown", probability: markdownProbability };
    } else if (htmlProbability >= threshold && htmlProbability > markdownProbability && containsHTMLPattern) {
        return { format: "HTML", probability: htmlProbability };
    } else {
        return { format: "Ambiguous", probability: 0.5 };
    }
}

</script>


<style scoped>
:deep(img) {
    @apply max-w-full mx-auto mb-3;
}
/* amplia la imagen en el ancho */
@media (min-width: 1154px) {
    :deep(p.images-wrapper) {
        width: 150%;
        margin-left: -25%;
    }
}
</style>
