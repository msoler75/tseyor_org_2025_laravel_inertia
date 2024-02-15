<template>
    <div class="flex flex-wrap gap-3">
        <span v-for="audio of audios" :key="audio" class="btn btn-xs text-xs"
            :class="player.music?.src == audio.src?'btn-secondary':'btn-primary'"
            @click="clickPlay(audio)">
            <Icon :icon="player.music?.src == audio.src?'ph:speaker-simple-high-duotone':'ph:speaker-simple-low-duotone'" />
            <template v-if="player.music?.src == audio.src">
                <span v-if="numerados">
                    <span v-if="player.state == 'playing'">Escuchando</span>
                    <span v-else-if="player.state == 'paused'">En pausa</span>
                    <span v-else-if="player.state == 'error'">Error en</span>
                    <span v-else="player.state == 'paused'">Escuchaste</span> 
                </span>
                {{ numerados?'Audio '+audio.index:audio.filename }}
            </template>
            <template v-else>
                {{ numerados?'Escuchar Audio '+audio.index:audio.filename }}
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
    },
    numerados: {
        type: Boolean,
        required: false,
        default: false
    },
    titulo: {
        type: String, 
        required: false,
        default: ""
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
                player.play(audio.src, props.titulo + (props.numerados?` (${audio.index})`:'') )
        }
    }
    // nuevo audio
    else player.play(audio.src, props.titulo + (props.numerados?` (${audio.index})`:''))
}

</script>
