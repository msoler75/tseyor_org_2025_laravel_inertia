<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back>Videos</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="video" necesita="administrar contenidos" :contenido="video" />
            </div>
        </div>
        </PageHeader>

        <PageWide>

        <div class="max-w-full lg:max-w-[640px] mx-auto">

            <div class="flex flex-col items-center">

                <h1>{{ video.titulo }}</h1>
                <p class="text-gray-600 text-sm mb-2">
                    Última actualización:
                    <TimeAgo :date="video.updated_at" />
                </p>
                <Prose class="my-7">
                    {{ video.descripcion }}
                </Prose>

                <div class="relative h-0 overflow-hidden max-w-full w-full" style="padding-bottom: 56.25%">
                    <iframe
                        :id="`youtube-${video.id}`"
                        :src="getEmbedYoutube(video.enlace) + '?enablejsapi=1'"
                        frameborder="0"
                        allowfullscreen
                        class="absolute top-0 left-0 w-full h-full"
                        :data-video-title="video.titulo"
                        :data-video-url="video.enlace">
                    </iframe>
                </div>
            </div>

        </div>
        </PageWide>
    </Page>
</template>

<script setup>
import { getEmbedYoutube } from '@/composables/srcutils.js'
import { onMounted, onUnmounted } from 'vue'
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js'

const { trackVideoPlay, trackDirectAccess } = useGoogleAnalytics()

const props = defineProps({
    video: {
        type: Object,
        required: true,
    },
})

// Variable para almacenar el player
let youtubePlayer = null

// Función que se ejecuta cuando la API de YouTube está lista
const onYouTubeIframeAPIReady = () => {
    if (typeof YT !== 'undefined' && YT.Player) {
        youtubePlayer = new YT.Player(`youtube-${props.video.id}`, {
            events: {
                'onStateChange': onPlayerStateChange
            }
        })
    }
}

// Función que se ejecuta cuando cambia el estado del player
const onPlayerStateChange = (event) => {
    // YT.PlayerState.PLAYING = 1
    if (event.data === 1) {
        console.log('🎬 Video YouTube iniciado:', props.video.titulo)
        trackVideoPlay(props.video.titulo, props.video.enlace)
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

    // Tracking de acceso directo/externo para videos
    trackDirectAccess('video', props.video.titulo)
})

onUnmounted(() => {
    // Limpiar player al desmontar
    if (youtubePlayer && typeof youtubePlayer.destroy === 'function') {
        youtubePlayer.destroy()
        youtubePlayer = null
    }
})

</script>
