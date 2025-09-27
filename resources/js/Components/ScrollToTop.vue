<template>
    <Icon
        icon="ph:arrow-circle-up-duotone"
        @click="nav.scrollToTopSmart('smooth')"
    />
</template>

<script setup>
const nav = useNav();
</script>
