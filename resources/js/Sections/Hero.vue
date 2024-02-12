<template>
    <TextImage :title="title" :subtitle="subtitle" :srcImage="srcImage" :buttonLabel="buttonLabel" :href="href"
        :src-width="srcWidth" :src-height="srcHeight" :imageRight="imageRight" :imageClass="imageClass"
        :imageSideClass="imageSideClass" :textClass="textClass" :full="full" :cover="cover"
        class="hero section bg-hero bg-cover bg-center py-20">
        <slot></slot>
    </TextImage>
</template>

<script setup>

defineProps({
    title: {
        type: String,
        required: false
    },
    subtitle: {
        type: String,
        required: false
    },
    buttonLabel: {
        type: String,
        required: false
    },
    href: {
        type: String,
        required: false
    },
    srcImage: {
        type: String,
        required: false
    },
    srcWidth: {
        type: [Number, String],
        required: true,
        default: null
    },
    srcHeight: {
        type: [Number, String],
        required: true,
        default: null
    },
    imageRight: {
        type: Boolean,
        required: false,
        default: false
    },
    imageClass: {
        type: String,
        required: false
    },
    imageSideClass: {
        type: String,
        required: false
    },
    textClass: {
        type: String,
        required: false,
        default: "container gap-5"
    },
    full: {
        type: Boolean,
        required: false,
        default: false
    },
    cover: {
        type: Boolean,
        default: false
    }
})
</script>
