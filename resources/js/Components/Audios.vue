<template>
    <div class="flex flex-wrap gap-3">
        <a v-for="audio of audios" :key="audio" class="btn btn-xs flex-nowrap text-xs"
        :href="audio.src"
            :class="player.music?.src == audio.src ? 'btn-secondary' : 'btn-primary'" @click.prevent="clickPlayPause(audio)"
            :title="player.music?.src == audio.src ? frase : 'Escuchar'">

            <AudioStateIcon :src="audio.src" />

            <template v-if="player.music?.src == audio.src">
                <template v-if="numerados">{{ frase }}</template>
                {{ numerados ? 'Audio ' + audio.index : audio.filename }}
            </template>
            <template v-else>
                {{ numerados ? 'Escuchar Audio ' + audio.index : audio.filename }}
            </template>
        </a>
    </div>
</template>

<script setup>
import usePlayer from '@/Stores/player'

const props = defineProps({
    audios: {
        type: Array,
        required: false,
        default: () => []
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

function clickPlayPause(audio) {

    //let titulo =  audio.title || audio.filename || audio.label
    //if(!titulo)
    let titulo = !props.titulo ? audio.label : props.titulo + (props.numerados ? ` (${audio.index})` : '')

    if(!titulo )
    titulo = audio.filename

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
                player.play(audio.src, titulo)
        }
    }
    // nuevo audio
    else player.play(audio.src, titulo)
}

const frase = computed(() => {
    if (player.state == 'playing') return 'Escuchando'
    if (player.state == 'paused') return 'En pausa'
    if (player.state == 'error') return 'Error en el audio'
    return 'Escuchaste'
})


</script>
