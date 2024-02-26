<template>
    <div id="content-main" class="transition-opacity duration-200"
            :class="fadeOnNavigate && nav.navigating?'opacity-0 pointer-events-none':''">
        <slot/>
    </div>
</template>

<script setup>
import { useNav } from '@/Stores/nav'
const nav = useNav()

const props = defineProps({
    fadeOnNavigate: {type: Boolean, default: true}
})
</script>
