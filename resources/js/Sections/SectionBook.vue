<template>
    <Section>
        <TextImage
            class="max-w-[1024px] mx-auto"
            :title="titulo"
            :buttonLabel="buttonLabel"
            :href="enlace">
            <slot></slot>
            <template #image>
                <Libro3d
                    :libro="{ slug, imagen: portada }"
                    :imageClass="imageClass" />
            </template>
        </TextImage>
    </Section>
</template>

<script setup>
defineProps({
    titulo: {
        type: String,
        required: true
    },
    slug: {
        type: String,
        required: true
    },
    portada: {
        type: String,
        required: true
    },
    enlace: {
        type: String,
        required: true
    },
    buttonLabel: {
        type: String,
        default: 'Ver en Biblioteca'
    },
    imageClass: {
        type: String,
        default: 'w-[200px]'
    }
})
</script>
