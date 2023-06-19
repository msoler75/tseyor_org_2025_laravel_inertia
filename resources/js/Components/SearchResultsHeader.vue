<template>
    <div v-if="(filtrado && results.data.length > 0) || (filtrado && results.data.length == 0)" >
        <h1 v-if="filtrado && results.data.length > 0" class="mt-0">
            {{results.total }} resultado(s) para '{{ filtrado }}'
        </h1>

        <h1 v-else-if="filtrado && results.data.length == 0">No hay resultados para '{{ filtrado }}'</h1>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
const filtrado = ref('');

const props = defineProps({
    results : {}
})

onMounted(() => {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    filtrado.value = urlParams.get('buscar');
});
</script>
