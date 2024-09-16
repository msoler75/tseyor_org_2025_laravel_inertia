<template>
    <h1>Test TimeAgo</h1>
    <div v-for="fecha in fechas" :key="fecha.i" class="flex gap-2">
        {{ fecha.i }} - <TimeAgo :date="fecha.date" />
    </div>
</template>

<script setup>

const fechas = ref([]);

const start = Date.now();
for (let i = 0; i > -3601; i--) {
    // Añadimos i segundos al timestamp actual
    fechas.value.push({i, date: start/1000 + i});
}
</script>
