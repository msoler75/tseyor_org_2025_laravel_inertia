<template>
    <Page>

        <div class="flex justify-between mb-20">
            <Back :href="route('biblioteca')">Biblioteca</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="audio" necesita="administrar contenidos"/>
            </div>
        </div>


        <TitleInfo title="Audios Tseyor">
            <p>Relájate y disfruta de los talleres, meditaciones, cuentos y otros materiales de la filosofía de
                Tseyor.</p>
                <p>Recuerda que también puedes acceder a la <Link href="/radio">Radio Tseyor</Link> y escuchar nuestra emisora de talleres, meditaciones y talleres cuentos, donde se reproducen estos audios de forma continua.</p>
            </TitleInfo>

        <!--
    <AudioPlayer id="player" v-if="playFile" :music="playFile" class="mx-auto max-w-[700px]
    card shadow-lg bg-base-300 dark:bg-gray-dark-800 rounded-tl-xl sm:rounded-t-xl
    mb-7
    " />
-->

        <ContentMain class="flex justify-end mb-5 md:container md:mx-auto px-2">
            <SearchInput placeholder="Buscar audios..." input-class="max-w-42"/>
        </ContentMain>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap md:container md:mx-auto px-2">

            <Categorias :categorias="categorias" :url="route('audios')" select-class="w-full rounded-sm"/>

            <div class="w-full grow">

                <SearchResultsHeader :results="listado"/>

                <GridAppear v-if="listado.data.length > 0" class="gap-4 max-w-full"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <div v-for="audio in listado.data.map(a => ({ ...a, src: srcAudio(a) }))" :key="audio.id"
                        class="card flex-row shadow-2xs bg-base-100 p-4 items-center gap-2 sm:gap-4 lg:gap-6"
                        style="max-width: calc(100vw - 14px)">

                        <div v-if="audio.audio" class="btn p-0 w-12 h-12 min-h-auto text-3xl"
                            :class="player.music?.src == audio.src ? 'btn-secondary' : 'btn-primary'"
                            @click="clickPlayPause(audio)" :title="audio.src">
                            <AudioStateIcon :src="audio.src" />
                        </div>
                        <a target="_blank" v-else :href="audio.enlace" class="btn p-0 w-12 h-12 min-h-auto text-3xl"
                            title="abrir enlace">
                            <Icon icon="ph:arrow-up-right-duotone" />
                        </a>

                        <div class="flex flex-col gap-2 mr-auto w-full">
                            <Link :href="route('audio', audio.slug)"
                                class="text-base font-bold my-0 leading-5 text-primary hover:text-secondary w-fit">{{ audio.titulo }}
                            </Link>
                            <Link v-if="!categoriaActiva" :href="`${route('audios')}?categoria=${audio.categoria}`"
                                class="text-xs w-fit hover:text-secondary">{{ audio.categoria }}
                            </Link>
                        </div>
                    </div>
                </GridAppear>

                <pagination class="mt-6" :links="listado.links" />

            </div>

        </div>
    </Page>
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
