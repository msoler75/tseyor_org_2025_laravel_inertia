
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Blog</h1>
        <p>Aquí puedes conocer sobre la vida de la comunidad Tseyor.</p>

        <div class="flex justify-end mb-5">
            <SearchInput/>
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full flex-grow">


                <SearchResultsHeader :results="listado"/>


                <div class="grid gap-8"
                :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <div v-if="listado.data.length > 0" v-for="entrada in listado.data" :key="entrada.id"
                        class="card bg-base-100 shadow">
                        <img :src="entrada.imagen_url" :alt="entrada.titulo" class="h-48 object-cover w-full" />
                        <div class="p-4">
                            <h2 class="text-lg font-bold mb-2">{{ entrada.titulo }}</h2>
                            <p class="text-gray-700 text-sm">{{ entrada.descripcion }}</p>
                            <Link :href="`/entradas/${entrada.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                Leer más
                            </Link>

                            <p class="text-gray-600 mb-2 w-full text-xs text-right">
                                <TimeAgo :date="entrada.published_at" />
                            </p>
                        </div>
                    </div>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>

            <div class="min-w-[250px] lg:min-w-[440px]"
            v-if="listado.first_page_url.indexOf('?buscar=')<0">
                <div class="card bg-base-100 shadow p-10 space-y-7">
                    <h2 class="mb-5">Acceso rápido</h2>
                    <ul class="list-disc">
                        <li v-for="entrada in recientes" :key="entrada.id">
                            <Link :href="`/entradas/${entrada.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                {{ entrada.titulo }}
                            </Link>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
   listado: {
        default: () => {data:[]}
    },
    recientes: {
        default: () => []
    }
});

const listado= ref(props.listado);
const recientes = ref(props.recientes)


</script>
