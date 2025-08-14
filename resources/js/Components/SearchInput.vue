<template>
    <div class="flex items-center gap-3">
        <slot></slot>

        <button v-if="query" type="button" @click="clearInput" class="btn border border-gray-500 border-opacity-20">
            Limpiar
        </button>


        <button v-if="query" type="submit" @click.prevent="submit" class="btn btn-primary"
            :disabled="submitting || (query == savedQuery && !cambiado)">
            Buscar
        </button>

        <div class="flex items-center relative">
            <div class="absolute z-10 left-2 transform scale-110 -translate-y-[.1rem] text-gray-600">
                <Spinner v-show="submitting" />
                <Icon v-show="!submitting" icon="ph:magnifying-glass-bold" />
            </div>
            <form @submit.prevent="submit">
                <input class="search-input pl-8 focus:bg-base-100 relative bg-transparent shadow-none px-6 py-3 focus:shadow-outline
                    text-left w-full rounded-md"
                    :class="[
                        query ? 'border-0 border-b border-gray-500 focus:border-b' : 'border-gray-500/20',
                        inputClass
                    ]"
                    @keydown.Esc="clearInput" autocomplete="off" type="text" :name="keyword" :placeholder="placeholder"
                    @focus="$emit('focus')" @blur="$emit('blur-xs')" v-model="query" />
            </form>
        </div>
    </div>
</template>

<script setup>

import { router } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: String,
    keyword: {
        type: String,
        required: false,
        default: "buscar"
    },
    placeholder: {
        type: String,
        required: false,
        default: "Buscar..."
    },
    inputClass: {
        type: String,
        required: false,
        default: 'max-w-[200px] lg:max-w-[400px]'
    },
    arguments: {},
    doSearch: { type: Boolean, default: true }
})

const query = ref(props.modelValue);
const currentUrl = ref('');
const savedQuery = ref('');
let reloadTimeout = null;

const emit = defineEmits(['update:modelValue', 'search', 'focus', 'blur-xs']);

onMounted(() => {
    currentUrl.value = window.location.href.replace(/\?.*/, '');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    if (urlParams.has(props.keyword)) {
        savedQuery.value = urlParams.get(props.keyword);
        query.value = savedQuery.value;
        console.log('from URL search', savedQuery.value)
    }
    emit('update:modelValue', query.value);
    document.addEventListener('keydown', handleKeyDown);
});

const cambiado = ref(false)


watch(() => props.modelValue, (v) => {
    query.value = v
})

// si hay algun cambio en los argumentos de búsqueda
watch(() => props.arguments, (value) => cambiado.value = true, { deep: true })

watch(query, (value) => emit('update:modelValue', value))

const submitting = ref(false)

const submit = () => {
    submitting.value = true
    var args = {}
    args[props.keyword] = query.value
    if (typeof props.arguments === 'object')
        args = { ...props.arguments, ...args }
    cambiado.value = false
    console.log('router.get args', args)
    router.get(currentUrl.value, args, { preserveScroll: true})
    emit('search', query.value);
};

const clearInput = () => {
    query.value = '';
    emit('update:modelValue', query.value);

    if (reloadTimeout) {
        clearTimeout(reloadTimeout);
    }

    if (savedQuery.value)
        reloadTimeout = setTimeout(() => {
            router.get(currentUrl.value);
        }, 1000);
};

const handleKeyDown = (event) => {
    if (event.key === 'Escape') {
        clearInput();
    }
};


</script>
