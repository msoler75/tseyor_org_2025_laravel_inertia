<template>
    <div class="container py-12 mx-auto">

        <div class="flex justify-between items-center mb-20">
            <Back href="/comunidad">Comunidad</Back>
            <AdminPanel modelo="experiencia" necesita="administrar experiencias" />
        </div>

        <h1>Experiencias Intedimensionales</h1>
        <p>Sueños, meditaciones, extrapolaciones y trabajos grupales.</p>

        <Link :href="route('experiencia.nueva')" class="btn btn-primary">
            <Icon icon="ph:plus-square-duotone"/> Envía tu experiencia
        </Link>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>


        <div class="w-full flex gap-7 lg:gap-10 flex-wrap md:flex-nowrap">

            <div
                class="gap-3 xl:gap-0 w-full md:w-[21ch] card bg-base-100 shadow flex-wrap flex-row xl:flex-col p-5 lg:p-10 xl:p-5 self-baseline md:sticky md:top-20">
                <Link :href="`${route('experiencias')}`" class="py-2 hover:text-primary transition-colors duration-250"
                    :class="!filtrado && !categoriaActiva ? 'text-primary font-bold' : ''">
                <span class="capitalize">Recientes</span>
                </Link>

                <div v-for="categoria of categorias" :key="categoria.nombre" class="flex"
                    :class="categoriaActiva == categoria.nombre ? 'text-primary font-bold' : ''">
                    <Link :href="`${route('experiencias')}?categoria=${categoria.nombre}`"
                        class="py-2 hover:text-primary transition-colors duration-250">
                    <span class="capitalize">{{ categoria.nombre }}</span>
                    <small v-if="categoria.total > 0"> ({{ categoria.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="cloud w-full flex-grow card shadow bg-base-100 px-5 py-7">

                <SearchResultsHeader :results="listado" :category="categoriaActiva" />

                <div class="grid gap-2 py-4" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">

                    <Link v-for="contenido in listado.data" :key="contenido.id" :href="route('experiencia', contenido.id)"
                        class="hover:text-primary transition-color duration-200 px-5 py-2 h-full flex flex-row items-baseline gap-3 hover:bg-base-200/40 rounded-xl w-full">
                    <Icon icon="ph:dot-fill" class="flex-shrink-0" />
                    <div class="max-w-[calc(100%-7rem)]">
                        <div v-html="contenido.nombre" class="capitalize lowercase font-bold" />
                        <div v-if="filtrado" v-html="contenido.texto" class="mt-3" />
                        <div class="flex flex-wrap gap-3 mt-4">
                            <span class="badge text-xs">
                                <TimeAgo :date="contenido.fecha" />
                            </span>
                            <span v-if="!categoriaActiva" class="badge text-xs badge-neutral">{{ contenido.categoria }}</span>
                        </div>
                    </div>
                    </Link>
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
// const categorias = ref(props.categorias)

</script>

<style scoped>
.cloud {
    background-color: transparent;
    background-image: radial-gradient(white 70%, transparent 30%);
    background-size: calc(var(--border-thickness) * 2) calc(var(--border-thickness) * 2);
    background-position: 0 0;
    position: relative;
    z-index: 1;
    --box-size: 100%;
  --border-thickness: 5%;
  box-shadow: none;
}

.dark .cloud {
    background-image: radial-gradient(black 70%, transparent 30%);
}

.cloud::before {
    content: "";
    display: block;
    background-color: hsla(0, 0%, 100%, 1);
    position: absolute;
    top: var(--border-thickness);
    left: var(--border-thickness);
    height: calc(var(--box-size) - var(--border-thickness) * 2);
    width: calc(var(--box-size) - var(--border-thickness) * 2);
    z-index: -1;
}

.dark .cloud::before {
    background-color: rgb(0, 0, 0);
}
</style>
