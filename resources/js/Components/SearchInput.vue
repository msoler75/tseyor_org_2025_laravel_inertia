<template>
    <div class="flex items-center gap-3">


        <button v-if="filtro" type="button" @click="clearInput" class="btn">
            Limpiar
        </button>


        <button v-if="filtro" type="submit" @click.prevent="submit" class="btn btn-primary" :disabled="filtro == filtrado">
            Buscar
        </button>

        <div class="flex items-center relative" :style="{ 'max-width': maxWidth + 'px' }">
            <Icon icon="ph:magnifying-glass-light" class="absolute z-10 right-2" />
            <form @submit.prevent="submit">
                <input
                    class="pr-8 focus:bg-base-100 relative bg-transparent border-transparent shadow-none px-6 py-3 focus:shadow-outline
                    text-right w-full focus:rounded-md"
                    :class="filtro?'border-0 border-b border-gray-700 focus:border-b':''"
                    @keydown.Esc="clearInput" autocomplete="off" type="text" name="buscar" :placeholder="placeholder"
                    v-model="filtro" />
            </form>
        </div>
    </div>
</template>

<script setup>
import { Icon } from '@iconify/vue';
import { router } from '@inertiajs/vue3';
import { ref, onMounted, defineEmits } from 'vue';

const maxWidth = ref(200);
const placeholder = ref('Buscar...');
const filtro = ref('');
const currentUrl = ref('');
const filtrado = ref('');
let reloadTimeout = null;

const emit = defineEmits(['update', 'search']);

onMounted(() => {
    currentUrl.value = window.location.href.replace(/\?.*/, '');
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    filtrado.value = urlParams.get('buscar');
    filtro.value = filtrado.value;
    emit('update', filtrado.value);
    document.addEventListener('keydown', handleKeyDown);
});

const submit = () => {
    router.get(currentUrl.value, { buscar: filtro.value });
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
