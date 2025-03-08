<template>
    <Page class="preguntas">
        <div class="flex justify-between items-center mb-20">
            <Back :href="route('preguntas')">Preguntas frecuentes</Back>
            <Link
                href="/libros/preguntas-y-respuestas-tseyor"
                class="btn btn-sm btn-primary flex gap-2 items-center"
                title="Descarga esta sección en pdf"
            >
                <Icon icon="ph:download-duotone" />Descargar libro</Link
            >
        </div>

        <Preguntas class="mb-20 lg:mb-32" :titulo="titulo" fade-out>
            <div v-html="texto" />
        </Preguntas>
    </Page>
</template>

<script setup>
defineProps({
    titulo: String,
    texto: String,
});
</script>
