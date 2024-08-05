<template>
    <div class="sticky top-16 flex justify-center w-full z-30 sm:hidden">
        <select class="mx-auto categorias " @change="onCategoria">
            <option v-for="categoria of categorias" :key="categoria.nombre" :value="categoria.href"
                :selected="categoria.seleccionada">{{ categoria.nombre }}
                <small v-if="categoria.total > 0">({{ categoria.total }})</small>
            </option>
        </select>
    </div>
    <div
        class="hidden sm:flex card bg-base-100 shadow self-baseline flex-wrap flex-row md:flex-col p-5 lg:p-10 gap-4 sticky top-16 z-30 overflow-x-auto">
        <Link v-for="categoria of categorias" :key="categoria.nombre" :href="categoria.href"
            :class="categoria.seleccionada ? 'text-primary font-bold' : ''">
        <span class="capitalize">{{ categoria.nombre }}</span>
        <small v-if="categoria.total > 0">({{ categoria.total }})</small>
        </Link>
    </div>
</template>

<script setup>

const props = defineProps({
    categorias: Array
})


function onCategoria(event) {
    // console.log('onCategoria', event)
    const elem = document.querySelector('select.categorias')
    // console.log(elem.value)
    router.visit(elem.value, { replace: true, preserveScroll: true })
}

</script>