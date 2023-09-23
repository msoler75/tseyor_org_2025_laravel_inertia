<template>
    <div class="text-xl font-bold">
        <div v-if="queryString && results.total > 0" :class="classes">
            {{ results.total }} {{ results.total == 1 ? 'resultado' : 'resultados' }}<span v-html="stringArguments"/> :
        </div>
        <div v-else-if="queryString && results.total == 0">No hay resultados<span v-html="stringArguments"/></div>
    </div>
</template>

<script setup>
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
    arguments: {}
})

const queryString = window.location.search;

onMounted(() => {
    const urlParams = new URLSearchParams(queryString);
    query.value = urlParams.get(props.keyword);
});

const stringArguments = computed(() => {
    // if (props.arguments && !query.value) return ''
    if(props.category) return ` en <em>${props.category}</em>`
    if (!query.value) return ''
    return ` para '${query.value}'`
})
</script>
