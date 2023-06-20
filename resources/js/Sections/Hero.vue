<template>
    <TextImage
    :title="title"
    :subtitle="subtitle"
    :text="text"
    :srcImage="srcImage"
    :buttonLabel="buttonLabel"
    :href="href"
    :imageRight="imageRight"
    class="bg-hero bg-cover bg-center py-20"
    />
</template>

<script setup>

defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        required: true,
    },
    text: {
        type: String,
        required: true,
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
        required: true
    },
    imageRight: {
        type: Boolean,
        required: false,
        default: false
    }
})
</script>
