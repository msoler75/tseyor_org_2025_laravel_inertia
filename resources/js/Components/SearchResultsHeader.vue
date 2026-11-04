<template>
    <div class="text-xl font-bold">
        <div v-if="loading" class="mb-5">Cargando...</div>
        <div v-else-if="queryString && results.total > 0" :class="classes">
            {{ summary }}<template v-if="queryString">:</template>
        </div>
        <!-- "Demasiados" solo cuando hay una PALABRA BUSCADA demasiado genérica (validSearch=false).
             Con solo filtros (categoria/ano) sin palabra, validSearch es false por diseño del helper:
             en ese caso decir "Demasiados" seria mentira. -->
        <div v-else-if="queryString && !validSearch && query">Demasiados resultados{{ resultsSuffix }}</div>
        <div v-else-if="queryString && results.total == 0"  class="mb-24">{{ noResultsText }}</div>
    </div>
</template>

<script setup>
import { buildNoResultsMessage } from '@/utils/mensajesBusqueda.js';
import { buildResultsSummary, buildResultsSuffix } from '@/utils/resultadosHeader.js';

const query = ref('');

const props = defineProps({
    results: {},
    keyword: {
        type: String,
        required: false,
        default: "buscar"
    },
    classes: {default: 'mb-5'},
    category: {},
    resource: { type: String, required: false, default: '' },
    arguments: {},
    validSearch: {type: Boolean, default: true},
    loading: {type: Boolean, default: false},
})

const queryString = ref()

onMounted(() => {
    // '?categoria=_' ("todos") SÍ se cuenta como querystring para mostrar el total,
    // pero el helper ignora el marcador '_' en el sufijo de categoría.
    queryString.value = window.location.search.replace(/[\?&]page=\d+/, '');
    const urlParams = new URLSearchParams(queryString.value);
    query.value = urlParams.get(props.keyword);
});

const summary = computed(() =>
    buildResultsSummary({
        total: props.results.total,
        resource: props.resource,
        category: props.category,
        query: query.value,
    })
);

const resultsSuffix = computed(() =>
    buildResultsSuffix({ category: props.category, query: query.value })
);

const noResultsText = computed(() => buildNoResultsMessage(queryString.value, props.keyword));
</script>
