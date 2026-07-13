<template>
    <div class="flex flex-wrap items-center gap-3">
        <slot></slot>

        <div class="flex items-center ml-auto w-full">
            <form @submit.prevent="submit" class="w-full relative flex">
            <div class="flex w-full rounded-xl overflow-hidden border border-gray-500/50 relative">
                <div class="relative flex-1">
                    <input class="search-input focus:bg-base-100 bg-transparent px-6 py-3 border-0 outline-none
                        text-left
                        placeholder:text-gray-500 dark:placeholder:text-gray-400
                        w-full"
                        :class="inputClass + (query ? ' pr-10' : '')"
                        @keydown.Esc="clearInput" autocomplete="off" type="text" :name="keyword" :placeholder="placeholder"
                        @focus="onFocus()" @blur="onBlur()" v-model="query" />
                    <button v-if="query" type="button" @click="clearInput()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-orange-500 hover:opacity-80 cursor-pointer"
                        tabindex="-1" aria-label="Limpiar búsqueda" title="Limpiar búsqueda">
                        <Icon icon="jam:rubber" class="text-lg" />
                    </button>
                </div>

                <button v-if="query" type="submit" @click.prevent="submit"
                    class="btn btn-md btn-primary rounded-none border-0 shadow-none h-full"
                    :disabled="submitting">
                    <Spinner v-show="submitting" class="text-xl" />
                    <Icon v-show="!submitting" class="text-xl" icon="ph:magnifying-glass-bold" />
                </button>
                <Icon v-else icon="ph:magnifying-glass-bold"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-base-content/40 pointer-events-none text-xl" />
            </div>
            </form>
        </div>
    </div>
</template>

<script setup>

import { router } from '@inertiajs/vue3';
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js'

const { trackSearch } = useGoogleAnalytics()

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
        default: ''
    },
    arguments: {},
    doSearch: { type: Boolean, default: true },
    reloadOnClear: {type: Boolean, default: true} // al limpiar, se recarga la página
})

const query = ref(props.modelValue);
const currentUrl = ref('');
const savedQuery = ref('');
const focused = ref(false)
// let reloadTimeout = null;

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

    // Tracking de búsqueda con contexto de página
    const pageContext = getPageContext()
    trackSearch(query.value, pageContext)

    console.log('router.get args', args)
    router.get(currentUrl.value, args, { preserveScroll: true})
    emit('search', query.value);
};

const clearInput = () => {
    query.value = '';
    emit('update:modelValue', query.value);

    if(!props.reloadOnClear) return

    router.get(currentUrl.value);

    /*if (reloadTimeout) {
        clearTimeout(reloadTimeout);
    }

    if (savedQuery.value)
        reloadTimeout = setTimeout(() => {
        }, 1);*/
};

const handleKeyDown = (event) => {
    if (event.key === 'Escape') {
        clearInput();
    }
};

function onFocus() {
    emit('focus')
    focused.value = true
}

function onBlur() {
    emit('blur-xs')
    focused.value = false
}

const getPageContext = () => {
    const path = window.location.pathname.toLowerCase()

    if (path.includes('/contactos')) return 'contactos'
    if (path.includes('/eventos')) return 'eventos'
    if (path.includes('/comunicados')) return 'comunicados'
    if (path.includes('/noticias')) return 'noticias'
    if (path.includes('/audios')) return 'audios'
    if (path.includes('/videos')) return 'videos'
    if (path.includes('/psicografias')) return 'psicografias'
    if (path.includes('/entradas')) return 'entradas'
    if (path.includes('/boletines')) return 'boletines'
    if (path.includes('/biblioteca')) return 'biblioteca'
    if (path === '/' || path === '') return 'inicio'

    // Si no coincide con ninguna sección conocida, usar el primer segmento de la URL
    const segments = path.split('/').filter(segment => segment !== '')
    return segments.length > 0 ? segments[0] : 'general'
}

</script>
