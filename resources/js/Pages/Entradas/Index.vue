<template>
    <div class="container py-12 mx-auto">

        <AdminPanel modelo="entrada" necesita="administrar contenidos" class="mb-3" />

        <h1>Blog</h1>
        <p>Aquí puedes conocer sobre la vida de la comunidad Tseyor.</p>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full flex-grow">

                <SearchResultsHeader :results="listado" />

                <div class="grid gap-8" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <CardContent v-if="listado.data.length > 0" v-for="contenido in listado.data"
                        :imageLeft="true"
                        :imageHeight="300"
                        :key="contenido.id" :title="contenido.titulo" :image="contenido.imagen"
                        :href="route('entrada', contenido.slug)" :description="contenido.descripcion"
                        :date="contenido.published_at" imageClass="h-80"/>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>

            <div class="min-w-[250px] lg:min-w-[440px]" v-if="listado.first_page_url.indexOf('?buscar=') < 0">
                <div class="card bg-base-100 shadow p-10 space-y-7">
                    <h2 class="mb-5">Recientes</h2>
                    <ul class="list-disc">
                        <li v-for="entrada in recientes" :key="entrada.id">
                            <Link :href="`/entradas/${entrada.slug}`"
                                class="mt-2 text-sm font-semibold">
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
        default: () => { data: [] }
    },
    recientes: {
        default: () => []
    }
});

const listado = ref(props.listado);
const recientes = ref(props.recientes)


</script>
