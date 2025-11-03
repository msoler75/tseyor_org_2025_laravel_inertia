<template>
    <Page>

        <PageHeader>
            <div class="flex justify-between items-center mb-20">
                <Back>Audios</Back>
                <div class="flex gap-2">
                    <Share/>
                    <AdminLinks modelo="audio" necesita="administrar contenidos" :contenido="audio" />
                </div>
            </div>
        </PageHeader>

        <PageContent class="max-w-[80ch] py-12 flex gap-10 md:rounded-2xl">

        <div class="flex flex-col items-center mb-20">
            <h1>{{ audio.titulo }}</h1>
            <p class="text-gray-600 text-sm mb-2">
                Última actualización:
                <TimeAgo :date="audio.updated_at" />
            </p>
            <div class="my-4 flex items-center gap-4">
                <Favorito coleccion="audios" :id="audio.id" :inicial="audio.favorito" @change="audioData.favorito = $event" />
                <span class="badge">{{ audio.categoria }}</span>
            </div>
            <p>{{ audio.descripcion }}</p>

            <div v-if="audio.audio" class="btn pl-2 pr-4 py-2 min-h-auto flex items-center gap-2 w-40"
                :class="player.music?.src == audio.src ? 'btn-secondary' : 'btn-primary'" @click="clickPlayPause(audio)"
                :title="audio.src">
                <AudioStateIcon :src="audio.src" class="text-3xl flex-shrink-0"/>
                <span class="flex-1 text-center">
                {{
                    player.music?.src == audio.src
                        ? player.state == 'playing'
                            ? 'Pausar'
                            : player.state == 'error'
                                ? 'Error'
                                : 'Reproducir'
                        : 'Reproducir'
                }}
                </span>
            </div>
            <a target="_blank" v-else :href="audio.enlace" class="btn pl-2 pr-4 py-2 min-h-auto btn-primary flex gap-2"
                title="abrir enlace">
                <Icon icon="ph:arrow-up-right-duotone"  class="text-3xl"/>
                Abrir enlace
            </a>
        </div>

        </PageContent>

        <PageFooter>
            <Comentarios :url="route('audio', audio.id)" />
        </PageFooter>
    </Page>
</template>

<script setup>
import usePlayer from '@/Stores/player'
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js'

const { trackDirectAccess } = useGoogleAnalytics()
const player = usePlayer()

const props = defineProps({
    audio: {
        type: Object,
        required: true,
    },
})

const audioData = ref({ ...props.audio })

const audio = computed(() => ({ ...audioData.value, src: srcAudio(audioData.value) }))

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

onMounted(() => {
    // Tracking de acceso directo/externo para audios
    trackDirectAccess('audio', props.audio.titulo)
})
</script>
