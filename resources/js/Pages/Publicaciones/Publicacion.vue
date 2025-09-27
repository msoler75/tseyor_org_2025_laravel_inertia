<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back>Publicaciones</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="publicacion" necesita="administrar contenidos" :contenido="publicacion" />
            </div>
        </div>

        </PageHeader>

         <PageContent class="sm:max-w-[80ch]">
            <div class="py-[10ch] mb-12 relative">

                <FontSizeControls class="hidden lg:flex absolute right-4 top-4"/>

                <div class="prose mx-auto">
                <h1>{{ publicacion.titulo }}</h1>

                <div class="text-sm mb-2 flex justify-between">
                    <div />
                    <TimeAgo :date="publicacion.updated_at" :includeTime="false" />
                </div>
            </div>

            <Content :content="publicacion.texto" class="mx-auto" />

        </div>
        </PageContent>

        <PageFooter>
            <Comentarios :url="route('publicacion', publicacion.id)" />
        </PageFooter>
    </Page>
</template>

<script setup>
// import usePlayer from '@/Stores/player'

// const player = usePlayer()


const props = defineProps({
    publicacion: {
        type: Object,
        required: true,
    }
});


</script>
