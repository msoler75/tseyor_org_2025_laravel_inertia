<template>
         <component :is="as" class="transition-opacity duration-200"
            :class="nav.navigating?'opacity-0 pointer-events-none':''">
            <slot />
            </component>
</template>

<script setup>
import { useNav } from '@/Stores/nav'
const nav = useNav()
defineProps({
    as: {default: 'div'}
})
</script>
