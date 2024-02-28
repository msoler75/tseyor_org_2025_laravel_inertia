<template>
    <div id="content-main" class="transition-opacity duration-200"
            :class="fadeOnNavigate && useNav().navigating?'opacity-0 pointer-events-none':''">
        <slot/>
    </div>
</template>

<script setup>
defineProps({
    fadeOnNavigate: {type: Boolean, default: true}
})
</script>
