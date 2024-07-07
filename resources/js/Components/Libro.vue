<template>
    <Link :href="route('libro', libro.slug)">
    <Image :src="libro.imagen" width="200" height="300" />
    </Link>
</template>

<script setup>
defineProps({
    libro: Object
})
</script>
