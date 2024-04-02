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
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <div v-for="audio in listado.data.map(a => ({ ...a, src: a.audio.match(/^http/) ? a.audio : '/almacen/' + a.audio }))"
                        :key="audio.id"
                        class="card flex-row shadow bg-base-100 p-4 items-center gap-2 sm:gap-4 lg:gap-6"
                        style="max-width: calc(100vw - 30px)">

                        <div class="btn p-0 w-12 h-5 min-h-auto text-3xl"
                            :class="player.music?.src == audio.src ? 'btn-secondary' : 'btn-primary'"
                            @click="clickPlayPause(audio)">
                            <AudioStateIcon :src="audio.src"  />
                        </div>

                        <div class="flex flex-col gap-2 mr-auto w-full">
                            <Link :href="route('audio', audio.slug)"
                                class="text-base font-bold my-0 leading-5 hover:underline">{{ audio.titulo }}
                            </Link>
                            <Link v-if="!categoriaActiva" :href="`${route('audios')}?categoria=${audio.categoria}`"
                                class="text-xs">{{ audio.categoria }}
                            </Link>
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


function clickPlayPause(audio) {

    const titulo = audio.titulo

    if (player.music?.src == audio.src) {
        switch (player.state) {
            /*case 'error':
                player.play()
                break*/
            case 'playing':
            case 'paused':
                player.playPause()
                break
            default:
                player.play(audio.src, titulo, { artist: audio.categoria })
        }
    }
    // nuevo audio
    else player.play(audio.src, titulo)
}
</script>
