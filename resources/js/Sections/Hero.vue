<template>
    <TextImage
    :title="title"
    :subtitle="subtitle"
    :srcImage="srcImage"
    :buttonLabel="buttonLabel"
    :href="href"
    :imageRight="imageRight"
    :imageClass="imageClass"
    :textClass="textClass"
    class="bg-hero bg-cover bg-center py-20"
    >
    <slot></slot>
    </TextImage>
</template>

<script setup>

defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        required: false,
        default: null,
    },
    buttonLabel: {
        type: String,
        required: false,
    },
    href: {
        type: String,
        required: false,
    },
    srcImage: {
        type: String,
        required: false,
        default: null
    },
    imageRight: {
        type: Boolean,
        required: false,
        default: false
    },
    imageClass: {
        type: String,
        required: false,
        default: null
    }
})
</script>
