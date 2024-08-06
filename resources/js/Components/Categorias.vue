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
        :class="(columnaBreakpoint == '2xl' ? '2xl:flex-col' : columnaBreakpoint == 'xl' ? 'xl:flex-col' : columnaBreakpoint == 'lg' ? 'lg:flex-col' : columnaBreakpoint == 'md' ? 'md:flex-col' : '') + ' ' +
            (selectBreakpoint == 'lg' ? 'lg:flex' : selectBreakpoint == 'md' ? 'md:flex' : selectBreakpoint == 'sm' ? 'sm:flex' : 'xs:flex')">
        <Link v-for="categoria of categorias" :key="categoria.nombre" :href="categoria.href"
            :class="actual == categoria.valor ? 'text-primary font-bold' : ''" @click="seleccionado = categoria.valor">

        <span>{{ ucFirst(categoria.nombre) }}</span>
        <small v-if="categoria.total > 0"> ({{ categoria.total }})</small>
        </Link>
    </div>
</template>

<script setup>

import { ucFirst } from '@/composables/textutils'

const page = usePage()

const props = defineProps({
    categorias: Array,
    url: String,
    novedades: { type: Boolean, default: true },
    selectBreakpoint: { type: String, default: 'sm' }, // en qué punto o breakpoint se muestra el select
    columnaBreakpoint: { type: String, default: 'md' }, // en qué punto o breakpoint se muestra en modo columna
    selectClass: String
})

const seleccionado = ref("")

const actual = computed(() => {
    if (seleccionado.value)
        return seleccionado.value;
    const search = page.url.split('?')
    if (search.length == 1)
        return 'Novedades'
    // descompone los parámetros de la url
    const params = new URLSearchParams(search[1])
    // si existe el parámetro categoria
    if (params.has('categoria')) {
        return params.get('categoria')
    }
    return 'Novedades'
})

const categorias = computed(() => {

    const items = []
    if (props.novedades)
        items.push({
            nombre: 'Novedades', href: props.url,
            valor: 'Novedades'
        })


    for (const categoria of props.categorias) {
        items.push({
            nombre: categoria.nombre, href: props.url + '?categoria=' + (categoria.nombre.match(/Tod.s/i) ? '_' : categoria.nombre),
            total: categoria.total,
            valor: categoria.nombre.match(/Tod.s/i) ? '_' : categoria.nombre
            // seleccionada: props.actual == categoria.nombre
        })
    }

    return items
})

function onCategoria(event) {
    // console.log('onCategoria', event)
    const elem = document.querySelector('select.categorias')
    // console.log(elem.value)
    router.visit(elem.value, { replace: true, preserveScroll: true })
}

</script>