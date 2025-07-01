<template>
    <div class="container py-12 mx-auto">

        <div class="flex justify-between mb-20">
            <Back :href="route('novedades')">Novedades</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="evento" necesita="administrar social"  />
            </div>
        </div>

        <h1>Eventos</h1>
        <p>Cursos y encuentros de la comunidad Tseyor a los que puedes acudir.</p>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


            <Categorias :categorias="categorias" :url="route('eventos')" select-breakpoint="md"
                div-class="min-w-[24ch] w-full md:w-fit " />

            <div class="w-full grow">



                <SearchResultsHeader :results="listado" />


                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <CardContent v-if="listado.data.length > 0" v-for="contenido in listado.data" :key="contenido.id"
                        :title="contenido.titulo" :image="contenido.imagen" :href="route('evento', contenido.slug)"
                        :description="contenido.descripcion">
                        <div
                            class="absolute right-2 top-2 rounded-xs shadow-lg bg-base-100 text-xl font-bold overflow-hidden">
                            <span class="p-2 inline-block">
                                {{ fechas[contenido.id].substring(0, fechas[contenido.id].lastIndexOf(' ')) }}
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
const fechas = computed(() => {
    const f = {}
    props.listado.data.forEach(evento => {
        f[evento.id] = fechaEs(evento.fecha_inicio)
    })
    return f
})


</script>
