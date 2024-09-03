<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <AdminLinks modelo="equipo" necesita="administrar equipos" class="mb-3" />

        <h1>Equipos</h1>
        <p>Equipos de trabajo y departamentos de la UTG.</p>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <Categorias :categorias="categorias" :url="route('equipos')" columna-breakpoint="md" select-breakpoint="sm"
                div-class="w-full md:w-fit " />

            <div class="w-full flex-grow">

                <SearchResultsHeader :results="listado" />

                <GridAppear v-if="listado.data.length > 0" class="gap-4" col-width="24rem">

                    <CardContent v-for="contenido in listado.data" :key="contenido.id"
                        :image="contenido.imagen || equipo_fallback" :title="contenido.nombre"
                        :href="route('equipo', contenido.slug)" image-left image-class="min-h-[150px]"
                        class="relative min-h-16" :description="contenido.descripcion" :tag="contenido.categoria"
                        descriptionClass="max-h-[4rem]">
                        <div v-if="contenido.oculto" class="badge badge-error flex gap-2 items-center text-xs">
                            OCULTO
                            <Icon icon="ph:lock-open-duotone" />
                        </div>
                        <div class="flex gap-3 items-center justify-between">
                            <span class="text-xs badge-neutral"
                                :class="contenido.soy_miembro || contenido.soy_coordinador ? 'badge' : ''">
                                {{ contenido.soy_coordinador ? 'ERES COORDINADOR' : contenido.soy_miembro ? 'ERES MIEMBRO':''}}
                            </span>
                            <div class="text-2xl flex gap-2 items-center">
                                <Icon icon="ph:user-duotone" /> {{ contenido.miembros_count }}
                            </div>
                        </div>
                    </CardContent>

                </GridAppear>

                <pagination class="mt-6" :links="listado.links" />

            </div>

        </div>
    </div>
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

const equipo_fallback = '/almacen/medios/equipos/equipo1.jpg'

</script>
