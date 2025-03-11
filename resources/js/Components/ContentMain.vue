<template>
    <div id="content-main" class="transition-opacity duration-200"
            :class="fadeOnNavigate && nav.navigating?'opacity-0 pointer-events-none':''">
        <slot/>
    </div>
</template>

<script setup>
defineProps({
    fadeOnNavigate: {type: Boolean, default: true}
})

const nav = useNav()
</script>
