<template>
    <div class="container py-12 mx-auto">

        <div class="flex justify-between items-center mb-20">
            <Back :href="route('biblioteca')">Biblioteca</Back>
            <AdminPanel modelo="audio" necesita="administrar contenidos" class="mb-3" />
        </div>

        <h1>Audios</h1>
        <p>Relájate y disfruta de los talleres, meditaciones, cuentos y otros materiales de la filosofía de Tseyor.</p>

        <!--
    <AudioPlayer id="player" v-if="playFile" :music="playFile" class="mx-auto max-w-[700px]
    card shadow-lg bg-base-300 dark:bg-gray-dark-800 rounded-tl-xl sm:rounded-t-xl
    mb-7
    " />
-->

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


            <div
                class="card bg-base-100 shadow self-baseline flex-wrap flex-row md:flex-col p-5 lg:p-10 gap-4 sticky top-20">
                <Link :href="`${route('audios')}`"
                    :class="!filtrado && !categoriaActiva ? 'text-primary font-bold' : ''">
                <span class="capitalize">Novedades</span>
                </Link>

                <div v-for="categoria of categorias" :key="categoria.nombre" class="flex gap-2"
                    :class="categoriaActiva == categoria.nombre ? 'text-primary font-bold' : ''">
                    <Link :href="`${route('audios')}?categoria=${categoria.nombre}`">
                        <span class="capitalize">{{ categoria.nombre }}</span>
                        <small v-if="categoria.total > 0">({{ categoria.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="w-full flex-grow">

                <SearchResultsHeader :results="listado" />

                <GridAppear v-if="listado.data.length > 0" class="gap-4 max-w-full"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(32rem, 1fr))` }">
                    <div v-for="audio in listado.data.map(a=>({...a, src: '/almacen/'+a.audio}))" :key="audio.id"
                        class="card flex-row shadow bg-base-100 p-4 items-center gap-2 sm:gap-4 lg:gap-6"
                        style="max-width: calc(100vw - 30px)">

                        <div v-if="player.music?.src != audio.src" @click="clickPlay(audio)" class="btn btn-primary p-0 w-12 h-5 min-h-auto text-lg">
                            <Icon icon="ph:play-duotone"/>
                        </div>
                        <div v-else @click="player.playPause()" class="btn btn-secondary p-0 w-12 h-5 min-h-auto text-2xl"
                        :title="player.state == 'error'?'Error al cargar el audio':''">
                            <Icon v-show="player.state == 'stopped'" icon="ph:play-duotone"  />
                            <Icon v-show="player.state == 'paused'" icon="ph:play-pause-duotone" />
                            <Icon v-show="player.state == 'playing'" icon="ph:pause-duotone" />
                            <Icon v-show="player.state == 'error'" icon="ph:warning-circle-duotone"/>
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
                </GridAppear>

                <pagination class="mt-6" :links="listado.links" />

            </div>

        </div>
    </div>
</template>



<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'

import { usePlayer } from '@/Stores/player'

defineOptions({ layout: AppLayout })


const props = defineProps({
    categoriaActiva: { default: () => '' },
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
    categorias: {
        default: () => []
    }
});

const listado = ref(props.listado);
const categorias = ref(props.categorias)

const player = usePlayer()


function clickPlay(audio) {

    const titulo = audio.titulo
    const src = '/almacen/'+audio.audio
    console.log({audio})
    console.log({src})

    if (player.music && player.music.src == src) {
        switch (player.state) {
            /*case 'error':
                player.play()
                break*/
            case 'playing':
            case 'paused':
                player.playPause()
                break
            default:
                player.play(src, titulo, { artist: audio.categoria })
        }
    }
    // nuevo audio
    else player.play(src, titulo)
}
</script>
