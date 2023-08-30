<template>
    <div class="flex items-center gap-3">

        <slot></slot>


        <button v-if="filtro" type="button" @click="clearInput" class="btn border border-gray-500 border-opacity-20">
            Limpiar
        </button>


        <button v-if="filtro" type="submit" @click.prevent="submit" class="btn btn-primary" :disabled="filtro == filtrado && !cambiado">
            Buscar
        </button>

        <div class="flex items-center relative" :style="{ 'max-width': maxWidth + 'px' }">
            <Icon icon="ph:magnifying-glass-light"
                class="absolute z-10 right-2 transform scale-110 -translate-y-[.1rem] text-gray-600" />
            <form @submit.prevent="submit">
                <input class="pr-8 focus:bg-base-100 relative bg-transparent shadow-none px-6 py-3 focus:shadow-outline
                    text-right w-full focus:rounded-md"
                    :class="filtro ? 'border-0 border-b border-gray-700 focus:border-b' : 'border-transparent'"
                    @keydown.Esc="clearInput" autocomplete="off" type="text" :name="keyword" :placeholder="placeholder"
                    @focus="$emit('focus')" @blur="$emit('blur')" v-model="filtro" />
            </form>
        </div>
    </div>
</template>

<script setup>

import { router } from '@inertiajs/vue3';

const props = defineProps({
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
    arguments: {}
})

const maxWidth = ref(200);
const filtro = ref('');
const currentUrl = ref('');
const filtrado = ref('');
let reloadTimeout = null;

const emit = defineEmits(['update', 'search', 'focus', 'blur']);

onMounted(() => {
    currentUrl.value = window.location.href.replace(/\?.*/, '');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    filtrado.value = urlParams.get(props.keyword);
    filtro.value = filtrado.value;
    emit('update', filtrado.value);
    document.addEventListener('keydown', handleKeyDown);
});

const cambiado = ref(false)

// si hay algun cambio en los argumentos de búsqueda
watch(()=>props.arguments, (value) => cambiado.value = true, { deep: true })

const submit = () => {
    var args = {}
    args[props.keyword] = filtro.value
    if (typeof props.arguments === 'object')
        args = { ...props.arguments, ...args }
    cambiado.value = false
    router.get(currentUrl.value, args);
    emit('search');
};

const clearInput = () => {
    filtro.value = '';
    emit('update', '');

    if (reloadTimeout) {
        clearTimeout(reloadTimeout);
    }

    if (filtrado.value)
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
