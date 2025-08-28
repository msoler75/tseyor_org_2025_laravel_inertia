<template>
    <div class="container py-12 mx-auto mb-16">

        <div class="flex justify-between items-center mb-7">
            <Back href="biblioteca">Biblioteca Tseyor</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="pagina" necesita="administrar contenidos" />
            </div>
        </div>


        <div class="mx-auto mb-16 flex flex-col justify-center items-center">
            <h1>Descubre</h1>
            <p>Presentación de algunos temas clave de Tseyor para una mayor comprensión y claridad.</p>
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

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

        </div>
    </div>
</template>

<script setup>


const props = defineProps({
    listado: {
        default: () => { data: [] }
    },
});


</script>
