
<template>
    <div class="container py-12 mx-auto">


        <div class="flex justify-between items-center mb-20">
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

        <TransitionFade>
            <div v-if="nav.scrollY > 300" class="fixed top-[4rem] left-0 right-0 p-3 bg-base-300 shadow z-10 cursor-pointer"
                @click="scrollToTop">
                <div class="container mx-auto flex justify-center gap-5 items-center ">
                    <Icon icon="ph:arrow-circle-up-duotone" class="transform scale-150 opacity-0" />
                    <span class="font-bold">Glosario</span>
                    <Icon icon="ph:arrow-circle-up-duotone" class="transform scale-150" />
                </div>
            </div>
        </TransitionFade>

        <div class="w-full flex gap-7 lg:gap-12 flex-wrap md:flex-nowrap">

            <div class="w-full md:w-[7rem] flex-shrink-0 card bg-base-100 shadow p-5 h-fit md:sticky md:top-20">
                <div class="flex flex-wrap md:hidden  gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2"
                        :href="route('terminos') + '?letra=' + letraItem"
                        preserve-scroll @click="scrollToWord(el)">
                    {{ letraItem }}
                    </Link>
                </div>

                <div class="hidden md:grid grid-flow-dense grid-cols-2 gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2"
                        :style="{'grid-column': Math.floor(index / (letras.length/2))+1 }" :href="route('terminos') + '?letra=' + letraItem"
                        preserve-scroll @click="scrollToWord(el)">
                    {{ letraItem }}
                    </Link>
                </div>
            </div>


            <div ref="el" class="w-full flex-grow">

                <SearchResultsHeader v-if="!letra" :results="listado" />

                <div class="grid gap-8 mb-14" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(16rem, 1fr))` }">
                    <Link v-for="contenido in listado.data" :key="contenido.id" :href="route('termino', contenido.slug)"
                        class="hover:text-primary transition-color duration-200 w-fit card shadow hover:shadow-lg px-5 py-2 bg-base-100 h-fit"
                        preserve-scroll @click="scrollToWord(el)">
                        <div v-html="contenido.nombre" class="capitalize lowercase font-bold text-lg"/>
                        <div v-if="filtrado" v-html="contenido.descripcion" class="mt-3"/>
                </Link>
                </div>

                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useNav } from '@/Stores/nav'
import { scrollToWord, scrollToTop } from '@/composables/glosarioscroll.js'

defineOptions({ layout: AppLayout })

const props = defineProps({
    listado: {
        default: () => { data: [] }
    },
    letras: {},
    letra: {},
    filtrado:{}
});

const el = ref(null)

const nav = useNav()

const listado = ref(props.listado);

</script>

