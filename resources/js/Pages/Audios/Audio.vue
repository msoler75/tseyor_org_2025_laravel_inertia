<template>
    <Page>

        <div class="flex justify-between items-center mb-20">
            <Back>Audios</Back>
            <div class="flex gap-2">
                <Share/>
                <AdminLinks modelo="audio" necesita="administrar contenidos" :contenido="audio" />
            </div>
        </div>

        <div class="flex flex-col items-center mb-20">
            <h1>{{ audio.titulo }}</h1>
            <p class="text-gray-600 text-sm mb-2">
                Última actualización:
                <TimeAgo :date="audio.updated_at" />
            </p>
            <p class="badge">{{ audio.categoria }}</p>
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

        <Comentarios :url="route('audio', audio.id)" />
    </Page>
</template>

<script setup>
import usePlayer from '@/Stores/player'

const player = usePlayer()

const props = defineProps({
    audio: {
        type: Object,
        required: true,
    },
})

const audio = computed(() => ({ ...props.audio, src: srcAudio(props.audio) }))

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
