
<template>
    <div class="container py-12 mx-auto">

        <AdminPanel modelo="libro" necesita="administrar contenidos" class="mb-3" />

        <h1>Libros</h1>
        <p>Libros que recogen toda la información de las <Referencia r="comunicados">conversaciones interdimensionales
            </Referencia> mantenidas con nuestros
            <Referencia>Guías Estelares</Referencia>.
        </p>

        <p>El autor de todas las obras es la <Referencia>Universidad Tseyor de Granada</Referencia>.</p>

        <div class="flex w-full justify-between mb-5">
            <SearchInput class="flex-grow">
                <div class="flex items-baseline gap-3 w-full pl-1"><input id="titulos" type="checkbox"
                        v-model="selectors.soloTitulosLibros"> <label for="titulos" class="mb-0">Solo títulos</label></div>
            </SearchInput>
        </div>

        <ContentMain class="w-full flex gap-5 flex-wrap xl:flex-nowrap" :fade-on-navigate="false">

            <div
                class="gap-3 xl:gap-0 xl:max-w-[260px] card bg-base-100 shadow flex-wrap flex-row xl:flex-col p-5 lg:p-10 xl:p-5 self-baseline xl:sticky xl:top-20">
                <Link :href="`${route('libros')}`" class="py-2 hover:text-primary transition-colors duration-250"
                    :class="!filtrado && !categoriaActiva ? 'text-primary font-bold' : ''"
                    >
                <span>Novedades</span>
                </Link>

                <div v-for="categoria of categorias" :key="categoria.nombre" class="flex"
                    :class="categoriaActiva == (categoria.valor || categoria.nombre) ? 'text-primary font-bold' : ''">
                    <Link :href="`${route('libros')}?categoria=${categoria.valor || categoria.nombre}`"
                        class="py-2 hover:text-primary transition-colors duration-250"
                        >
                    <span>{{ ucFirst(categoria.nombre) }}</span>
                    <small v-if="categoria.total > 0"> ({{ categoria.total }})</small>
                    </Link>
                </div>
            </div>

            <FadeOnNavigate class="w-full flex-grow">

                <SearchResultsHeader :results="listado" :valid-search="busquedaValida" />

                    <GridAppear v-if="selectors.soloTitulosLibros" class="grid gap-4" col-width="28rem">
                        <div v-for="libro in listado.data" :key="libro.id"
                            class="card shadow bg-base-100 p-5 hover:text-primary transition-colors duration-250">
                            <Link :href="route('libro', libro.slug)" class="flex items-center gap-3" >
                            <span v-html="libro.titulo"/></Link>
                        </div>
                    </GridAppear>

                    <GridAppear v-else class="grid gap-4" col-width="28rem">
                        <CardContent v-for="contenido in listado.data" :key="contenido.id" :title="contenido.titulo"
                            :image="contenido.imagen" :href="route('libro', contenido.slug)"
                            :description="contenido.descripcion" :date="contenido.published_at" :tag="contenido.categoria"
                            image-left class="h-[355px]" imageClass="w-[250px] h-[355px]"
                            image-contained
                             :image-view-transition-name="`imagen-libro-${contenido.id}`"/>

                    </GridAppear>


                <pagination class="mt-6" :links="listado.links" />

            </FadeOnNavigate>


        </ContentMain>
    </div>
</template>



<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import useSelectors from '@/Stores/selectors'
import {ucFirst }  from '@/composables/textutils'

defineOptions({ layout: AppLayout })

const selectors = useSelectors()


const props = defineProps({
    categoriaActiva: { default: () => '' },
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
    categorias: {
        default: () => []
    },
    busquedaValida: Boolean
});

const listado = ref(props.listado);

</script>
