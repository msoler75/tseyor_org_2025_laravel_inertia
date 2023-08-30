
<template>
    <div class="container py-12 mx-auto">

        <AdminPanel modelo="comunicado" necesita="administrar contenidos" class="mb-3" />

        <div class="flex gap-12">
            <div>
                <h1>Comunicados de Tseyor</h1>
                <p>Aquí puedes encontrar todos los comunicados de Tseyor.</p>
            </div>

            <Planets />

        </div>



        <div class="flex flex-wrap justify-between items-baseline my-7 gap-x-12 gap-y-7">

            <a v-for="categoria, index of categorias" :key="index" :style="{ order: categoria.order }"
                class="cursor-pointer hover:underline select-none" :class="categoria._class" @click="seleccionado(index)">
                <span :class="categoriaActual == index ? 'border-b-4 border-secondary font-bold' : ''">
                    {{ categoria.etiqueta }}
                </span>
            </a>

            <div class="flex gap-2 select-none"><input id="tabla" type="checkbox" v-model="tabla"><label for="tabla">Solo
                    listado</label></div>

            <SearchInput :arguments="{ ...busqueda, vista: vistaActual }" class="ml-auto" @focus="enBusqueda = true" />
        </div>


        <div v-show="enBusqueda" class="mb-7 -mt-4 flex flex-wrap justify-end gap-4">
            <select v-model="busqueda.tipo">
                <option value="todos">Todos los comunicados</option>
                <option v-for="etiqueta, tipo  of tiposBusqueda" :key="tipo" :value="tipo">{{ etiqueta }}</option>
            </select>
            <select v-model="busqueda.ano">
                <option value="todos">Cualquier año</option>
                <option v-for="año of añosBusqueda" :key="año" :value="año">{{ año }}</option>
            </select>
            <select v-model="busqueda.orden">
                <option value="relevancia">Relevancia</option>
                <option value="recientes">Primero los recientes</option>
                <option value="antiguos">Primero los antiguos</option>
            </select>
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="flex-grow">

                <SearchResultsHeader :results="listado" />

                <div v-if="(!vistaActual || vistaActual == 'tarjetas') && listado.data && listado.data.length > 0"
                    class="grid gap-4" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">

                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :title="contenido.titulo"
                        :image="contenido.imagen" :href="route('comunicado', contenido.slug)"
                        :description="contenido.descripcion" :date="contenido.published_at" />

                </div>


                <table v-else-if="vistaActual == 'tabla' && listado.data && listado.data.length > 0" class="table">
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
                            <td class="table-cell" v-html="comunicado.titulo"></td>
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

defineOptions({ layout: AppLayout })

const props = defineProps({
    vista: { default: '' },
    categoria: { default: 'recientes' },
    tipo: {},
    ano: {},
    orden: {},
    buscar: {},
    listado: {},
    recientes: {},
    archivo: {}
});

// Obtener la fecha actual
const añoActual = new Date().getFullYear()

const tiposBusqueda =
{
    GEN: 'General',
    TAP: 'TAP',
    DDM: 'Doce del Muulasterio',
    MUL: 'Comunicados para los Muul'
}

const añosBusqueda = []
for (var i = 2004; i <= añoActual; i++)
    añosBusqueda.push(i)


const enBusqueda = ref(props.buscar)
const busqueda = ref({ tipo: props.tipo || 'todos', ano: props.ano || 'todos', orden: props.orden || 'relevancia' })

const tabla = ref(props.vista == 'tabla')
const vistaActual = ref(props.vista)
const categoriaActual = ref(props.categoria || 'recientes')

watch(tabla, (value) => {
    console.log(value)
    vistaActual.value = value ? 'tabla' : 'tarjetas'
})






const categorias = ref({
    resultados: { etiqueta: 'Resultados', order: -4, _class: categoriaActual.value === 'resultados' ? '' : 'hidden' },
    recientes: { etiqueta: 'Recientes', order: -3 },
    general: { etiqueta: 'Lista general', order: -2 },
})

categorias.value[añoActual] = { etiqueta: añoActual, order: 0 }
categorias.value[añoActual - 1] = { etiqueta: añoActual - 1, order: 0 }
categorias.value[añoActual - 2] = { etiqueta: añoActual - 2, order: 0 }

function seleccionado(categoria) {
    categoriaActual.value = categoria
}

watch(categoriaActual, (value) => {
    const currentUrl = window.location.href.replace(/\?.*/, '')
    router.get(currentUrl, { categoria: value, vista: vistaActual.value })
})
</script>



<style scoped>
.table {
    @apply min-w-full divide-y divide-gray-200 bg-white;
}

.table-row {
    @apply bg-gray-50;
}

.table-header {
    @apply px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
}

.table-body {
    @apply divide-y divide-gray-200;
}

.table-cell {
    @apply px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900;
}
</style>
