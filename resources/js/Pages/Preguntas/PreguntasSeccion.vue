<template>
    <Page class="preguntas">

        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back :href="route('preguntas')">Preguntas frecuentes</Back>
            <div class="flex gap-2 items-center">
                <Link
                    href="/libros/preguntas-y-respuestas-tseyor"
                    class="btn btn-xs btn-error flex gap-2 items-center"
                    title="Descarga todo el glosario en pdf"
                    >
                    <Icon icon="ph:download-duotone" /><span class="hidden sm:inline">Descargar </span>libro
                </Link>
                <Share />
            </div>
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
