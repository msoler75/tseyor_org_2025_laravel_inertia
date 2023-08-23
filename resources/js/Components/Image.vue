<template>
    <img :src="finalSrc" :alt="alt" />
</template>



<script setup>
import { getImageUrl } from '@/composables/utils.js'

const props = defineProps({
    src: {
        type: String,
        required: true
    },
    fallback: {
        type: String,
        required: false
    },
    alt: {
        type: String,
    },
    title: {
        type: String
    }
});

const finalSrc = computed(() => getImageUrl(props.src, props.fallback))

</script>
