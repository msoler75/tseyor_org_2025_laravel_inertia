<template>
    <Page>

        <PageHeader>

        <div class="flex justify-between items-center mb-20">
            <Back href="biblioteca">Biblioteca Tseyor</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="pagina" necesita="administrar contenidos" />
            </div>
        </div>


        <div class="mx-auto container mb-16 flex flex-col justify-center items-center">
            <h1>Descubre</h1>
            <p>Presentación de algunos temas clave de Tseyor para una mayor comprensión y claridad.</p>
        </div>

        </PageHeader>


        <PageWide>

            <div class="w-full grow">

                <GridAppear class="gap-8" col-width="16rem">
                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :image="contenido.imagen"
                    :title="contenido.titulo"
                    :description="contenido.descripcion"
                        :href="contenido.ruta" imageClass="h-60" preserve-page>
                    </CardContent>
                </GridAppear>


                <pagination class="mt-6" :links="listado.links" />

            </div>

        </PageWide>
        </Page>
</template>

<script setup>


const props = defineProps({
    listado: {
        default: () => { data: [] }
    },
});


</script>
