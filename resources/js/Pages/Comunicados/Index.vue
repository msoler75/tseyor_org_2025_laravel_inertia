
<template>
    <div class="container px-4 py-12 mx-auto sm:px-6 lg:px-8">

        <AdminPanel modelo="comunicado" necesita="editar contenidos" class="mb-3"/>

        <tabs>
            <tab name="Recientes">

                <div class="flex gap-12">
                    <div>
                        <h1>Comunicados Recientes</h1>
                        <p>Aquí puedes encontrar los últimas comunicados de Tseyor.</p>
                    </div>

                    <Planets />

                </div>


                <div class="flex justify-end mb-5">
                    <SearchInput keyword="buscar_recientes" />
                </div>


                <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

                    <div class="flex-grow">

                        <SearchResultsHeader :results="listado" keyword="buscar_recientes" />

                        <div v-if="listado.data.length > 0" class="grid gap-4"
                            :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">

                            <CardContent v-if="listado.data.length > 0" v-for="contenido in listado.data"
                                :key="contenido.id" :title="contenido.titulo" :image="contenido.imagen"
                                :href="route('comunicado', contenido.slug)" :description="contenido.descripcion"
                                :date="contenido.published_at" />

                        </div>

                        <pagination class="mt-6" :links="listado.links" />

                    </div>

                    <div class="min-w-[250px] lg:min-w-[440px]" v-if="listado.first_page_url.indexOf('?buscar=') < 0">
                        <div class="card bg-base-100 shadow  p-10 space-y-7">

                            <h2 class="mb-5">Recientes</h2>
                            <ul class="list-disc">
                                <li v-for="comunicado in recientes" :key="comunicado.id">
                                    <Link :href="`/comunicados/${comunicado.slug}`"
                                        class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                    {{ comunicado.titulo }}
                                    </Link>
                                </li>
                            </ul>


                        </div>
                    </div>
                </div>

            </tab>




            <tab name="Archivados ⭐">

                <h1>Archivo de Comunicados</h1>
                <p>Consulta todos los comunicados registrados en archivo.</p>

                <div class="flex justify-end mb-5">
                    <SearchInput keyword="buscar_archivo" />
                </div>

                <SearchResultsHeader :results="archivo" keyword="buscar_archivo" />

                <table class="table">
                    <thead>
                        <tr class="table-row">
                            <th scope="col" class="table-header">Número</th>
                            <th scope="col" class="table-header">Categoría</th>
                            <th scope="col" class="table-header">Fecha</th>
                            <th scope="col" class="table-header">Título</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <tr v-for="(comunicado, index) of archivo.data" :key="comunicado.slug" class="table-row">
                            <td class="table-cell">{{ comunicado.numero }}</td>
                            <td class="table-cell">{{ comunicado.categoria }}</td>
                            <td class="table-cell">{{ comunicado.fecha_comunicado }}</td>
                            <td class="table-cell">{{ comunicado.titulo }}</td>
                        </tr>
                    </tbody>
                </table>


                <pagination class="mt-6" :links="archivo.links" />



            </tab>

        </tabs>

    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
import { Tabs, Tab } from 'vue3-tabs-component';

defineOptions({ layout: AppLayout })

const props = defineProps({
    vista: {},
    filtrado: {},
    listado: {},
    recientes: {},
    archivo: {}
});

// const listado = ref(props.listado)
// const recientes = ref(props.recientes)


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
