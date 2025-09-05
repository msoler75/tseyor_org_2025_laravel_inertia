<template>
    <div class="flex items-center gap-3"
    :class="active?'min-w-full sm:min-w-auto':''">
        <slot></slot>

        <div class="flex items-center"
        :class="active?'min-w-full sm:min-w-auto':''"
        >
            <form @submit.prevent="submit" class="w-full relative flex"
            :class="active?'min-w-full sm:min-w-auto':''"
            >
              <div class="absolute top-1/2 z-20 left-2 transform scale-110 -translate-y-[.6rem] text-gray-600"
                v-if="!active">
                <Icon v-show="!submitting" icon="ph:magnifying-glass-bold" />
            </div>


            <div class="join w-full sm:w-fit">
                  <button v-if="active" type="submit" @click.prevent="submit" class="btn btn-md btn-primary
                  rounded-l-xl h-full" :disabled="submitting || (query == savedQuery && !cambiado)">
                    <span class="hidden xs:inline">Buscar</span>
                    <Spinner v-show="submitting" class="text-2xl"/>
                    <Icon v-show="!submitting" class="text-2xl" icon="ph:magnifying-glass-bold" />
                </button>


                <input class="search-input focus:bg-base-100 bg-transparent shadow-none px-6 py-3 focus:shadow-outline
                    text-left rounded-none
                    flex-grow"
                    :class="[
                        active ? 'pl-4' : 'rounded-l-lg border-gray-500/20 pl-8',
                        query?'':'rounded-r-lg',
                        inputClass,
                    ]"
                    @keydown.Esc="clearInput" autocomplete="off" type="text" :name="keyword" :placeholder="placeholder"
                    @focus="onFocus()" @blur="onBlur()" v-model="query" />

                      <button
                      v-if="query"
                                type="button"
                                @click="clearInput()"
                                class="h-full btn text-orange-500 border-l-0 border-1 border-gray-500 text-3xl rounded-r-xl cursor-pointer hover:opacity-100"
                                :class="query?'opacity-80':'opacity-20 pointer-events-none'"
                                tabindex="-1"
                                aria-label="Limpiar búsqueda"
                                title="Limpiar búsqueda"
                            >
                            <Icon icon="jam:rubber" />
                </button>

            </div>
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
        default: ''
    },
    arguments: {},
    doSearch: { type: Boolean, default: true },
    cleanReset: {type: Boolean, default: true} // al limpiar, se recarga la página
})

const query = ref(props.modelValue);
const active = computed(()=>query.value||focused.value)
const currentUrl = ref('');
const savedQuery = ref('');
const focused = ref(false)
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

function onFocus() {
    emit('focus')
    focused.value = true
}

function onBlur() {
    emit('blur-xs')
    focused.value = false
}


</script>
