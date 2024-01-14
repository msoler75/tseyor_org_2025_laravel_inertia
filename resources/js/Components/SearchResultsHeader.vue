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

const queryString = window.location.search.replace(/[\?&]page=\d+/, '').replace('?categoria=_', '')

onMounted(() => {
    const urlParams = new URLSearchParams(queryString);
    query.value = urlParams.get(props.keyword);
});

const stringArguments = computed(() => {
  let phrase = '';

  if (props.category && query.value) {
    phrase = ` para '${query.value}' en <em>${props.category}</em>`;
  } else if (props.category) {
    phrase = ` en <em>${props.category}</em>`;
  } else if (query.value) {
    phrase = ` para '${query.value}'`;
  }

  return phrase;
});
</script>
