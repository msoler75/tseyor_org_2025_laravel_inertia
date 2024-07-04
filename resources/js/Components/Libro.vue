<template>
    <Link :href="route('libro', libro.slug)">
    <Image :src="libro.imagen" class="w-[200px] h-[300px]" />
    </Link>
</template>

<script setup>
defineProps({
    libro: Object
})
</script>
