
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Eventos</h1>
        <p>Cursos y encuentros de la comunidad Tseyor a los que puedes acudir.</p>

        <div class="flex justify-end mb-5">
                    <SearchInput />
                </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


            <div
                class="card bg-base-100 shadow flex-wrap flex-row mb-3 md:flex-col p-5 lg:p-10 gap-4 mx-auto self-baseline w-full justify-evenly md:w-auto md:sticky md:top-20">
                <Link :href="`${route('eventos')}`"
                :class="!filtrado&&!categoriaActiva ? 'text-blue-700 font-bold' : ''">
                <span class="capitalize">Novedades</span>
                </Link>

                <div v-for="categoria of categorias" :key="categoria.nombre" class="flex gap-2"
                :class="categoriaActiva == categoria.nombre ? 'text-blue-700 font-bold' : ''">
                    <Link :href="`${route('eventos')}?categoria=${categoria.nombre}`">
                    <span class="capitalize">{{ categoria.nombre }}</span>
                    <small v-if="categoria.total > 0">({{ categoria.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="w-full flex-grow">



                <SearchResultsHeader :results="listado" />


                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <div v-if="listado.data.length > 0" v-for="evento in listado.data" :key="evento.id"
                        class="card bg-base-100 shadow">
                        <img :src="evento.imagen_url" :alt="evento.titulo" class="h-48 object-cover w-full" />
                        <div class="p-4">
                            <h2 class="text-lg font-bold mb-2">{{ evento.titulo }}</h2>
                            <p class="text-gray-700 text-sm">{{ evento.descripcion }}</p>
                            <Link :href="`/eventos/${evento.slug || evento.id}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                            Leer más
                            </Link>

                            <p class="text-gray-600 mb-2 w-full text-xs text-right">
                                <TimeAgo :date="evento.published_at" />
                            </p>
                        </div>
                    </div>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </div>
</template>



<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
    categoriaActiva: {default: ()=> ''},
    filtrado: {default: ()=> ''},
    listado: {
        default: () => { data: [] }
    },
    categorias: {
        default: () => []
    }
});

const listado = ref(props.listado);
const categorias = ref(props.categorias)


</script>
