<template>
    <div class="sticky top-16 flex justify-center w-full z-30"
        :class="[
            divSelectClass,
            selectBreakpoint == 'lg' ? 'lg:hidden' : selectBreakpoint == 'md' ? 'md:hidden' : selectBreakpoint == 'sm' ? 'sm:hidden' : 'xs:hidden',
        ]">
        <select class="mx-auto categorias max-w-full" @change="onCategoria" :value="selectHrefActual"
            :class="selectClass">
            <option v-for="categoria of categorias" :key="categoria.nombre" :value="categoria.href"
                :selected="actual == categoria.valor">{{ ucFirst(categoria.nombre) }}
                <small v-if="categoria.total > 0">({{ categoria.total }})</small>
            </option>
        </select>
    </div>
    <div class="hidden card bg-base-100 shadow-2xs self-baseline flex-wrap flex-row p-5 lg:p-10 gap-4 sticky top-16 z-30 overflow-x-auto"
        :class="[
            divClass,
            columnaBreakpoint == '2xl' ? '2xl:flex-col' : columnaBreakpoint == 'xl' ? 'xl:flex-col' : columnaBreakpoint == 'lg' ? 'lg:flex-col' : columnaBreakpoint == 'md' ? 'md:flex-col' : '',
            selectBreakpoint == 'lg' ? 'lg:flex' : selectBreakpoint == 'md' ? 'md:flex' : selectBreakpoint == 'sm' ? 'sm:flex' : 'xs:flex'
            ]">
            <div v-if="title" class="hidden md:inline text-xl font-bold md:mb-4">{{ title }}</div>
        <Link v-for="categoria of categorias" :key="categoria.nombre" :href="categoria.href"
            :class="actual.toLowerCase() == categoria.valor.toLowerCase() ? 'text-primary font-bold' : 'hover:text-secondary'" @click="clickCategoria(categoria.valor)"
            :only="only" :preserve-state="preserveState" :preserve-scroll="preserveScroll" :replace="replace"
            @finish="emit('finish')">

        <span>{{ ucFirst(categoria.nombre) }}</span>
        <small v-if="counters && categoria.total > 0"> ({{ categoria.total }})</small>
        </Link>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ucFirst } from '@/composables/textutils'

const page = usePage()

const props = defineProps({
    title: { type: String, default: '' },
    categorias: Array,
    url: String,
    novedades: { type: [Boolean, String], default: true },
    resultados: {type: [Boolean,String], default: false},
    selectBreakpoint: { type: String, default: 'sm' }, // en qué punto o breakpoint se muestra el select
    columnaBreakpoint: { type: String, default: 'md' }, // en qué punto o breakpoint se muestra en modo columna
    divSelectClass: String,
    selectClass: String,
    divClass: String,
    parametro: { type: String, default: 'categoria' },
    valor: { type: String, default: 'valor' }, // indica el campo donde está el valor
    counters: {type: Boolean, default: true},
    // parametros de link
    preserveScroll: {
        type: [Boolean, Function],
        default: true /* ESTA ES LA DIFERENCIA CON EL LINK DE INERTIA */
    },
    preserveState: {
        type: [Boolean, Function, null],
        default: null
    },
    replace: {
        type: Boolean,
        default: false
    },
    only: {
        type: Array,
        default: () => []
    },

})

// ha de emitir un evento click, con el valor

const emit = defineEmits(['click', 'finish'])

const seleccionado = ref("")

const actual = computed(() => {
    if(props.resultados)
        return 'Resultados';
    if (seleccionado.value)
        return seleccionado.value;
    return obtenerValorDeUrl(page.url)
})


function obtenerValorDeUrl(url) {
    const search = url.split('?')
    if (search.length == 1)
        return props.novedades?'Novedades':''
    // descompone los parámetros de la url
    const params = new URLSearchParams(search[1])
    // si existe el parámetro categoria
    if (params.has(props.parametro)) {
        return params.get(props.parametro)
    }
    return props.novedades?'Novedades':'Todos'
}


const novedadesLabel = computed(() => {
    if (props.novedades === true)
        return "Novedades"
    return props.novedades
})

const resultadosLabel = computed(() => {
    if (props.resultados === true)
        return "Resultados"
    return props.resultados
})

const categorias = computed(() => {

    const items = []

    if (props.novedades)
        items.push({
            nombre: novedadesLabel.value, href: props.url,
            valor: 'Novedades'
        })

    for (const categoria of props.categorias) {
        const cvalor = categoria.nombre.match(/Tod.s/i) ? (props.novedades?'_':'') : categoria[props.valor] ? categoria[props.valor] : categoria.nombre
        items.push({
            nombre: categoria.nombre,
            href: props.url + (cvalor?'?' + props.parametro + '=' + cvalor : ''),
            total: categoria.total,
            valor: cvalor
            // seleccionada: props.actual == categoria.nombre
        })
    }

    if(props.resultados )
    items.push({
            nombre: resultadosLabel.value, href: props.url,
            valor: 'Resultados'
        })


    return items
})

function onCategoria(event) {
    console.log('onCategoria', event)
    // const elem = document.querySelector('select.categorias')
    // console.log(elem.value)
    const url = event.target.value

    router.visit(url, {
        preserveScroll: props.preserveScroll,
        preserveState: props.preserveState, replace: props.replace,
        only: props.only,
        onFinish: visit => {
            console.log('Categorias.onFinish')
            emit('finish')
        },
    })
    const valor = obtenerValorDeUrl(url)
    seleccionado.value = valor
    emit('click', valor)
}

function clickCategoria(valor) {
    seleccionado.value = valor
    emit('click', valor)
}


onMounted(() => {
    seleccionado.value = obtenerValorDeUrl(page.url)
    console.log('Categorias. onMounted', seleccionado.value)
})

const selectHrefActual = computed(() => {
    const c = categorias.value.find(x => x.valor.toLowerCase() == seleccionado.value.toLowerCase());
    return c?.href
})
</script>
