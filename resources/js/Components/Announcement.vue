<template>
    <div v-if="text&&showit" class="bg-primary text-primary-content px-4 lg:px-8 py-1 flex justify-between items-center">
        <div class="text-transparent">.....</div>
        <div class="text-sm" v-html="text" />
        <button class="text-2xl" @click="hide">
            <Icon icon="ph:x-square-duotone" />
        </button>
    </div>
</template>

<script setup>
import { useNav } from '@/Stores/nav'
const nav = useNav()
const props = defineProps({
    text: {}
})
const showit = ref(!!props.text)

function hide() {
    showit.value = false
    nav.announce = false
}
</script>
