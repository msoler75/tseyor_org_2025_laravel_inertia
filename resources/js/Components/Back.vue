<template>
    <Link :href="enlace" class="flex h-fit gap-2 text-sm items-center hover:underline select-none">
    <Icon icon="ph:arrow-left" />
    <slot />
    </Link>
</template>

<script setup>
const props = defineProps({
    href: String
})

const currentUrl = window.location.href;
const parentUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/'));
const enlace = ref(props.href || parentUrl)

</script>
