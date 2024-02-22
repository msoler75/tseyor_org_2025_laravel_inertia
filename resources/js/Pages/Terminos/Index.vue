
<template>
    <div class="container py-12 mx-auto">


        <div class="flex justify-between items-center mb-7">
            <Back class="opacity-0 pointer-events-none">Glosario</Back>
            <Link href="/libros/glosario-terminologico" class="flex gap-2 items-center" title='Descarga todo el glosario en pdf'><Icon icon="ph:download-duotone" />Descargar</Link>
            <AdminPanel modelo="termino" necesita="administrar contenidos" />
        </div>


        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Glosario</h1>
            <GlosarioTabs/>
        </div>


        <div class="flex justify-end mb-5">
            <SearchInput/>
        </div>

       <GlosarioBar/>

        <div class="w-full flex gap-7 lg:gap-12 flex-wrap md:flex-nowrap">

            <div class="w-full md:w-[7rem] flex-shrink-0 card bg-base-100 shadow p-5 h-fit md:sticky md:top-20">
                <div class="flex flex-wrap md:hidden  gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2"
                        :href="route('terminos') + '?letra=' + letraItem"
                        preserve-scroll @click="scrollToTerm">
                    {{ letraItem }}
                    </Link>
                </div>

                <div class="hidden md:grid grid-flow-dense grid-cols-2 gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2"
                        :style="{'grid-column': Math.floor(index / (letras.length/2))+1 }" :href="route('terminos') + '?letra=' + letraItem"
                        preserve-scroll @click="scrollToTerm">
                    {{ letraItem }}
                    </Link>
                </div>
            </div>


            <div class="glosario-term w-full flex-grow">

                <SearchResultsHeader v-if="!letra" :results="listado" />

                <GridAppear class="gap-8 mb-14" :time-lapse="0.01" col-width="16rem">
                    <Link v-for="contenido in listado.data" :key="contenido.id" :href="route('termino', contenido.slug)"
                        class="hover:text-primary transition-color duration-200 w-fit card shadow hover:shadow-lg px-5 py-2 bg-base-100 h-fit"
                        preserve-scroll @click="scrollToTerm">
                        <div v-html="contenido.nombre" class="capitalize lowercase font-bold text-lg"/>
                        <div v-if="filtrado" v-html="contenido.descripcion" class="mt-3"/>
                </Link>
                </GridAppear>

                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { scrollToTerm } from '@/composables/glosarioscroll.js'

defineOptions({ layout: AppLayout })

const props = defineProps({
    listado: {
        default: () => { data: [] }
    },
    letras: {},
    letra: {},
    filtrado:{}
});


const listado = ref(props.listado);

</script>

