<template>
         <component :is="as" class="transition-opacity duration-200"
            :class="useNav().navigating?'opacity-0 pointer-events-none':''">
            <slot />
            </component>
</template>

<script setup>
defineProps({
    as: {default: 'div'}
})
</script>
