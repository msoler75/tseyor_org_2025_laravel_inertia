
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Archivo de Comunicados</h1>


        <div class="flex justify-end mb-5">
            <SearchInput/>
        </div>

        <SearchResultsHeader :results="listado"/>

        <div class="">
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
                    <tr v-for="(comunicado, index) of listado.data" :key="comunicado.slug" class="table-row">
                        <td class="table-cell">{{ comunicado.numero }}</td>
                        <td class="table-cell">{{ comunicado.categoria }}</td>
                        <td class="table-cell">{{ comunicado.fecha_comunicado }}</td>
                        <td class="table-cell">{{ comunicado.titulo }}</td>
                    </tr>
                </tbody>
            </table>


            <pagination class="mt-6" :links="listado.links" />

        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import TimeAgo from '@/Components/TimeAgo.vue';
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import SearchInput from '@/Components/SearchInput.vue'
import SearchResultsHeader from '@/Components/SearchResultsHeader.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    listado: {
        default: () => []
    }
});

const listado = ref(props.listado)
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
