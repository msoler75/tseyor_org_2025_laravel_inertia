<template>
    <Page>

        <div class="flex justify-between items-center mb-20">
            <Back v-if="pagina.atras_ruta" :href="pagina.atras_ruta">{{ pagina.atras_texto }}</Back>
            <span v-else></span>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="pagina" necesita="administrar contenidos" :contenido="pagina" />
            </div>
        </div>

        <div class="py-14 px-5 md:px-14 card bg-base-100 shadow mx-auto w-fit">
            <h1 class="text-center capitalize">{{ pagina.titulo }}</h1>
            <Content :content="pagina.texto" class="mx-auto animate-fade-in" />
        </div>

    </Page>
</template>


<script setup>

const props = defineProps({
    pagina: {
        type: Object,
        required: true,
    },
})

</script>
