<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back>Normativas</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="normativa" necesita="administrar legal" :contenido="normativa" />
            </div>
        </div>
        </PageHeader>


  <PageContent class="sm:max-w-[80ch]">

            <div class="py-[10ch] mb-12 relative">

                <FontSizeControls class="hidden lg:flex absolute right-4 top-4"/>

                <div class="prose mx-auto">
                <h1>{{ normativa.titulo }}</h1>

                <div class="text-sm mb-2 flex justify-between">
                    <div class="ml-auto">Última actualización: <TimeAgo :date="normativa.updated_at" :includeTime="false" /></div>
                </div>
            </div>

            <Content :content="normativa.texto" class="mx-auto" />

        </div>
  </PageContent>

        <PageFooter>
        <Comentarios :url="route('normativa', normativa.id)" />

    </PageFooter>
</Page>
</template>

<script setup>


const props = defineProps({
    normativa: {
        type: Object,
        required: true,
    }
});

</script>
