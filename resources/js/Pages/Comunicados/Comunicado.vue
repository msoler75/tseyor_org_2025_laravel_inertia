<template>
    <div class="container mx-auto px-4 py-8">
        <h1 class="max-w-xl md:w-[120%] mx-auto">{{ comunicado.titulo }}</h1>
        <p class="text-gray-600 text-sm mb-2">
            <TimeAgo :date="comunicado.published_at" />
        </p>
       <Prose :content="comunicado.texto"/>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    comunicado: {
        type: Object,
        required: true,
    },
});

onMounted(() => {
    replaceImagesWithHTML()
})

function replaceImagesWithHTML() {
    const imagenes = document.querySelectorAll('.container img')
    imagenes.forEach((imagen) => {
        const wrapper = document.createElement('div')
        wrapper.className = 'image-expanded'
        imagen.parentNode.insertBefore(wrapper, imagen)
        wrapper.appendChild(imagen)
    })
}
</script>

