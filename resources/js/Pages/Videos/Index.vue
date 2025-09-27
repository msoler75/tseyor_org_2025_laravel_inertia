<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between mb-20">
            <Back :href="route('biblioteca')">Biblioteca</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="video" necesita="administrar contenidos"  />
            </div>
        </div>

        <section class="py-10 space-y-10 mx-auto">
            <h1 class="text-4xl font-bold text-center mb-10">Vídeos TSEYOR</h1>
            <div class="text-lg text-center">Visita nuestro canal de Youtube @tseyor</div>
            <div
                class="flex flex-wrap justify-center items-center gap-10 card flex-row bg-base-100 shadow-2xs rounded-lg p-12 w-fit mx-auto">
                <a href="https://www.youtube.com/@tseyor" target="_blank"
                    class="text-blue-600 hover:text-blue-800 font-bold text-2xl">youtube.com/@tseyor</a>
                <a href="https://www.youtube.com/@tseyor" target="_blank">
                    <Image src="/almacen/medios/logos/youtube_big_logo.svg" alt="Logo de Youtube"
                        src-width="1000" src-height="223"
                        class="h-12 mx-auto dark:mix-blend-exclusion" />
                </a>
            </div>
        </section>

        </PageHeader>

        <PageWide>

            <ContentMain class="flex justify-end mb-5">
                <SearchInput />
            </ContentMain>

            <GridAppear class="mt-2 grid-cols-1 lg:grid-cols-2 gap-7">
                <div v-for="video in listado.data" :key="video.id"
                    class="card px-5 py-2 h-full flex flex-col items-center gap-3 hover:bg-base-200/40 rounded-xl w-full">
                    <div class="relative h-0 overflow-hidden max-w-full w-full" style="padding-bottom: 56.25%">
                        <iframe
                            :id="`youtube-${video.id}`"
                            :src="getEmbedYoutube(video.enlace) + '?enablejsapi=1'"
                            frameborder="0"
                            allowfullscreen
                            class="absolute top-0 left-0 w-full h-full bg-gray-800"
                            :data-video-title="video.titulo"
                            :data-video-url="video.enlace">
                        </iframe>
                    </div>
                    <Link :href="route('video', video.slug)"
                        class="text-primary hover:text-secondary transition-color duration-200  text-xl font-bold"
                        v-html="video.titulo">
                    </Link>
                    <div class="text-gradient opacity-75 transition duration-300 group-hover:opacity-90 text-sm text-ellipsis overflow-hidden "
                        v-html="video.descripcion" />
                </div>
            </GridAppear>

            <pagination class="mt-6" :links="listado.links" />
        </PageWide>
    </Page>
</template>


<script setup>
import { getEmbedYoutube } from '@/composables/srcutils.js'
import { onMounted, onUnmounted } from 'vue'
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js'

const { trackVideoPlay } = useGoogleAnalytics()

const props = defineProps({
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
    /* categoriaActiva: { default: () => '' },
    categorias: {
        default: () => []
    }*/
});

// Variable global para almacenar los players
let youtubePlayers = []

// Función que se ejecuta cuando la API de YouTube está lista
const onYouTubeIframeAPIReady = () => {
    // Inicializar players para cada video
    props.listado.data.forEach(video => {
        if (typeof YT !== 'undefined' && YT.Player) {
            const player = new YT.Player(`youtube-${video.id}`, {
                events: {
                    'onStateChange': (event) => onPlayerStateChange(event, video)
                }
            })
            youtubePlayers.push(player)
        }
    })
}

// Función que se ejecuta cuando cambia el estado del player
const onPlayerStateChange = (event, video) => {
    // YT.PlayerState.PLAYING = 1
    if (event.data === 1) {
        console.log('🎬 Video YouTube iniciado:', video.titulo)
        trackVideoPlay(video.titulo, video.enlace)
    }
}

// Cargar la API de YouTube
const loadYouTubeAPI = () => {
    // Si ya está cargada, inicializar directamente
    if (typeof YT !== 'undefined' && YT.Player) {
        onYouTubeIframeAPIReady()
        return
    }

    // Si ya existe el script, esperar a que cargue
    if (document.querySelector('script[src*="youtube"]')) {
        window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady
        return
    }

    // Cargar script de YouTube API
    const script = document.createElement('script')
    script.src = 'https://www.youtube.com/iframe_api'
    script.async = true
    document.head.appendChild(script)

    // Configurar callback global
    window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady
}

onMounted(() => {
    // Cargar API de YouTube cuando el componente se monta
    loadYouTubeAPI()
})

onUnmounted(() => {
    // Limpiar players al desmontar
    youtubePlayers.forEach(player => {
        if (player && typeof player.destroy === 'function') {
            player.destroy()
        }
    })
    youtubePlayers = []
})

</script>
