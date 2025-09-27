<template>
    <Page>

        <PageHeader>
        <div class="flex justify-between mb-20">
            <Back :href="route('novedades')">Novedades</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="evento" necesita="administrar social"  />
            </div>
        </div>

        <div class="container mx-auto">
            <h1>Eventos</h1>
            <p>Cursos y encuentros de la comunidad Tseyor a los que puedes acudir.</p>
        </div>

        <div class="flex justify-end mb-5">
            <SearchInput placeholder="Buscar eventos..." />
        </div>

        </PageHeader>

        <PageWide>
        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


            <Categorias v-if="false" :categorias="categorias" :url="route('eventos')" select-breakpoint="md"
                div-class="min-w-[24ch] w-full md:w-fit " />

            <div class="w-full grow">



                <SearchResultsHeader :results="listado" />


                <div v-if="eventosProximos.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <CardEvent v-for="contenido in eventosProximos" :key="contenido.id"
                        :title="contenido.titulo" :image="contenido.imagen" :href="route('evento', contenido.slug)"
                        :description="contenido.descripcion" :fecha-inicio="contenido.fecha_inicio"
                        :draft="contenido.visibilidad!='P'"/>
                </div>

                <div v-if="eventosPasados.length > 0" class="mt-8">
                    <div class="my-6 border-t border-base-200"></div>
                    <h2 class="text-xl font-semibold mb-4">Eventos pasados</h2>
                    <div class="grid gap-4"
                        :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                        <CardEvent v-for="contenido in eventosPasados" :key="`eventosPasados-${contenido.id}`"
                            :title="contenido.titulo" :image="contenido.imagen" :href="route('evento', contenido.slug)"
                            :description="contenido.descripcion" :fecha-inicio="contenido.fecha_inicio"
                            :draft="contenido.visibilidad!='P'"/>
                    </div>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
        </PageWide>
    </Page>
</template>



<script setup>

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

// Separar eventos próximos y pasados según `fecha_inicio`.
// Se asume que `fecha_inicio` es una cadena ISO (o similar) legible por Date.
import { esFechaFutura, aFecha } from '@/composables/fechas.js'

const now = new Date();

const eventsArray = computed(() => Array.isArray(listado.value.data) ? listado.value.data : []);

const eventosProximos = computed(() => eventsArray.value.filter(e => esFechaFutura(e && e.fecha_inicio, now)));

const eventosPasados = computed(() => eventsArray.value.filter(e => {
    // si no hay fecha o la fecha no es futura, considerar pasado
    return !esFechaFutura(e && e.fecha_inicio, now);
}).sort((a, b) => (aFecha(b && b.fecha_inicio) || 0) - (aFecha(a && a.fecha_inicio) || 0)));
</script>
