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
                    <CardContent v-if="listado.data.length > 0" v-for="contenido in listado.data" :key="contenido.id"
                        :title="contenido.titulo" :image="contenido.imagen" :href="route('entrada', contenido.slug)"
                        :description="contenido.descripcion" :date="contenido.published_at" imageClass="h-80" />
                </div>


                <pagination class="mt-6" :links="listado.links" />


            </div>
        </div>
</template>


<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
   listado: {
        default: () => {data:[]}
    }
});

const listado= ref(props.listado);
const recientes = ref(props.recientes)


</script>
