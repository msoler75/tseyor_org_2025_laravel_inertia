<template>
    <div v-if="(filtrado && results.data.length > 0) || (filtrado && results.data.length == 0)" >
        <h3 v-if="filtrado && results.data.length > 0" class="mt-0">
            {{results.total }} {{results.total==1?'resultado':'resultados'}} para '{{ filtrado }}'
        </h3>

        <h1 v-else-if="filtrado && results.data.length == 0">No hay resultados para '{{ filtrado }}'</h1>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
const filtrado = ref('');

const props = defineProps({
    results : {},
    keyword: {
        type: String,
        required: false,
        default: "buscar"
    }
})

onMounted(() => {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    filtrado.value = urlParams.get(props.keyword);
});
</script>
