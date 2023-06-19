<template>
     <div class="prose p-content text-justify mx-auto" v-html="content"></div>
</template>

<script setup>

const props = defineProps({
    content: {
        type: String,
        required: true,
    },
});

onMounted(() => {
    replaceImagesWithHTML()
})

function replaceImagesWithHTML() {
    const imagenes = document.querySelectorAll('.p-content img')
    imagenes.forEach((imagen) => {
        const wrapper = document.createElement('div')
        wrapper.className = 'image-expanded'
        imagen.parentNode.insertBefore(wrapper, imagen)
        wrapper.appendChild(imagen)
    })
}
</script>


<style>
.image-expanded {
    display: flex;
    justify-content: center;
    transform: scale(1.15);
    margin-top: 2rem;
    margin-bottom: 2rem;
}
</style>
