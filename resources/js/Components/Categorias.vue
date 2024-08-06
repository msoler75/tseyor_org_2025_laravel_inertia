<template>
    <div class="sticky top-16 flex justify-center w-full z-30"
        :class="selectBreakpoint == 'lg' ? 'lg:hidden' : selectBreakpoint == 'md' ? 'md:hidden' : selectBreakpoint == 'sm' ? 'sm:hidden' : 'xs:hidden'">
        <select class="mx-auto categorias max-w-full" @change="onCategoria" :class="selectClass">
            <option v-for="categoria of categorias" :key="categoria.nombre" :value="categoria.href"
                :selected="actual == categoria.valor">{{ ucFirst(categoria.nombre) }}
                <small v-if="categoria.total > 0">({{ categoria.total }})</small>
            </option>
        </select>
    </div>
    <div class="hidden card bg-base-100 shadow self-baseline flex-wrap flex-row p-5 lg:p-10 gap-4 sticky sm:static top-16 z-30 overflow-x-auto"
        :class="divClass + ' ' + (columnaBreakpoint == '2xl' ? '2xl:flex-col' : columnaBreakpoint == 'xl' ? 'xl:flex-col' : columnaBreakpoint == 'lg' ? 'lg:flex-col' : columnaBreakpoint == 'md' ? 'md:flex-col' : '') + ' ' +
            (selectBreakpoint == 'lg' ? 'lg:flex' : selectBreakpoint == 'md' ? 'md:flex' : selectBreakpoint == 'sm' ? 'sm:flex' : 'xs:flex')">
        <Link v-for="categoria of categorias" :key="categoria.nombre" :href="categoria.href"
            :class="actual == categoria.valor ? 'text-primary font-bold' : ''" @click="clickCategoria(categoria.valor)"
            :only="only" :preserve-state="preserveState" :preserve-scroll="preserveScroll" :replace="replace"
            @finish="emit('finish')">

        <span>{{ ucFirst(categoria.nombre) }}</span>
        <small v-if="categoria.total > 0"> ({{ categoria.total }})</small>
        </Link>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ucFirst } from '@/composables/textutils'

const page = usePage()

const props = defineProps({
    categorias: Array,
    url: String,
    novedades: { type: Boolean, default: true },
    selectBreakpoint: { type: String, default: 'sm' }, // en qué punto o breakpoint se muestra el select
    columnaBreakpoint: { type: String, default: 'md' }, // en qué punto o breakpoint se muestra en modo columna
    selectClass: String,
    divClass: String,
    parametro: { type: String, default: 'categoria' },
    valor: { type: String, default: 'valor' }, // indica el campo donde está el valor
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
    if (seleccionado.value)
        return seleccionado.value;
    return obtenerValorDeUrl(page.url)
})

function obtenerValorDeUrl(url) {
    const search = url.split('?')
    if (search.length == 1)
        return 'Novedades'
    // descompone los parámetros de la url
    const params = new URLSearchParams(search[1])
    // si existe el parámetro categoria
    if (params.has(props.parametro)) {
        return params.get(props.parametro)
    }
    return 'Novedades'
}

const categorias = computed(() => {

    const items = []
    if (props.novedades)
        items.push({
            nombre: 'Novedades', href: props.url,
            valor: 'Novedades'
        })


    for (const categoria of props.categorias) {
        const cvalor = categoria.nombre.match(/Tod.s/i) ? '_' : categoria[props.valor] ? categoria[props.valor] : categoria.nombre
        items.push({
            nombre: categoria.nombre,
            href: props.url + '?' + props.parametro + '=' + cvalor,
            total: categoria.total,
            valor: cvalor
            // seleccionada: props.actual == categoria.nombre
        })
    }

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

</script>