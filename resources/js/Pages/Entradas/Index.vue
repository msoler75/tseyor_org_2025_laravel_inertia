<template>
    <Page>

        <div class="flex justify-between mb-20">
            <Back :href="route('biblioteca')">Biblioteca</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="entrada" necesita="administrar contenidos" />
            </div>
        </div>

        <h1>Blog</h1>
        <p>Aquí puedes conocer sobre la vida de la comunidad Tseyor.</p>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <ContentMain class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full grow">

                <SearchResultsHeader :results="listado" />

                <div class="grid gap-8" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(18rem, 1fr))` }">
                    <CardContent v-if="listado.data.length > 0" v-for="contenido in listado.data" :imageLeft="false"
                        :imageHeight="300" :key="contenido.id" :title="contenido.titulo" :image="contenido.imagen"
                        :href="route('blog.entrada', contenido.slug)"
                        :date="contenido.published_at" imageClass="h-50" :skeleton="cargando" />
                </div>

                <pagination @click="cargando = true" @finish="cargando = false" scroll-to="#main-content" class="mt-6"
                    :links="listado.links" />

            </div>

        </ContentMain>
    </Page>
</template>

<script setup>

const props = defineProps({
    listado: {
        default: () => { data: [] }
    },
    /*recientes: {
        default: () => []
    }*/
});

const cargando = ref(false);

</script>
