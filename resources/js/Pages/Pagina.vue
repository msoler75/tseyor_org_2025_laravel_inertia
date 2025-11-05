<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back v-if="pagina.atras_ruta" :href="pagina.atras_ruta">{{ pagina.atras_texto }}</Back>
            <Back v-else-if="pagina.descubre" :href="route('descubre')">Descubre</Back>
            <span v-else></span>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="pagina" necesita="administrar contenidos" :contenido="pagina" />
            </div>
        </div>
        </PageHeader>


           <PageContent class="sm:max-w-[80ch]">
            <div class="py-[10ch] mb-12 relative">

                <FontSizeControls class="hidden lg:flex absolute right-4 top-4"/>

                <div class="prose mx-auto">
            <h1 class="text-center capitalize">{{ pagina.titulo }}</h1>
            <Content :content="pagina.texto" class="mx-auto animate-fade-in" />
        </div>

            </div>
        </PageContent>
    </Page>
</template>


<script setup>
import {saveImagesInfo} from "@/composables/image";

const props = defineProps({
    pagina: {
        type: Object,
        required: true,
    },
    imagenesInfo: {
        type: Object,
        required: false,
    }
})

saveImagesInfo(props.imagenesInfo)

</script>
