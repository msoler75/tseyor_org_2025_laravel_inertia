<template>
    <div class="text-xl font-bold">
        <div v-if="queryString && results.data.length > 0">
            {{ results.total }} {{ results.total == 1 ? 'resultado' : 'resultados' }}{{ stringArguments }}
        </div>
        <div v-else-if="queryString && results.data.length == 0">No hay resultados{{ stringArguments }}</div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
const query = ref('');

const props = defineProps({
    results: {},
    keyword: {
        type: String,
        required: false,
        default: "buscar"
    },
    arguments: {}
})

const queryString = window.location.search;

onMounted(() => {
    const urlParams = new URLSearchParams(queryString);
    query.value = urlParams.get(props.keyword);
});

const stringArguments = computed(() => {
    if (props.arguments && !query.value) return ''
    return ` para '${query.value}'`
})
</script>
