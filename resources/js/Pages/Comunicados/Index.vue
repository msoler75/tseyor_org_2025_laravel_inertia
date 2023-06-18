
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Comunicados</h1>
        <p>Aquí puedes ver las últimas comunicados de Tseyor.</p>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full flex-grow">
                <div class="flex justify-end mb-5">
                    <form :action="`/comunicados?buscar=${filtro}`">
                        <div class="flex gap-4 mt-5">
                            <input name="buscar" type="search" placeholder="Texto..." v-model="filtro"
                                class="w-full max-w-[200px] border border-gray-200 rounded focus:outline-none focus:border-gray-400" />

                            <button type="submit" @click.prevent="buscar()" class="btn btn-primary"
                            :disabled="filtro==filtrado">
                                Buscar
                            </button>

                            <button v-if="filtrado" type="button" @click.prevent="filtro = ''" class="btn btn-secondary">
                                Limpiar
                            </button>
                        </div>
                    </form>
                </div>



                <h1 v-if="listado.a.length > 0">
                    <template v-if="filtrado">
                        Resultados de '{{ filtrado }}'
                    </template>
                </h1>

                <div v-else>No hay resultados</div>


                <div class="grid grid-cols-1 gap-8 mt-8">
                    <div v-if="listado.a.length > 0" v-for="comunicado in lislistado.y="comunicado.id"
                        class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <img :src="comunicado.imagen_url" :alt="comunicado.titulo" class="h-48 object-cover w-full" />
                        <div class="p-4">
                            <h2 class="text-lg font-bold mb-2">{{ comunicado.titulo }}</h2>
                            <p class="text-gray-700 text-sm">{{ comunicado.descripcion }}</p>
                            <Link :href="`/comunicados/${comunicado.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                Leer más
                            </Link>

                            <p class="text-gray-600 mb-2 w-full text-xs text-right">
                                <TimeAgo :date="comunicado.published_at" />
                            </p>
                        </div>
                    </div>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>

            <div class="min-w-[250px] lg:min-w-[440px]">
                <div class="card shadow rounded p-10 space-y-7">
                    <h2 class="mb-5">Acceso rápido</h2>
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
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import TimeAgo from '@/Components/TimeAgo.vue';
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    filtro: { default: () => "" },
   listado: {
        default: () => []
    },
    recientes: {
        default: () => []
    }
});

const filtro = ref(props.filtrado
const listado= ref(props.listado);
const recientes = ref(props.recientes)

watch(filtro, () => {
    if (filtro.value == "" && props.filtrado)
        router.visit(route('comunicados'))
})

function buscar() {
    router.get(route('comunicados'), { buscar: filtro.value }, { replace: true })
}
</script>
