<template>
    <AppLayout title="Audios">
        <div class="py-12 w-full">

            <div class="container max-w-full flex justify-between items-center mb-20">
                <Back :href="route('biblioteca')">Biblioteca</Back>
                <AdminLinks modelo="audio" necesita="administrar contenidos" class="mb-3" />
            </div>

            <section class="container mx-auto">
                <h1>Audios</h1>
                <p>Relájate y disfruta de los talleres, meditaciones, cuentos y otros materiales de la filosofía de
                    Tseyor.</p>
            </section>

            <!--
    <AudioPlayer id="player" v-if="playFile" :music="playFile" class="mx-auto max-w-[700px]
    card shadow-lg bg-base-300 dark:bg-gray-dark-800 rounded-tl-xl sm:rounded-t-xl
    mb-7
    " />
-->

            <div class="flex justify-end mb-5 md:container md:mx-auto px-2">
                <SearchInput />
            </div>

            <div class="w-full flex gap-5 flex-wrap md:flex-nowrap md:container md:mx-auto px-2">

                <Categorias :categorias="categorias" :url="route('audios')" />

                <div class="w-full flex-grow">

                    <SearchResultsHeader :results="listado" />

                    <GridAppear v-if="listado.data.length > 0" class="gap-4 max-w-full"
                        :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                        <div v-for="audio in listado.data.map(a => ({ ...a, src: srcAudio(a) }))" :key="audio.id"
                            class="card flex-row shadow bg-base-100 p-4 items-center gap-2 sm:gap-4 lg:gap-6"
                            style="max-width: calc(100vw - 14px)">

                            <div v-if="audio.audio" class="btn p-0 w-12 h-5 min-h-auto text-3xl"
                                :class="player.music?.src == audio.src ? 'btn-secondary' : 'btn-primary'"
                                @click="clickPlayPause(audio)" :title="audio.src">
                                <AudioStateIcon :src="audio.src" />
                            </div>
                            <a target="_blank" v-else :href="audio.enlace" class="btn p-0 w-12 h-5 min-h-auto text-3xl"
                                title="abrir enlace">
                                <Icon icon="ph:arrow-up-right-duotone" />
                            </a>

                            <div class="flex flex-col gap-2 mr-auto w-full">
                                <Link :href="route('audio', audio.slug)"
                                    class="text-base font-bold my-0 leading-5 hover:underline w-fit">{{ audio.titulo }}
                                </Link>
                                <Link v-if="!categoriaActiva" :href="`${route('audios')}?categoria=${audio.categoria}`"
                                    class="text-xs w-fit hover:underline">{{ audio.categoria }}
                                </Link>
                            </div>
                        </div>
                    </GridAppear>

                    <pagination class="mt-6" :links="listado.links" />

                </div>

            </div>
        </div>
    </AppLayout>
</template>



<script setup>
import usePlayer from '@/Stores/player'
import { getSrcUrl } from '@/composables/srcutils.js'

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

const player = usePlayer()


function clickPlayPause(audio) {

    const titulo = audio.titulo

    if (player.music?.src == audio.src) {
        switch (player.state) {
            /*case 'error':
                player.play()
                break*/
            case 'waiting':
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

function srcAudio(a) {
    if (!a.audio) return a.enlace
    return getSrcUrl(a.audio)
}
</script>
