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
            <SearchInput placeholder="Buscar eventos..." />
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


            <Categorias :categorias="categorias" :url="route('eventos')" select-breakpoint="md"
                div-class="min-w-[24ch] w-full md:w-fit " />

            <div class="w-full grow">



                <SearchResultsHeader :results="listado" />


                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <CardEvent v-for="contenido in listado.data" :key="contenido.id"
                        :title="contenido.titulo" :image="contenido.imagen" :href="route('evento', contenido.slug)"
                        :description="contenido.descripcion" :fecha-inicio="contenido.fecha_inicio"/>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </div>
</template>



<script setup>
import CardEvent from '@/Components/CardEvent.vue'


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
</script>
