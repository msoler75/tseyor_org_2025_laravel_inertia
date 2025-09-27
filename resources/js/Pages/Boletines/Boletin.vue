<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back>Boletines</Back>
            <div class="flex gap-2">
                <a href="/admin/boletin" v-if="!boletin.enviado" class="btn btn-xs btn-warning shadow">Pendiente de enviar</a>
                <Share />
                <AdminLinks
                    modelo="boletin"
                    necesita="administrar contenidos"
                    :contenido="boletin"
                />
            </div>
        </div>
        </PageHeader>

        <PageContent class="sm:max-w-[80ch]">

            <div class="py-[10ch] mb-12 relative">

            <FontSizeControls class="hidden lg:flex absolute right-4 top-4"/>

            <div class="prose mx-auto">
                <h1>{{ boletin.titulo }}</h1>

                <div class="text-sm mb-20 flex justify-between">
                    <span />

                    <!-- <a target="_blank" class="btn btn-xs btn-error w-fit flex gap-3" :href="comunicado.pdf"
                        title="Ver en formato PDF">
                        <Icon icon="ph:file-pdf" />
                    </a> -->
                    <TimeAgo :date="boletin.created_at" :includeTime="true" />
                </div>
            </div>

            <LazyHydrate>
                <Content :content="boletin.texto" class="mx-auto" />
            </LazyHydrate>
            </div>
        </PageContent>
    </Page>
</template>

<script setup>
import { defineProps } from "vue";

defineProps({
    boletin: {
        type: Object,
        required: true,
    },
});
</script>

<style scoped>
/* Agrega estilos específicos para la vista aquí */
</style>
