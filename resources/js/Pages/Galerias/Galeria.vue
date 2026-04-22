<template>
    <Page>
        <PageHeader>
            <div class="flex justify-between items-center mb-20">
                <Back :href="route('galerias')">Galerías</Back>
                <div class="flex gap-2">
                    <Share />
                    <AdminLinks modelo="galeria" necesita="administrar contenidos" />
                </div>
            </div>
        </PageHeader>

        <PageContent class="sm:max-w-[100vw]">
            <div class="py-[10ch] mb-12 relative">

                <div class="prose mx-auto">
                    <h1>{{ galeria.titulo }}</h1>

                    <div class="text-sm mb-2 flex justify-between">
                        <span />
                        <TimeAgo :date="galeria.created_at" :includeTime="false" />
                    </div>

                    <p v-if="galeria.descripcion">{{ galeria.descripcion }}</p>
                </div>

                <!-- Galería de imágenes -->
                <GridFill class="gap-4 mt-8" col-width="18rem">
                    <CardContent
                        v-for="(item, index) in galeria.items"
                        :key="item.id"
                        :image="item.nodo ? item.nodo.ubicacion : ''"
                        :title="item.titulo || undefined"
                        :description="item.descripcion || undefined"
                        imageClass="h-48"
                        @click="viewImage(index)"
                    >
                        <p v-if="item.user" class="text-sm opacity-50 mt-auto">{{ item.user.name }}</p>
                    </CardContent>
                </GridFill>

            </div>
        </PageContent>

        <PageFooter>
            <Comentarios :url="route('galeria', galeria.id)" />
        </PageFooter>

        <ClientOnly>
        <ImagesViewer
            :show="showImagesViewer"
            @close="showImagesViewer = false"
            :images="images"
            :index="imageIndex"
            :showFilename="true"
        />
    </ClientOnly>
    </Page>
</template>

<script setup>

const props = defineProps({
    galeria: {
        type: Object,
        required: true
    }
})
const showImagesViewer = ref(false);
const imageIndex = ref(0);
const images = computed(() => props.galeria.items.map(item => item.nodo ? item.nodo.ubicacion : null).filter(src => src !== null))

function viewImage(index) {
    imageIndex.value = images.value.findIndex((src, i) => i === index);
    showImagesViewer.value = true;
}
</script>
