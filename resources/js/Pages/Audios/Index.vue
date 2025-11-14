<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between mb-20">
            <Back :href="route('biblioteca')">Biblioteca</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="audio" necesita="administrar contenidos"/>
            </div>
        </div>


        <TitleInfo title="Audios Tseyor">
            <div>Relájate y disfruta de los talleres, meditaciones, cuentos y otros materiales de la filosofía de
                Tseyor.</div>
            <div>Recuerda que también puedes acceder a la <Link href="/radio">Radio Tseyor</Link> y escuchar nuestra emisora de talleres, meditaciones y talleres cuentos, donde se reproducen estos audios de forma continua.</div>
        </TitleInfo>

        <!--
    <AudioPlayer id="player" v-if="playFile" :music="playFile" class="mx-auto max-w-[700px]
    card shadow-lg bg-base-300 dark:bg-gray-dark-800 rounded-tl-xl sm:rounded-t-xl
    mb-7
    " />
-->
<div class="px-2 flex justify-end">
    <SearchInput placeholder="Buscar audios..."/>
</div>

</PageHeader>

<PageWide>

        <ScrollToHere class="mt-6 w-full flex gap-5 flex-wrap md:flex-nowrap">

            <Categorias :categorias="categorias" :url="route('audios')" :favoritos="authenticated" select-class="w-full rounded-sm"/>

            <div class="w-full grow">

                <SearchResultsHeader :results="listado"/>

                <GridAppear v-if="listado.data.length > 0" class="gap-4 max-w-full"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(min(24rem, 100%), 1fr))` }">
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
                            <div class="flex justify-between">
                                <Link v-if="!categoriaActiva||categoriaActiva=='Favoritos'" :href="`${route('audios')}?categoria=${audio.categoria}`"
                                class="text-xs w-fit hover:text-secondary">{{ audio.categoria }}
                                </Link>
                                <span v-else></span>
                                <Favorito coleccion="audios" :id="audio.id" :inicial="audio.favorito" @change="updateFavorito(audio, $event)" />
                            </div>
                        </div>
                    </div>
                </GridAppear>

                <pagination class="mt-6" :links="listado.links" />

            </div>

        </ScrollToHere>
        </PageWide>
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

const authenticated = computed(() => !!usePage().props.auth?.user)

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

function updateFavorito(audio, nuevoEstado) {
    audio.favorito = nuevoEstado;
}
</script>
