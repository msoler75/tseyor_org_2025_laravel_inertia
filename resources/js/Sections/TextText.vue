<template>
    <div class="py-12">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 gap-7 lg:gap-12">
            <h2 class="text-2xl font-bold order-0 mb-0">{{ title }}</h2>
            <h2 class="text-2xl font-bold mb-0 order-3  md:order-2">{{ title2 }}</h2>
            <div class="order-2">
                <slot name="text1"></slot>
            </div>
            <div class="order-3">
                <slot name="text2"></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    title2: {
        type: String,
        required: false,
        default: null
    }
})
</script>
