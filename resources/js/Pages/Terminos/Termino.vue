<template>
    <div class="container py-12 mx-auto">

        <div class="flex justify-between items-center mb-7">
            <Back>Índice</Back>
            <Link href="/libros/glosario-terminologico" class="flex gap-2 items-center"
                title='Descarga todo el glosario en pdf'>
            <Icon icon="ph:download-duotone" />Descargar</Link>
            <AdminPanel modelo="termino" necesita="administrar contenidos" :contenido="termino" />
        </div>


        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Glosario</h1>
            <GlosarioTabs />
        </div>

        <div class="flex justify-end mb-5">
            <SearchInput :doSearch="false" @search="buscarClick" />
        </div>


        <GlosarioBar/>

        <div class="w-full flex justify-between gap-7 lg:gap-12 flex-wrap md:flex-nowrap">

            <div class="w-full md:w-[7rem] flex-shrink-0 card bg-base-100 shadow p-5 h-fit md:sticky md:top-20">
                <div class="flex flex-wrap md:hidden  gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2"
                        :href="route('terminos') + '?letra=' + letraItem" preserve-scroll @click="scrollToTerm">
                    {{ letraItem }}
                    </Link>
                </div>

                <div class="hidden md:grid grid-flow-dense grid-cols-2 gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2"
                        :style="{ 'grid-column': Math.floor(index / (letras.length / 2)) + 1 }"
                        :href="route('terminos') + '?letra=' + letraItem" preserve-scroll @click="scrollToTerm">
                    {{ letraItem }}
                    </Link>
                </div>
            </div>

            <!-- scroll aqui -->
            <div class="glosario-term">
                <div class="py-[5ch] bg-base-100 md:max-w-[80ch] mx-auto shadow-xl mb-20 px-7 rounded-xl">

                    <div class="prose mx-auto">
                        <h1 class="text-center xl:text-left capitalize lowercase">{{ termino.nombre }}</h1>
                        <p class="text-gray-600 text-xs my-5 text-right">
                            Última actualización:
                            <TimeAgo :date="termino.updated_at" />
                        </p>
                        <div class="mb-4"></div>
                        <Content :content="termino.texto" format="md" />
                    </div>

                </div>



                <div v-if="referencias.terminos.length"
                    class="flex flex-wrap sm:flex-nowrap gap-6 items-baseline mb-12 max-w-[80ch] mx-auto">
                    <h3 class="mr-5 whitespace-nowrap">Véase también:</h3>
                    <div class="flex gap-6 flex-wrap">
                        <Link v-for="contenido in referencias.terminos" :key="contenido.id"
                            :href="route('termino', contenido.slug)"
                            class="capitalize lowercase hover:text-primary transition-color duration-200 w-fit h-fit font-bold text-lg card shadow hover:shadow-lg px-5 py-2 bg-base-100"
                            preserve-scroll @click="scrollToTerm">
                        {{ contenido.nombre }}
                        </Link>
                    </div>
                </div>

                <div v-if="referencias.libros.length" class="grid gap-4 mt-4 max-w-[80ch] mx-auto"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(28rem, 1fr))` }">

                    <CardContent v-for="contenido in referencias.libros" :key="contenido.id" :title="contenido.titulo"
                        :image="contenido.imagen" :href="route('libro', contenido.slug)"
                        :description="contenido.descripcion" :date="contenido.published_at" :tag="contenido.categoria"
                        image-left class="h-[300px]" imageClass="w-[200px] h-[300px]" />
                </div>
            </div>


            <div class="w-[7rem] card bg-base-100 shadow p-5 h-fit sticky top-20 opacity-0 hidden lg:flex">
                <div class="letras grid grid-cols-2 gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2"
                        :href="route('terminos') + '?letra=' + letraItem" preserve-scroll @click="scrollToTerm">
                    {{ letraItem }}
                    </Link>
                </div>
            </div>

        </div>

        <hr class="my-12" />


        <div class="flex justify-between my-12">
            <Link v-if="anterior" :href="anterior.slug" class="hover:underline" preserve-scroll @click="scrollToTerm">
            ‹&nbsp;&nbsp; {{ anterior.nombre }}</Link>
            <span v-else />
            <Link v-if="siguiente" :href="siguiente.slug" class="hover:underline" preserve-scroll @click="scrollToTerm">{{
                siguiente.nombre }} &nbsp;&nbsp;›
            </Link>
            <span v-else />
        </div>

    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3';
import { useNav } from '@/Stores/nav'
import { scrollToTerm } from '@/composables/glosarioscroll.js'

defineOptions({ layout: AppLayout })

const props = defineProps({
    termino: {
        type: Object,
        required: true,
    },
    anterior: {
        type: Object,
        required: true,
    },
    siguiente: {
        type: Object,
        required: true,
    },
    letras: {},
    referencias: {
        type: Object,
        required: true,
    },
});

const el = ref(null)

const nav = useNav()

function buscarClick(query) {
    router.visit(route('terminos') + '?buscar=' + query)
}
</script>
