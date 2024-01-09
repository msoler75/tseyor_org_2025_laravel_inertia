<template>
    <div class="flex gap-2">
        <span v-for="audio of audios" :key="audio" class="badge cursor-pointer gap-1 select-none"
            :class="player.music && player.music.src == audio.src?'badge-secondary':'badge-primary'"
            @click="clickPlay(audio)">
            <Icon icon="material-symbols:volume-up-outline-rounded" class="mr-2" />
            <template v-if="player.music && player.music.src == audio.src">
                <span v-if="player.state == 'playing'">Escuchando</span>
                <span v-else-if="player.state == 'paused'">En pausa</span>
                <span v-else-if="player.state == 'error'">Error en</span>
                <span v-else="player.state == 'paused'">Escuchaste</span>{{ audio.label }}
            </template>
            <template v-else>
                Escuchar {{ audio.label }}
            </template>
        </span>
    </div>
</template>

<script setup>
import { usePlayer } from '@/Stores/player'

const props = defineProps({
    audios: {
        type: Object,
        required: true,
    }
});

const player = usePlayer()

function clickPlay(audio) {
    if (player.music && player.music.src == audio.src) {
        switch (player.state) {
            /*case 'error':
                player.play()
                break*/
            case 'playing':
            case 'paused':
                player.playPause()
                break
            default:
                player.play(audio.src, audio.title)
        }
    }
    // nuevo audio
    else player.play(audio.src, audio.title)
}

</script>
