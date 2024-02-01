
<template>
    <div class="container py-12 mx-auto">

        <AdminPanel modelo="comunicado" necesita="administrar contenidos" class="mb-3" />

        <div class="flex gap-12">
            <div>
                <h1>Comunicados de Tseyor</h1>
                <p>Aquí puedes encontrar todos los comunicados de Tseyor.</p>
            </div>

            <!-- <Planets /> -->

        </div>


        <div class="flex flex-wrap justify-between items-center my-4 gap-x-12 gap-y-7">

            <select v-model="busqueda.orden" class="sel-trans">
                <option value="relevancia" v-if="query">Relevancia</option>
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

            <SearchInput :arguments="busqueda" class="ml-auto sel-trans" v-model="query"
                @focus="focusQuery" @blur="blurQuery" />

            <div class="select-none text-2xl cursor-pointer" title="Elige la visualización en modo tabla o listado"
                @click="selectors.vistaComunicados=selectors.vistaComunicados!='tabla'?'tabla':'tarjetas'">
                <Icon v-show="selectors.vistaComunicados!='tabla'" icon="ph:squares-four-duotone" />
                <Icon v-show="selectors.vistaComunicados=='tabla'" icon="ph:grid-four-duotone" />
            </div>
        </div>



        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="flex-grow">

                <div class="flex justify-between items-center my-5">

                    <SearchResultsHeader :results="listado" :arguments="busqueda" />

                </div>


                <div v-if="(selectors.vistaComunicados != 'tabla') && listado.data && listado.data.length > 0"
                    class="grid gap-4" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">

                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :title="contenido.titulo"
                        :image="contenido.imagen"
                        image-class="h-80"
                        :href="route('comunicado', contenido.slug)"
                        :description="contenido.descripcion" :date="contenido.published_at" />

                </div>


                <table v-else-if="selectors.vistaComunicados == 'tabla' && listado.data && listado.data.length > 0" class="table bg-base-100">
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
                            <td class="table-cell"><Link :href="route('comunicado', comunicado.slug)" class="hover:underline py-2" v-html="comunicado.titulo"/></td>
                        </tr>
                    </tbody>
                </table>

                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
import { useSelectors } from '@/Stores/selectors'
defineOptions({ layout: AppLayout })
const selectors = useSelectors()

const props = defineProps({
    categoria: {},
    ano: {},
    orden: {},
    buscar: {},
    listado: {},
    recientes: {},
    archivo: {}
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

const añosBusqueda = []
for (var i = 2004; i <= añoActual; i++)
    añosBusqueda.push(i)

const query = ref("")
const busqueda = ref({ categoria: props.categoria || 'todos', ano: props.ano || 'todos', orden: props.orden || 'recientes' })

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

        router.get(currentUrl, args)
    }
}, { deep: true })

const inQuery = ref(false)
function focusQuery() {
    inQuery.value = true
}

function blurQuery() {
    inQuery.value = false
}

onMounted(() => {
    watch(query, () => {
        // console.log('query', query.value)
        if (query.value)
            busqueda.value.orden = 'relevancia'
        else
            busqueda.value.orden = 'recientes'
    })
})


</script>



<style scoped>
.table {
    @apply min-w-full divide-y divide-gray-500/50 ;
}

.table-header {
    @apply px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider;
}


.table-cell {
    @apply px-6 py-4 whitespace-nowrap text-sm font-medium;
}

.sel-trans {
    @apply bg-transparent border-transparent border-b-gray-500/50;
}
</style>
