<template>
    <img :src="finalSrc" :alt="alt" />
</template>



<script setup>
import { imageUrl } from '@/utils.js'

const props = defineProps({
    src: {
        type: String,
        required: true
    },
    alt: {
        type: String,
    },
    title: {
        type: String
    }
});

const finalSrc = computed(() => imageUrl(props.src))

</script>
