<template>
    <Page class="preguntas">

        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back :href="route('preguntas')">Preguntas frecuentes</Back>
            <Link
                href="/libros/preguntas-y-respuestas-tseyor"
                class="btn btn-sm btn-primary flex gap-2 items-center"
                title="Descarga esta sección en pdf"
            >
                <Icon icon="ph:download-duotone" />Descargar libro</Link
            >
            <Share />
        </div>
        </PageHeader>

               <PageContent class="sm:max-w-[80ch]">
            <div class="py-[10ch] relative">

                <FontSizeControls class="hidden lg:flex absolute right-4 top-4"/>


        <Preguntas class="mb-20 lg:mb-32 mx-auto break-normal" :titulo="titulo" fade-out>
            <div v-html="texto" />
        </Preguntas>
        </div>
        </PageContent>
    </Page>
</template>

<script setup>
defineProps({
    titulo: String,
    texto: String,
});
</script>
