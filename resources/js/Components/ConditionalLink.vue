<template>
    <component :is="isLink?Link:tag" class="conditional-link" :data-link="isLink ? 1 : 0" :href="href"
        :preserve-page="preservePage" :auto-scroll="autoScroll">
        <slot />
    </component>
</template>

<script setup>
// import Link from '@/Components/Link.vue'
import { Link } from "@inertiajs/vue3";

defineProps({
    isLink: { type: Boolean, required: true },
    href: { type: String },
    tag: { type: String, default: 'a' },
    preservePage: {
        type: Boolean,
        default: false
    },
    autoScroll: {
        type: Boolean,
        default: true
    },
  /*  disabled: { // permite deshabilitar el enlace
        type: Boolean,
        default: false
    }*/
})
</script>
