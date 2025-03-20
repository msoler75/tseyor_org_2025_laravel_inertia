<template>
    <Page>

        <div class="flex justify-between mb-20">
            <span />
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="sala" necesita="administrar directorio"/>
            </div>
        </div>

        <h1>Salas virtuales</h1>
        <p>Listado de salas para reuniones virtuales de los distintos equipos de la comunidad Tseyor.</p>

        <div class="flex w-full justify-end mb-5">
            <SearchInput />
        </div>

        <div class="w-full flex gap-5 flex-wrap xl:flex-nowrap" :fade-on-navigate="false">


            <div class="w-full grow">

                <SearchResultsHeader :results="listado" :valid-search="busquedaValida" />

                <GridAppear class="grid gap-4" col-width="28rem">
                    <div class="rounded-lg bg-base-100 p-5" v-for="sala in listado.data" :key="sala.id">
                        <Link :href="route('sala', sala.slug || sala.id)" class="text-lg"><strong>{{ sala.nombre
                            }}</strong></Link>

                        <p>{{ sala.descripcion }}</p>

                        <div class="flex w-full justify-end">
                            <a target="_blank" :href="sala.enlace"
                                class=" btn btn-primary after:content-['↗']">Acceder</a>
                        </div>

                    </div>

                </GridAppear>


                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </Page>
</template>



<script setup>


const props = defineProps({
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
});

const listado = ref(props.listado);

</script>
