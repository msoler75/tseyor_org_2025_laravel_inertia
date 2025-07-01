<template>
    <Page>

        <div class="flex justify-between items-center mb-20">
            <span />
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="noticia" necesita="administrar contenidos" />
            </div>
        </div>


        <h1>Noticias</h1>
        <p>Aquí puedes ver las últimas noticias de Tseyor.</p>

        <ContentMain class="flex justify-end mb-5">
            <SearchInput />
        </ContentMain>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="w-full grow">

                <SearchResultsHeader :results="listado" />

                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(28rem, 1fr))` }">

                    <CardContent v-if="listado.data.length > 0" v-for="contenido in listado.data" :key="contenido.id"
                        :imageLeft="true" :title="contenido.titulo" :image="contenido.imagen"
                        :href="route('noticia', contenido.slug)" :description="contenido.descripcion"
                        :date="contenido.published_at" imageClass="h-60" />

                </div>

                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </Page>
</template>

<script setup>


const props = defineProps({
    filtrado: { default: () => "" },
    listado: {
        default: () => { data: [] }
    },
    recientes: {
        default: () => []
    }
});

const listado = ref(props.listado)
const recientes = ref(props.recientes)

</script>
