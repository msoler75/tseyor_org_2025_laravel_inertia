<template>
    <TextImage :title="title" :subtitle="subtitle" :srcImage="srcImage" :buttonLabel="buttonLabel" :href="href"
        :src-width="srcWidth" :src-height="srcHeight" :imageRight="imageRight" :imageClass="imageClass"
        :imageSideClass="imageSideClass" :titleClass="titleClass" :textClass="textClass" :full="full" :cover="cover"
        :caption="caption"
        class="hero section bg-hero bg-cover bg-center py-20">
        <slot></slot>
        <template #action v-if="actionSlotPresent">
            <slot name="action"></slot>
        </template>
        <template #image v-if="imageSlotPresent">
            <slot name="image"></slot>
        </template>
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
        required: false,
        default: null
    },
    srcHeight: {
        type: [Number, String],
        required: false,
        default: null
    },
    caption: {
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
        required: false
    },
    imageSideClass: {
        type: String,
        required: false
    },
    titleClass: {
        type: String,
        required: false,
        default: ""
    },
    textClass: {
        type: String,
        required: false,
        default: "gap-5"
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

const slots = useSlots()
const imageSlotPresent = computed(() => !!slots.image)
const actionSlotPresent = computed(() => !!slots.action)

</script>
