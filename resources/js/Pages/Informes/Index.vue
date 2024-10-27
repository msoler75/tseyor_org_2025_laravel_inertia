<template>
    <div class="container py-12 mx-auto">

        <div class="flex justify-between items-center mb-20">
            <Back v-if="equipo && equipo.slug" :href="route('equipo', equipo.slug)">{{ equipo.nombre }}</Back>
            <Back v-else :href="route('equipos')">Equipos</Back>
            <Link v-if="equipo" :href="route('informes')" class="flex h-fit gap-2 text-sm items-center hover:underline">
            Informes de todos los equipos
            <Icon icon="ph:arrow-right" />
            </Link>
            <AdminLinks modelo="informe" :necesita="['administrar equipos', 'coordinar equipo']" />
        </div>

        <template v-if="equipo">
            <h2>Informes de {{ equipo.nombre }} {{ equipo.oculto ? '(PRIVADO)' : '' }}</h2>
        </template>
        <template v-else>
            <h1>Informes</h1>
            <p>Informes de trabajo de los equipos Tseyor.</p>
        </template>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>


        <div class="w-full flex gap-7 lg:gap-10 flex-wrap md:flex-nowrap">

            <div
                class="gap-3 xl:gap-0 w-full md:w-[21ch] card bg-base-100 shadow flex-wrap flex-row xl:flex-col p-5 lg:p-10 xl:p-5 self-baseline md:sticky md:top-20">

                <Link
                    :href="(equipo ? route('equipo.informes', equipo.slug) : route('informes')) + (filtrado ? `?buscar=${filtrado}` : '')"
                    class="py-2 hover:text-primary transition-colors duration-250"
                    :class="!filtrado && !categoriaActiva ? 'text-primary font-bold' : ''">
                <span class="capitalize">Novedades</span>
                </Link>


                <div v-for="categoria of categorias" :key="categoria.nombre" class="flex"
                    :class="categoriaActiva == (categoria.valor || categoria.nombre) ? 'text-primary font-bold' : ''">
                    <Link
                        :href="(equipo ? route('equipo.informes', equipo.slug) : route('informes')) + `?categoria=${categoria.valor || categoria.nombre}` + (filtrado ? `&buscar=${filtrado}` : '')"
                        class="py-2 hover:text-primary transition-colors duration-250">
                    <span class="capitalize">{{ categoria.nombre }}</span>
                    <small> ({{ categoria.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="w-full flex-grow card shadow bg-base-100 px-5 py-7">

                <SearchResultsHeader :results="listado" :category="categoriaActiva" />

                <GridAppear class="gap-2 py-4" col-width="24rem">
                    <Link v-for="informe in listado.data" :key="informe.id" :href="route('informe', informe.id)"
                        class="group hover:text-primary transition-color duration-200 px-5 py-2 h-full flex flex-row items-baseline gap-3 hover:bg-base-200/40 rounded-xl w-full">
                    <Icon icon="ph:dot-fill" class="flex-shrink-0" />
                    <div class="w-full">
                        <div v-html="informe.titulo + (informe.visibilidad == 'B' ? ' (borrador)' : '') + (!equipo && informe.equipo_oculto ? ' (privado)' : '')"
                            class="break-all capitalize lowercase font-bold" />
                        <div v-if="filtrado" v-html="informe.descripcion" class="mt-3" />
                        <div class="flex gap-3 items-center mt-4 w-full">
                            <span v-if="!categoriaActiva || categoriaActiva == '_'" class="badge badge-primary text-xs">{{
                                informe.categoria }}</span>
                            <Link v-if="!equipo" class="badge text-xs" :href="route('equipo', informe.slug_equipo)">{{
                            informe.nombre_equipo }}</Link>
                            <TimeAgo
                                class="text-xs ml-auto opacity-50 group-hover:opacity-100 transition-opacity duration-200"
                                :date="informe.updated_at" />
                        </div>
                    </div>
                    </Link>
                </GridAppear>

                <pagination class="mt-6" :links="listado.links" />

            </div>
        </div>
    </div>
</template>


<script setup>


const props = defineProps({
    categoriaActiva: { default: () => '' },
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
    categorias: {
        default: () => []
    },
    equipo: { type: Object, required: false }
});

const listado = ref(props.listado);
// const categorias = ref(props.categorias)

const total = computed(() => {
    var n = 0
    for (var c of props.categorias)
        n += c.valor == '_' ? 0 : c.total
    return n
})
</script>
