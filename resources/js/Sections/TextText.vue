<template>
    <div class="py-12">
        <div class="container space-y-5">
            <h2 class="md:w-1/2 text-2xl font-bold">{{ title }}</h2>
            <div v-if="subtitle" class="md:w-1/2 text-lg text-center my-0">
                {{ subtitle }}
            </div>
        </div>
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 gap-7 lg:gap-12">
            <div>
                <slot name="text1"></slot>
            </div>
            <div>
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
    subtitle: {
        type: String,
        required: true,
    }
})
</script>
