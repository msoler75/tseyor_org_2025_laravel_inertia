
<template>
    <div class="container py-12 mx-auto">

        <AdminPanel modelo="comunicado" necesita="administrar contenidos" class="mb-3" />

        <div class="w-full flex gap-7 items-center justify-between">

            <h1 class="my-0">Comunicados de Tseyor</h1>

            <Dropdown width="60">

                <template #trigger="data">
                    <div class="btn btn-neutral btn-sm cursor-pointer gap-3" :class="data.open ? 'opacity-50' : ''">
                        <span>{{ selectors.vistaComunicados }}</span>
                        <Icon v-show="!data.open" icon="ph:caret-down-duotone" class="text-xl" />
                        <Icon v-show="data.open" icon="ph:caret-up-duotone" class="text-xl" />
                    </div>
                </template>

                <template #content>
                    <div class="bg-base-100">
                        <div v-for="mode of modes" :key="mode.label"
                            class="flex gap-3 items-center px-4 py-2  hover:bg-base-300 cursor-pointer whitespace-nowrap"
                            @click="selectors.vistaComunicados = mode.label">
                            <span class="w-4">
                                <Icon v-show="selectors.vistaComunicados == mode.label" icon="ph:check" />
                            </span>
                            <Icon :icon="mode.icon" />
                            <span class="transform translate-y-[1px]">{{ mode.label }}</span>
                        </div>
                    </div>
                </template>
            </Dropdown>
        </div>


        <p class="mt-12 mb-16">Aquí puedes encontrar todos los comunicados de Tseyor. Son transcripciones de las
            conversaciones
            interdimensionales mantenidas con los hermanos de las estrellas con la <Referencia>telepatía externa
            </Referencia> a través de nuestro <Referencia>Chac-Mool Puente</Referencia>.</p>

        <!-- <Planets /> -->



        <div class="flex flex-wrap justify-between items-center my-4 gap-x-9 gap-y-7">

            <select v-model="busqueda.orden" class="sel-trans">
                <option value="relevancia" v-if="vistaBusquedaCompleta || query">Relevancia</option>
                <option value="recientes">Recientes primero</option>
                <option value="cronologico">Cronológico</option>
            </select>

            <select v-model="busqueda.categoria" class="sel-trans">
                <option value="todos">Todos los comunicados</option>
                <option v-for="etiqueta, categoria  of categoriasBusqueda" :key="categoria" :value="categoria">{{ etiqueta
                }}</option>
            </select>

            <select v-model="busqueda.ano" class="sel-trans">
                <option value="todos">Cualquier año</option>
                <option v-for="año of añosBusqueda" :key="año" :value="año">{{ año }}</option>
            </select>

            <SearchInput :arguments="busqueda" class="ml-auto sel-trans" v-model="query" @focus="focusQuery" @search="buscando=true"
                @blur="blurQuery" />
        </div>


        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="flex-grow">

                <div v-if="!vistaBusquedaCompleta || completo"
                    class="flex justify-between items-center my-5">

                    <SearchResultsHeader :results="listado" :arguments="busqueda"
                        v-show="!vistaBusquedaCompleta || (vistaBusquedaCompleta && !buscando && (filtrado || (listado.data?.length && listado.data[0].extractos)))"
                        :class="listado.data?.length==0?'mb-64':''"
                        :valid-search="busquedaValida"
                        />

                    </div>


                <GridAppear v-if="(selectors.vistaComunicados == VISTA_TARJETAS) && listado.data && listado.data?.length > 0"
                    class="gap-4" col-width="24rem">

                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :title="contenido.titulo"
                        :image="contenido.imagen" image-class="h-80" :href="route('comunicado', contenido.slug)"
                        :description="contenido.descripcion" :date="contenido.published_at"
                        imageLeft/>

                </GridAppear>


                <table v-else-if="selectors.vistaComunicados == VISTA_LISTADO && listado.data && listado.data?.length > 0"
                    class="table bg-base-100">
                    <thead>
                        <tr class="table-row">
                            <th scope="col" class="table-header">Fecha</th>
                            <!-- <th scope="col" class="table-header">Categoría</th>
                            <th scope="col" class="table-header">Número</th> -->
                            <th scope="col" class="table-header">Título</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <tr v-for="(comunicado, index) of listado.data" :key="comunicado.slug" class="table-row">
                            <td class="table-cell">{{ comunicado.fecha_comunicado }}</td>
                            <!-- <td class="table-cell">{{ comunicado.categoria }}</td>
                            <td class="table-cell">{{ comunicado.numero }}</td> -->
                            <td class="table-cell">
                                <Link :href="route('comunicado', comunicado.slug) + resultadoQueryBusqueda" class="hover:underline py-2"
                                    v-html="comunicado.titulo" />
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-else>
                    <div v-if="vistaBusquedaCompleta && buscando" class="mt-12 p-8 pb-64 flex gap-4 text-xl items-center">
                            <Spinner /> Buscando ...
                    </div>
                    <div v-else-if="vistaBusquedaCompleta && (!completo || !filtrado)" class="card bg-base-100 shadow p-8 w-fit mt-12">
                            <h2>
                                <Icon icon="ph:info-duotone" class="inline mr-4 transform -translate-y-1" />Instrucciones para
                                la búsqueda completa
                            </h2>
                            <p>Ahora puedes realizar una búsqueda completa en los comunicados.</p>
                            <p>Para ello ve a la <div class="inline text-primary cursor-pointer after:content-['↗'] hover:underline" @click.native="focusBuscar">casilla de búsqueda</div>, pon el texto a buscar y pulsa en el botón "BUSCAR".</p>

                    </div>
                    <div v-else class="flex flex-col gap-5">
                        <div v-for="(comunicado, index) of listado.data" :key="comunicado.slug"
                            class="card overflow-hidden shadow border border-black border-opacity-[0.1] flex flex-col gap-1 bg-info dark:bg-black bg-opacity-[0.1] dark:bg-opacity-[0.1]">
                            <!-- <td class="table-cell">{{ comunicado.categoria }}</td>
                                <td class="table-cell">{{ comunicado.numero }}</td> -->
                            <div class="px-3 pt-1 text-lg font-bold flex items-center gap-5 justify-between">
                                <Link :href="route('comunicado', comunicado.slug) + resultadoQueryBusqueda" class="hover:underline py-2"
                                    v-html="comunicado.titulo" />
                                <div class="ml-auto text-sm px-3">{{ comunicado.fecha_comunicado }}</div>
                                <Icon v-show="!extractos_colapsado[index]" icon="ph:caret-up-duotone"
                                    class="text-xl cursor-pointer" @click="extractos_colapsado[index] = true" />
                                <Icon v-show="extractos_colapsado[index]" icon="ph:caret-down-duotone"
                                    class="text-xl cursor-pointer" @click="extractos_colapsado[index] = false" />
                            </div>

                            <div class="bg-base-100 px-5 py-3 max-h-[300px] overflow-y-auto divide-y divide-dashed"
                                v-show="!extractos_colapsado[index]">
                                <div v-if="!comunicado.extractos?.length" class="text-neutral opacity-75">
                                    <em>No se han podido precisar los resultados.</em>
                                </div>
                                <div v-for="extracto, index of comunicado.extractos" :key="index" class="py-3">
                                    <div v-html="extracto" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <pagination
                    v-if="!vistaBusquedaCompleta || (vistaBusquedaCompleta && !buscando && listado.data?.length && listado.data[0].extractos)"
                    class="mt-6" :links="listado.links" />

            </div>


        </div>
    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
import useSelectors  from '@/Stores/selectors'

const VISTA_TARJETAS = 'Vista normal'
const VISTA_LISTADO = 'Listado'
const VISTA_BUSQUEDA_COMPLETA = 'Búsqueda completa'



defineOptions({ layout: AppLayout })
const selectors = useSelectors()

if(!selectors.vistaComunicados)
    selectors.vistaComunicados = VISTA_TARJETAS

const props = defineProps({
    categoria: {},
    ano: {},
    orden: {},
    buscar: {},
    filtrado: {},
    listado: {},
    completo: {},
    busquedaValida: Boolean
});

// Obtener la fecha actual
const añoActual = new Date().getFullYear()

const categoriasBusqueda =
{
    0: 'General',
    1: 'TAP',
    2: 'Doce del Muulasterio',
    3: 'Comunicados Muul'
}


const modes = ref([
    {
        icon: 'ph:squares-four-duotone',
        label: VISTA_TARJETAS
    },
    {
        icon: 'ph:list-dashes-bold',
        label: VISTA_LISTADO
    },
    {
        icon: 'tabler:list-search',
        label: VISTA_BUSQUEDA_COMPLETA
    }])

const añosBusqueda = []
for (var i = 2004; i <= añoActual; i++)
    añosBusqueda.push(i)

const query = ref("")
const resultadoQueryBusqueda = computed(()=>{
    /*if(props.filtrado) {
        return `?busqueda=${props.filtrado}`
    }
    */
   return ''
})

const extractos_colapsado = ref({})

const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
if (urlParams.has('completo')) {
    const ex = urlParams.get('completo')
    console.log('(setup) ex from url?', ex)
    if (ex == 1 || ex == "1") {
        console.log('vistaComunicados = ' + VISTA_BUSQUEDA_COMPLETA)
        selectors.vistaComunicados = VISTA_BUSQUEDA_COMPLETA
        // busqueda.value.completo = 1
    }
}


const vistaBusquedaCompleta = computed(() => selectors.vistaComunicados == VISTA_BUSQUEDA_COMPLETA)
const busqueda = ref({ categoria: props.categoria || 'todos', ano: props.ano || 'todos', orden: props.orden || 'recientes', completo: vistaBusquedaCompleta.value ? 1 : 0 })
console.log('(setup) ex=', busqueda.value.completo)

onMounted(() => {
    watch(query, () => {
        // console.log('query', query.value)
        if(!query.value&&!vistaBusquedaCompleta.value)
            busqueda.value.orden = 'recientes'

    })
})

watch(vistaBusquedaCompleta, (v) => {
    console.log('watched vistaBusquedaCompleta', v)
    if (v) {
        query.value = ""
        busqueda.value.orden = 'relevancia'
    }
    else  if(!query.value)
            busqueda.value.orden = 'recientes'
    busqueda.value.completo = v ? 1 : 0
    console.log('(watch) ex=', busqueda.value.completo)
})

const buscando = ref(false)

watch(busqueda, (value) => {
    if (!inQuery.value) {
        const currentUrl = window.location.href.replace(/\?.*/, '')

        var args = {}
        if (query.value)
            args.buscar = query.value

        if (busqueda.value.categoria != 'todos')
            args.categoria = busqueda.value.categoria

        if (busqueda.value.ano != 'todos')
            args.ano = busqueda.value.ano

        if (busqueda.value.orden != 'recientes')
            args.orden = busqueda.value.orden

        //if (busqueda.completo)
        //  args.completo = 1
        if (vistaBusquedaCompleta.value)
            args.completo = 1

        if(!args.completo||query.value) {
            buscando.value = true
            router.get(currentUrl, args)
        }
    }
}, { deep: true })

const inQuery = ref(false)
function focusQuery() {
    inQuery.value = true
}

function blurQuery() {
    inQuery.value = false
}

function focusBuscar() {
    document.querySelector('.search-input').focus()
    window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
}



</script>



<style scoped>
.table {
    @apply min-w-full divide-y divide-gray-500/50;
}

.table-header {
    @apply px-6 py-3 text-left text-xs font-medium uppercase tracking-wider;
}


.table-cell {
    @apply px-6 py-4 whitespace-nowrap text-sm font-medium;
}

.sel-trans {
    @apply bg-base-200 border-transparent border-b-gray-500/50;
}</style>
