<template>
    <div class="sections" :style="computedStyle">
        <slot></slot>
    </div>
</template>


<script setup>

const props = defineProps({
    height: {}
})

const computedStyle = computed(() => {
    return {
        '--sectionHeight': props.height ? `${props.height}px` : '',
    }
})
</script>



<style>
@reference "../../css/app.css";

.sections>* {
    @apply w-full mx-auto;
}

.sections>*>* {
    @apply mx-auto;
}

.sections:not([white-first])>.section:nth-child(2n) {
    @apply bg-base-100;
}

.sections[white-first]>.section:nth-child(2n+1) {
    @apply bg-base-100;
}

</style>
