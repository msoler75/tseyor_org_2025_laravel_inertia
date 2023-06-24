<template>
    <div v-if="text&&show" class="bg-primary text-primary-content px-4 lg:px-8 py-2 lg:py-3 flex justify-between items-center">
        <div class="text-transparent">.....</div>
        <div class="text-sm" v-html="text" />
        <button class="text-2xl" @click="hide">
            <Icon icon="ph:x-square-duotone" />
        </button>
    </div>
</template>

<script setup>
const show = ref(true)
defineProps({
    text: {}
})

function hide() {
    show.value = false
}
</script>
