<template>
    <AppLayout title="Eventos">
        <div class="container py-12 mx-auto">

            <AdminLinks modelo="evento" necesita="administrar social" class="mb-3" />

            <h1>Eventos</h1>
            <p>Cursos y encuentros de la comunidad Tseyor a los que puedes acudir.</p>

            <div class="flex justify-end mb-5">
                <SearchInput />
            </div>

            <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


                <div
                    class="card bg-base-100 shadow flex-wrap flex-row mb-3 md:flex-col p-5 lg:p-10 gap-4 mx-auto self-baseline w-full justify-evenly md:w-auto md:sticky md:top-20">
                    <Link :href="`${route('eventos')}`"
                        :class="!filtrado && !categoriaActiva ? 'text-primary font-bold' : ''">
                    <span class="capitalize">Novedades</span>
                    </Link>

                    <div v-for="categoria of categorias" :key="categoria.nombre" class="flex gap-2"
                        :class="categoriaActiva == categoria.nombre ? 'text-primary font-bold' : ''">
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
                        <CardContent v-if="listado.data.length > 0" v-for="contenido in listado.data"
                            :key="contenido.id" :title="contenido.titulo" :image="contenido.imagen"
                            :href="route('evento', contenido.slug)" :description="contenido.descripcion">
                            <div class="absolute right-2 top-2 rounded shadow-lg bg-base-100 text-xl font-bold overflow-hidden">
                                <span class="p-2 inline-block">
                                    {{ fechas[contenido.id].substring(0,fechas[contenido.id].lastIndexOf(' ')) }}
                                </span>
                                <div class="bg-primary text-primary-content text-sm text-center p-1">
                                    {{ fechas[contenido.id].substring(fechas[contenido.id].lastIndexOf(' ')) }}
                                </div>
                                </div>
                        </CardContent>

                    </div>


                    <pagination class="mt-6" :links="listado.links" />

                </div>


            </div>
        </div>
    </AppLayout>
</template>



<script setup>
import { fechaEs } from '@/composables/textutils.js'

const props = defineProps({
    categoriaActiva: { default: () => '' },
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
    categorias: {
        default: () => []
    }
});

const listado = ref(props.listado);
const categorias = ref(props.categorias)
const fechas = computed(()=> {
    const f = {}
    props.listado.data.forEach(evento=>{
        f[evento.id] = fechaEs(evento.fecha_inicio)
    })
    return f
})


</script>
