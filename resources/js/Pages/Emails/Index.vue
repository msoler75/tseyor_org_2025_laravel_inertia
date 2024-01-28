
<template>
    <div class="container py-12 mx-auto">

        <div class="flex gap-3 items-center mb-20">
            <Icon icon="ph:arrow-left" /> <a href="/admin/dashboard">Panel de Administración</a>
        </div>

        <h1>Correos</h1>

        <div class="w-full flex gap-7 lg:gap-12 flex-wrap md:flex-nowrap">

            <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <th scope="col" class="px-6 py-3">Desde</th>
            <th scope="col" class="px-6 py-3">A</th>
            <th scope="col" class="px-6 py-3">Asunto</th>
            <th scope="col" class="px-6 py-3">Fecha</th>
                </thead>
                <tbody>
                    <template v-for="correo of listado.data" :key="correo.id">
                        <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 "
                        @click="clickHandle(correo.id)" >
                             <td class="px-6 py-4">{{ correo.from }}</td>
                             <td class="px-6 py-4">{{ correo.to }}</td>
                             <td class="px-6 py-4">{{ correo.subject }}</td>
                             <td class="px-6 py-4">
                                <TimeAgo :date="correo.created_at" />
                            </td>
                        </tr>
                        <tr v-show="viendoCorreo == correo.id">
                            <td colspan="4" class="border border-gray-500 border-opacity-25 max-h-[70vh] overflow-y-auto">
                                <Content class="mx-auto w-full" :content="correo.body" :optimize-images="false"/>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

            <pagination class="mt-6" :links="listado.links" />

        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    email: { required: false },
    listado: {
        default: () => { data: [] }
    }
});

const viendoCorreo = ref(props.email?.id || null)

const listado = ref(props.listado);

function clickHandle(id) {
    if(viendoCorreo.value==id)
        viendoCorreo.value = null
    else
        viendoCorreo.value = id
}
</script>

