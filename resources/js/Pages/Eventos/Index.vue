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

                <div v-if="eventosEnCurso.length > 0" class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 text-accent">🔴 Eventos en curso</h2>
                    <div class="grid gap-4"
                        :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(min(24rem, 100%), 1fr))` }">
                        <CardEvent v-for="contenido in eventosEnCurso" :key="`enCurso-${contenido.id}`"
                            :title="contenido.titulo" :image="contenido.imagen" :href="route('evento', contenido.slug)"
                            :description="contenido.descripcion" :fecha-inicio="contenido.fecha_inicio"
                            :fecha-fin="contenido.fecha_fin" :hora-inicio="contenido.hora_inicio" :hora-fin="contenido.hora_fin"
                            :draft="contenido.visibilidad!='P'"
                            :imageWidth="800"
                            />
                    </div>
                </div>

                <div v-if="eventosProximos.length > 0" :class="eventosEnCurso.length > 0 ? 'mt-8' : ''">
                    <h2 v-if="eventosEnCurso.length > 0" class="text-xl font-semibold mb-4">Próximos eventos</h2>
                    <div class="grid gap-4"
                        :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(min(24rem, 100%), 1fr))` }">
                        <CardEvent v-for="contenido in eventosProximos" :key="contenido.id"
                            :title="contenido.titulo" :image="contenido.imagen" :href="route('evento', contenido.slug)"
                            :description="contenido.descripcion" :fecha-inicio="contenido.fecha_inicio"
                            :fecha-fin="contenido.fecha_fin" :hora-inicio="contenido.hora_inicio" :hora-fin="contenido.hora_fin"
                            :draft="contenido.visibilidad!='P'"
                            :imageWidth="800"
                            />
                    </div>
                </div>

                <div v-if="eventosPasados.length > 0" class="mt-8">
                    <div class="my-6 border-t border-base-200"></div>
                    <h2 class="text-xl font-semibold mb-4">Eventos pasados</h2>
                    <div class="grid gap-4"
                        :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(min(24rem, 100%), 1fr))` }">
                        <CardEvent v-for="contenido in eventosPasados" :key="`eventosPasados-${contenido.id}`"
                            :title="contenido.titulo" :image="contenido.imagen" :href="route('evento', contenido.slug)"
                            :description="contenido.descripcion" :fecha-inicio="contenido.fecha_inicio"
                            :fecha-fin="contenido.fecha_fin" :hora-inicio="contenido.hora_inicio" :hora-fin="contenido.hora_fin"
                            :draft="contenido.visibilidad!='P'"
                            :imageWidth="800"
                            />
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

// Separar eventos en curso, próximos y pasados
import { esFechaFutura, aFecha, esEventoEnCurso } from '@/composables/fechas.js'

const eventsArray = computed(() => Array.isArray(listado.value.data) ? listado.value.data : []);

// Eventos que están actualmente en curso
const eventosEnCurso = computed(() =>
    eventsArray.value.filter(e =>
        esEventoEnCurso(e.fecha_inicio, e.hora_inicio, e.fecha_fin)
    )
);

// Eventos futuros que aún no han comenzado
const eventosProximos = computed(() =>
    eventsArray.value.filter(e => {
        // No incluir si está en curso
        if (esEventoEnCurso(e.fecha_inicio, e.hora_inicio, e.fecha_fin)) {
            return false;
        }
        // Incluir si es fecha futura
        return esFechaFutura(e.fecha_inicio);
    })
);

// Eventos que ya terminaron
const eventosPasados = computed(() =>
    eventsArray.value.filter(e => {
        // No incluir si está en curso
        if (esEventoEnCurso(e.fecha_inicio, e.hora_inicio, e.fecha_fin)) {
            return false;
        }
        // No incluir si es futuro
        if (esFechaFutura(e.fecha_inicio)) {
            return false;
        }
        // Es pasado
        return true;
    }).sort((a, b) => (aFecha(b.fecha_inicio) || 0) - (aFecha(a.fecha_inicio) || 0))
);
</script>
