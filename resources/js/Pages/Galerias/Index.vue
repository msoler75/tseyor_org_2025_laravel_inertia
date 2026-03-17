<template>
    <Page>
        <PageHeader>
            <div class="flex justify-between mb-20">
                <Back :href="route('biblioteca')">Biblioteca</Back>
                <div class="flex gap-2">
                    <Share />
                    <AdminLinks modelo="galeria" necesita="administrar contenidos" />
                </div>
            </div>

            <div class="container mx-auto">
                <h1>Galerías</h1>
                <p>Explora nuestras colecciones de imágenes y arte.</p>
            </div>

            <div class="flex justify-end mb-5">
                <SearchInput placeholder="Buscar en galerías..." />
            </div>
        </PageHeader>

        <PageWide>

            <ScrollToHere class="flex gap-5 flex-wrap md:flex-nowrap">

                <div class="w-full grow">

                    <SearchResultsHeader :results="listado" />

                    <div class="grid gap-8" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(min(18rem, 100%), 1fr))` }">
                        <CardContent v-if="listado.data.length > 0" v-for="galeria in listado.data" :imageLeft="false"
                            :imageHeight="300" :key="galeria.id" :title="galeria.titulo" :description="galeria.descripcion"
                            :href="route('galeria', galeria.id)" :image="galeria.imagen_principal"
                            :date="galeria.created_at" :skeleton="cargando" />
                    </div>

                    <pagination @click="cargando = true" @finish="cargando = false" scroll-to="#main-content" class="mt-6"
                        :links="listado.links" />

                </div>

            </ScrollToHere>
        </PageWide>
    </Page>
</template>

<script setup>

const props = defineProps({
    listado: {
        type: Object,
        required: true
    },
    filtrado: {
        type: String,
        default: null
    }
})

const cargando = ref(false)

</script>
