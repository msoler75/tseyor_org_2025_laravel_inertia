
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Audios</h1>
        <p>Relájate y disfruta de los talleres, meditaciones, cuentos y otros materiales de la filosofía de Tseyor.</p>


        <AudioPlayer id="player" v-if="playFile" :music="playFile" class="mx-auto max-w-[700px]
        card shadow-lg bg-base-300 dark:bg-gray-dark-800 rounded-tl-xl sm:rounded-t-xl
        mb-7
        " />


        <div class="flex justify-end mb-5">
            <SearchInput/>
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


            <div class="card bg-base-100 shadow self-baseline flex-wrap flex-row md:flex-col p-5 lg:p-10 gap-4 sticky top-5">
                <Link :href="`${route('audios')}`"
                :class="!filtrado&&!categoriaActiva ? 'text-blue-700 font-bold' : ''">
                <span class="capitalize">Novedades</span>
                </Link>

                <div v-for="categoria of categorias" :key="categoria.nombre" class="flex gap-2"
                    :class="categoriaActiva == categoria.nombre ? 'text-blue-700 font-bold' : ''">
                    <Link :href="`${route('audios')}?categoria=${categoria.nombre}`">
                    <span class="capitalize">{{ categoria.nombre }}</span>
                    <small v-if="categoria.total > 0">({{ categoria.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="w-full flex-grow">

                <SearchResultsHeader :results="listado"/>

                <div v-if="listado.data.length > 0" class="grid gap-4 max-w-full"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(32rem, 1fr))` }">
                    <div v-for="audio in listado.data" :key="audio.id"
                        class="card flex-row shadow bg-base-100 p-4 items-center gap-2 sm:gap-4 lg:gap-6"
                        style="max-width: calc(100vw - 30px)">
                        <div @click="play(audio)" class="btn btn-primary p-0 w-12 h-5 min-h-auto">
                            <Icon icon="ph:play-duotone" class="text-lg" />
                        </div>
                        <div class="flex flex-col gap-2 mr-auto ">
                            <h2 class="ml-2 text-lg font-bold my-0 leading-5">{{ audio.titulo }}</h2>
                            <Link v-if="!categoriaActiva" :href="`${route('audios')}?categoria=${audio.categoria}`"
                                class="sm:hidden badge badge-primary badge-outline">{{ audio.categoria }}</Link>
                        </div>
                        <Link v-if="!categoriaActiva" :href="`${route('audios')}?categoria=${audio.categoria}`"
                            class="hidden sm:block badge badge-primary badge-outline">{{ audio.categoria }}</Link>
                        <div class="btn p-2 w-12 h-9 min-h-auto shadow">
                            <Icon icon="ph:info-duotone" class="text-2xl" />
                        </div>
                        <div class="btn p-2 w-12 h-9 min-h-auto shadow">
                            <Icon icon="ph:share-network-duotone" class="text-xl" />
                        </div>
                    </div>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </div>
</template>



<script setup>

import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Icon } from '@iconify/vue';

defineOptions({ layout: AppLayout })

const props = defineProps({
    categoriaActiva: {default: ()=> ''},
    filtrado: {default: ()=> ''},
    listado: {
        default: () => { data: [] }
    },
    categorias: {
        default: () => []
    }
});

const listado = ref(props.listado);
const categorias = ref(props.categorias)

const playFile = ref(null)

function play(audio) {
    playFile.value = {
        src: audio.audio,
        title: audio.titulo,
        // artist: 'tseyor'
    }
    setTimeout(() => document.querySelector("#player").scrollIntoView({ behavior: "smooth", block: "end", inline: "nearest" }), 250)
}
</script>
