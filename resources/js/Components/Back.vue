<template>
    <Link :href="href" class="flex gap-2 text-sm items-center">
    <Icon icon="ph:arrow-left-light" />
    <slot />
    </Link>
</template>

<script setup>
defineProps({
    href: String
})
</script>
