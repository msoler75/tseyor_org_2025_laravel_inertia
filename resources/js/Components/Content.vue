<template>
    <Prose ref="container" class="text-container">
        <Markdown v-if="isMarkdown" :source="content" :html="true" :linkify="true"/>
        <div v-else v-html="content" />
    </Prose>
</template>

<script setup>
import { v3ImgPreviewFn } from 'v3-img-preview'
import Markdown from 'vue3-markdown-it';
import {detectFormat} from '@/composables/markdown.js'

const props = defineProps({
    content: {
        type: String,
        required: true,
    },
    format: {
        type: String,
        default: ''
    }
});

const isMarkdown = computed(() => props.format=='md'?true:['md', 'ambiguous'].includes(detectFormat(props.content).format) )
const container = ref(null)
const images = ref([])

onMounted(() => {
    nextTick(() => {
        console.log('container:', container.value.$el)
        const imgElements = container.value.$el.querySelectorAll('img');

        // añadimos la clase especial para contenedor de imagenes
        for (const img of imgElements)
            img.parentNode.className = 'images-wrapper'

        // guardamos el array de imagenes del contenido
        images.value = Array.from(imgElements)
        for (const index in images.value)
            images.value[index].addEventListener('click', () => handlePreview(index))


    // Obtener todos los enlaces de desplazamiento
    var scrollLinks = document.querySelectorAll('.footnote-ref a, a.footnote-backref');

    console.log({scrollLinks})

    // Agregar evento de clic a cada enlace
    scrollLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            console.log('clicked!')

            var targetId = this.getAttribute('href').substring(1);
            var targetElement = document.getElementById(targetId);

            if (targetElement) {
                console.log('got target')
                var offset = 90;
                var targetRect = targetElement.getBoundingClientRect();
                var targetOffsetTop = window.scrollY + targetRect.top - offset;

                window.scrollTo({
                    top: targetOffsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

});


});

function handlePreview(index) {
    console.log('clicked ', index, images.value[index])
    v3ImgPreviewFn({ images: images.value.map(img => img.src), index })
}



</script>


<style scoped>
:deep(img) {
    @apply max-w-full mx-auto mb-3;
}


.text-container :deep(h1) {
    @apply text-2xl;
}

.text-container :deep(h2) {
    @apply text-xl;
}

.text-container :deep(h3) {
    @apply text-lg;
}

/* amplia la imagen en el ancho */
@media (min-width: 1154px) {
    :deep(p.images-wrapper) {
        /* width: 150%;
        margin-left: -25%; */
        text-align: center;
    }
}
</style>
