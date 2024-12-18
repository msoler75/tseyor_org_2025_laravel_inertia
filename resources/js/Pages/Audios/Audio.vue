<template>

    <div class="container mx-auto px-4 py-8 mb-32">
        <div class="flex justify-between items-center mb-20">
            <Back>Audios</Back>
            <AdminLinks modelo="audio" necesita="administrar contenidos" :contenido="audio" />
        </div>

        <div class="flex flex-col items-center mb-20">
            <h1>{{ audio.titulo }}</h1>
            <p class="text-gray-600 text-sm mb-2">
                Última actualización:
                <TimeAgo :date="audio.updated_at" />
            </p>
            <p class="badge">{{ audio.categoria }}</p>
            <p>{{ audio.descripcion }}</p>

            <div v-if="audio.audio" class="btn px-4 h-5 min-h-auto flex gap-2"
                :class="player.music?.src == audio.src ? 'btn-secondary' : 'btn-primary'" @click="clickPlayPause(audio)"
                :title="audio.src">
                <AudioStateIcon :src="audio.src"  class="text-3xl"/>
                Reproducir
            </div>
            <a target="_blank" v-else :href="audio.enlace" class="btn px-4 h-5 min-h-auto btn-primary flex gap-2"
                title="abrir enlace">
                <Icon icon="ph:arrow-up-right-duotone"  class="text-3xl"/>
                Abrir enlace
            </a>
        </div>

        <Comentarios :url="route('audio', audio.id)" />
    </div>
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
