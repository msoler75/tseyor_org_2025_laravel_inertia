<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between mb-20">
            <Back :href="route('equipo', 'iniciados-interiorizacion')">Interiorización</Back>
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

        <div class="flex flex-wrap justify-between items-center my-4 gap-x-9 gap-y-7">
            <select v-model="busqueda.orden" class="sel-trans">
                <option value="relevancia" v-if="query">Relevancia</option>
                <option value="recientes">Recientes primero</option>
                <option value="cronologico">Cronológico</option>
            </select>

            <select v-model="busqueda.nivel" class="sel-trans">
                <option value="todos">Todos los niveles</option>
                <option value="1">Nivel 1</option>
                <option value="2">Nivel 2</option>
            </select>

            <select v-model="busqueda.ciclo" class="sel-trans">
                <option value="todos">Todos los ciclos</option>
                <option v-for="c of ciclos" :key="c" :value="c">{{ c }}</option>
            </select>

            <select v-model="busqueda.ano" class="sel-trans">
                <option value="todos">Cualquier año</option>
                <option v-for="año of añosBusqueda" :key="año" :value="año">{{ año }}</option>
            </select>

            <SearchInput :arguments="busqueda" class="ml-auto sel-trans"
                auto-width
                v-model="query" @focus="focusQuery"
                placeholder="Buscar en comunicados de interiorización..."
                @search="buscando = true" @blur="blurQuery" />
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
                    class="gap-4" col-width="20rem">
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
                        class="mt-12 p-8 pb-64 flex gap-4 text-xl items-center">
                        <Spinner /> Buscando ...
                    </div>
                    <div v-else class="flex flex-col gap-5">
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

watch(busqueda, () => {
    if(typeof window == 'undefined') return
    const currentUrl = window.location.href.replace(/\?.*/, '')

    var args = {}
    if (query.value)
        args.buscar = query.value

    if (busqueda.value.nivel != 'todos')
        args.nivel = busqueda.value.nivel

    if (busqueda.value.ciclo != 'todos')
        args.ciclo = busqueda.value.ciclo

    if (busqueda.value.ano != 'todos')
        args.ano = busqueda.value.ano

    if (busqueda.value.orden != 'recientes')
        args.orden = busqueda.value.orden

    buscando.value = true
    router.get(currentUrl, args)
}, { deep: true })
</script>

<style scoped>
@reference "../../../css/app.css";

.sel-trans {
    @apply bg-base-200 border-transparent border-b-gray-500/50;
}
</style>
