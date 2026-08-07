<template>
    <div class="flex flex-wrap items-center gap-3">
        <slot></slot>

        <div class="flex items-center"
            :class="wrapperClass">
            <form @submit.prevent="submit" class="relative flex"
                :class="wrapperClass">
            <div class="flex w-full rounded-xl overflow-hidden border border-gray-500/50 relative">
                <div class="relative flex-1">
                    <input class="search-input focus:bg-base-100 bg-transparent px-6 py-3 border-0 outline-none
                        text-left
                        placeholder:text-gray-500 dark:placeholder:text-gray-400
                        w-full"
                        :class="inputClass + (query ? ' pr-10' : '')"
                        :style="autoWidth ? { width: placeholderWidth + 'ch' } : {}"
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

const nav = useNav()

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
    reloadOnClear: {type: Boolean, default: true},
    compact: { type: Boolean, default: false },
    autoWidth: { type: Boolean, default: false },
})

const CHAR_WIDTH = {
    ' ': 0.35, '.': 0.4, ',': 0.4, ':': 0.4, ';': 0.4, '!': 0.45, '\'': 0.35, '"': 0.5,
    'i': 0.45, 'l': 0.45, 'I': 0.45, '|': 0.35, '¡': 0.5, '¿': 0.95,
    't': 0.6, 'f': 0.6, 'j': 0.6, 'r': 0.65,
    'a': 0.95, 'c': 0.95, 'e': 0.95, 'n': 1.0, 'o': 1.0, 's': 0.9, 'u': 1.0, 'v': 0.95,
    'x': 0.95, 'z': 0.85,
    'b': 1.0, 'd': 1.0, 'g': 1.0, 'h': 1.0, 'k': 1.0, 'p': 1.0, 'q': 1.0, 'y': 0.95,
    '0': 1.0, '1': 0.7, '2': 0.95, '3': 0.95, '4': 0.95, '5': 0.95, '6': 0.95, '7': 0.85, '8': 0.95, '9': 0.95,
    'm': 1.4, 'w': 1.4, 'ñ': 1.0, 'ç': 0.95,
    'A': 1.05, 'B': 1.0, 'C': 1.1, 'D': 1.1, 'E': 0.95, 'F': 0.9, 'G': 1.15, 'H': 1.1,
    'J': 0.85, 'K': 1.0, 'L': 0.9, 'N': 1.05, 'O': 1.15, 'P': 0.95, 'Q': 1.15, 'R': 1.0,
    'S': 0.95, 'T': 0.95, 'U': 1.05, 'V': 1.0, 'X': 1.0, 'Y': 0.95, 'Z': 0.95,
    'M': 1.45, 'W': 1.6, '@': 1.2, '#': 0.95, 'Ñ': 1.05, 'Ç': 1.1,
}

function textWidth(str) {
    let w = 0
    for (const c of str) {
        w += CHAR_WIDTH[c] ?? 0.85
    }
    return Math.ceil(w * 1.03 + 7)
}

const placeholderWidth = computed(() => textWidth(props.placeholder))
const wrapperClass = computed(() => props.compact ? '' : (props.autoWidth ? '' : 'w-full'))

const query = ref(props.modelValue);
const currentUrl = ref('');
const savedQuery = ref('');
const focused = ref(false)
// let reloadTimeout = null;

const emit = defineEmits(['update:modelValue', 'search', 'focus', 'blur-xs', 'click', 'finish']);

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
    nav.navigating = true
    emit('click')
    router.get(currentUrl.value, args, {
        preserveScroll: true,
        onFinish: () => emit('finish'),
    })
    emit('search', query.value);
};

const clearInput = () => {
    query.value = '';
    emit('update:modelValue', query.value);

    if(!props.reloadOnClear) return

    emit('click')
    nav.navigating = true
    router.get(currentUrl.value, {}, {
        onFinish: () => emit('finish'),
    });

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
