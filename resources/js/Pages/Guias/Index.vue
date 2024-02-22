
<template>
    <div class="container py-12 mx-auto">


        <div class="flex justify-between items-center mb-7">
            <Back class="opacity-0 pointer-events-none">Glosario</Back>
            <Link href="/libros/glosario-terminologico" class="flex gap-2 items-center" title='Descarga todo el glosario en pdf'><Icon icon="ph:download-duotone" />Descargar</Link>
            <AdminPanel modelo="guia" necesita="administrar contenidos" />
        </div>


        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Glosario</h1>
            <GlosarioTabs />
        </div>


        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <GlosarioBar/>

        <div class="glosario-term w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div
                class="w-full h-fit md:w-auto min-w-[150px] lg:min-w-[240px] card bg-base-100 shadow p-10 md:sticky md:top-20 flex-row md:flex-col flex-wrap md:gap-1">
                <Link v-for="guia in guias.data" :key="guia.slug" :href="route('guia', guia.slug)"
                    class="inline font-semibold p-3 md:px-0 md:py-1 w-fit"
                    preserve-scroll @click="scrollToTerm">
                {{ guia.nombre }}
                </Link>
            </div>

            <div class="w-full flex-grow">

                <GridAppear class="gap-8" col-width="12rem">
                    <CardContent v-for="contenido in guias.data" :key="contenido.id" :image="contenido.imagen"
                        :href="route('guia', contenido.slug)" imageClass="h-60"
                        preserve-scroll @click="scrollToTerm">
                        <div
                            class="text-center p-2 text-xl font-bold transition duration-300 group-hover:text-primary  group-hover:drop-shadow">
                            {{ contenido.nombre }}</div>
                    </CardContent>
                </GridAppear>


            </div>


        </div>
    </div>
</template>

<script setup>
import { scrollToTerm } from '@/composables/glosarioscroll.js'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    guias: {
        default: () => []
    }
});

</script>
