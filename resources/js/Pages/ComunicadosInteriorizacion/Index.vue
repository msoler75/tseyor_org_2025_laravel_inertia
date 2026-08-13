<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between mb-20">
            <Back :href="route('equipo', $page.props.equipo_interiorizacion_id)">Interiorización</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="comunicado-interiorizacion" necesita="administrar contenidos" />
            </div>
        </div>

        <div class="w-full flex flex-wrap gap-7 items-center justify-between mb-16">
            <TitleInfo title="Comunicados de Interiorización" classes="!mb-0">
                <div>Comunicados restringidos para miembros del grupo de interiorización.
                    <div>Contenido de nivel 1 y nivel 2, organizados por ciclos.</div>
                </div>
            </TitleInfo>
        </div>

        <div
            class="w-fit rounded-lg border border-warning/30 bg-warning/10 flex items-center gap-3 px-4 py-2 mb-8">
            <Icon icon="ph:lock-key-duotone" class="text-xl text-warning shrink-0 opacity-80" />
            <p class="font-display text-sm font-bold uppercase tracking-wider text-warning">Zona solo para iniciados en los talleres de interiorización</p>
        </div>

        <div class="flex flex-wrap justify-between items-center my-4 gap-x-3 gap-y-3">
            <div class="relative">
            <select v-model="busqueda.orden" class="filtro-solid">
                <option value="relevancia" v-if="query">Relevancia</option>
                <option value="recientes">Recientes primero</option>
                <option value="cronologico">Cronológico</option>
            </select>
            <Icon icon="ph:caret-down-duotone" class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-primary text-base" />
            </div>

            <div class="relative">
            <select v-model="busqueda.nivel" class="filtro-solid">
                <option value="todos">Nivel 1 y 2</option>
                <option value="1">Nivel 1</option>
                <option value="2">Nivel 2</option>
            </select>
            <Icon icon="ph:caret-down-duotone" class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-primary text-base" />
            </div>

            <div class="relative">
            <select v-model="busqueda.ciclo" class="filtro-solid">
                <option value="todos">Todos los ciclos</option>
                <option value="1">Ciclo 1</option>
                <option value="2">Ciclo 2</option>
                <option v-for="c of ciclos" :key="c" :value="c">{{ c }}</option>
            </select>
            <Icon icon="ph:caret-down-duotone" class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-primary text-base" />
            </div>

            <div class="relative">
            <select v-model="busqueda.ano" class="filtro-solid">
                <option value="todos">Cualquier año</option>
                <option v-for="año of añosBusqueda" :key="año" :value="año">{{ año }}</option>
            </select>
            <Icon icon="ph:caret-down-duotone" class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-primary text-base" />
            </div>

            <button v-if="hayFiltrosActivos" type="button" @click="limpiarFiltros"
                class="btn btn-sm rounded-md border border-base-content/25 bg-base-100 text-base-content/70 hover:border-error hover:text-error transition-colors cursor-pointer shrink-0"
                title="Borrar filtros" aria-label="Borrar filtros">
                <Icon icon="ph:x-circle-duotone" class="text-lg" />
                <span class="text-[0.7rem]">Borrar filtros</span>
            </button>

            <div class="flex items-center gap-2 ml-auto">
                <SearchInput class="sel-trans"
                    auto-width
                    v-model="query" @focus="focusQuery"
                    placeholder="Buscar en comunicados..."
                    @search="buscando = true" @blur="blurQuery" />
            </div>
        </div>

        </PageHeader>

        <PageWide>
            <ScrollToHere class="mb-12">

                <div class="flex justify-between items-center my-1">
                    <SearchResultsHeader :results="listado" :arguments="busqueda"
                        v-show="!buscando && (filtrado || (listado.data?.length && listado.data[0].extractos))"
                        :class="listado.data?.length == 0 ? 'mb-64' : ''" :valid-search="busquedaValida" />
                </div>

                <GridAppear
                    v-if="listado.data && listado.data?.length > 0"
                    class="gap-4 min-h-[30vh]" col-width="20rem">
                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :title="contenido.titulo"
                        :image="contenido.imagen" image-class="h-80"
                        :href="route('comunicado-interiorizacion', contenido.slug) + resultadoQueryBusqueda"
                        :description="contenido.descripcion" :date="contenido.published_at" imageLeft
                        class="max-h-[14rem] overflow-hidden">
                        <template #footer>
                            <div class="flex gap-2 text-xs opacity-70">
                                <span class="badge badge-sm">{{ contenido.nivel == 1 ? 'N1' : 'N2' }}</span>
                                <span>{{ contenido.ciclo }}</span>
                            </div>
                        </template>
                    </CardContent>
                </GridAppear>

                <div v-else>
                    <div v-if="buscando"
                        class="mt-12 p-8 pb-64 flex gap-4 text-xl items-center min-h-[30vh]">
                        <Spinner /> Buscando ...
                    </div>
                    <div v-else-if="listado.data?.length === 0"
                        class="mt-12 p-8 min-h-[30vh] flex flex-col items-center justify-center gap-3 text-center">
                        <Icon icon="tabler:zoom-cancel" class="text-5xl opacity-30" />
                        <p class="text-base-content/70">No se encontraron comunicados con los filtros seleccionados.</p>
                    </div>
                    <div v-else class="flex flex-col gap-5 min-h-[30vh]">
                        <div v-for="(comunicado, index) of listado.data" :key="comunicado.slug"
                            class="card overflow-hidden shadow-2xs border border-black border-opacity-[0.1] flex flex-col gap-1 bg-info dark:bg-black/10">
                            <div class="px-3 pt-1 text-lg font-bold flex items-center gap-5 justify-between">
                                <Link :href="route('comunicado-interiorizacion', comunicado.slug) + resultadoQueryBusqueda"
                                    class="hover:underline py-2" v-html="comunicado.titulo" />
                                <div class="ml-auto text-sm px-3">{{ comunicado.fecha_comunicado }}</div>
                                <Icon v-show="!extractos_colapsado[index]" icon="ph:caret-up-duotone"
                                    class="text-xl cursor-pointer" @click="extractos_colapsado[index] = true" />
                                <Icon v-show="extractos_colapsado[index]" icon="ph:caret-down-duotone"
                                    class="text-xl cursor-pointer" @click="extractos_colapsado[index] = false" />
                            </div>
                            <div class="bg-base-100 px-5 py-3 max-h-[300px] overflow-y-auto divide-y divide-dashed"
                                v-show="!extractos_colapsado[index]">
                                <div v-if="!comunicado.extractos?.length" class="opacity-75">
                                    <em>No hay resultados relevantes.</em>
                                </div>
                                <div v-for="extracto, idx of comunicado.extractos" :key="idx" class="py-3">
                                    <div v-html="extracto" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <pagination
                    v-if="!buscando && listado.data?.length && listado.data[0].extractos"
                    class="mt-6" :links="listado.links" />

        </ScrollToHere>
        </PageWide>
    </Page>
</template>

<script setup>
const VISTA_LISTADO = 'Listado'

const props = defineProps({
    nivel: {},
    ciclo: {},
    ano: {},
    orden: {},
    filtrado: {},
    listado: {},
    ciclos: { type: Array, default: () => [] },
    esIniciado: { type: Boolean, default: false },
    busquedaValida: Boolean
});

const añoActual = new Date().getFullYear()

const añosBusqueda = []
for (var i = 2004; i <= añoActual; i++)
    añosBusqueda.push(i)

const query = ref("")
const resultadoQueryBusqueda = computed(() => {
    if (props.filtrado) {
        return `?resaltar=${props.filtrado}`
    }
    return ''
})

const extractos_colapsado = ref({})

const busqueda = ref({
    nivel: props.nivel || 'todos',
    ciclo: props.ciclo || 'todos',
    ano: props.ano || 'todos',
    orden: props.orden || 'recientes'
})

function focusQuery() {}
function blurQuery() {}

const buscando = ref(false)

const hayFiltrosActivos = computed(() =>
    busqueda.value.nivel !== 'todos' ||
    busqueda.value.ciclo !== 'todos' ||
    busqueda.value.ano !== 'todos' ||
    busqueda.value.orden !== 'recientes'
)

function limpiarFiltros() {
    busqueda.value.nivel = 'todos'
    busqueda.value.ciclo = 'todos'
    busqueda.value.ano = 'todos'
    busqueda.value.orden = 'recientes'
    aplicarBusqueda()
}

function aplicarBusqueda() {
    if (typeof window == 'undefined') return
    const currentUrl = window.location.href.replace(/\?.*/, '')

    var args = {}
    if (query.value)
        args.buscar = query.value

    // Enviar siempre los cuatro filtros para que el servidor guarde el estado
    // completo (incluidos los "todos"/"recientes" cuando se resetean)
    args.nivel = busqueda.value.nivel
    args.ciclo = busqueda.value.ciclo
    args.ano = busqueda.value.ano
    args.orden = busqueda.value.orden

    buscando.value = true
    router.get(currentUrl, args)
}

watch(busqueda, aplicarBusqueda, { deep: true })
</script>

<style scoped>
@reference "../../../css/app.css";

/* Filtro sólido con color de acento primario */
.filtro-solid {
    @apply appearance-none bg-none rounded-md border-0 bg-primary/15 text-primary font-semibold pl-3 pr-7 py-1.5 text-sm cursor-pointer;
}
</style>
