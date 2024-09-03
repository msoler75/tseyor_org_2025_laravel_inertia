<template>
    <div class="py-8">

        <div class="container mx-auto flex justify-between items-center mb-20">
            <Back>Normativas</Back>
            <AdminLinks modelo="normativa" necesita="administrar legal" :contenido="normativa" />
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto">
                <h1>{{ normativa.titulo }}</h1>

                <div class="text-neutral text-sm mb-2 flex justify-between">
                    <div class="ml-auto">Última actualización: <TimeAgo :date="normativa.updated_at" :includeTime="false" /></div>
                </div>
            </div>

            <Content :content="normativa.texto" class="mx-auto" />

        </div>

        <Comentarios :url="route('normativa', normativa.id)" />

    </div>
</template>

<script setup>


const props = defineProps({
    normativa: {
        type: Object,
        required: true,
    }
});

</script>
