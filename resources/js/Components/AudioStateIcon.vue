<template>
    <div v-if="player.music?.src == src" >
        <Icon v-show="player.state == 'stopped'" icon="ph:play-duotone" />
        <Icon v-show="player.state == 'paused'" icon="ph:play-pause-duotone" />
        <img v-show="player.state == 'playing'" style="width: 1em" src="/almacen/medios/iconos/wave.gif"
            class="mix-blend-color-burn">
        <Icon v-show="player.state == 'error'" icon="ph:warning-circle-duotone" />
    </div>
    <div v-else>
        <Icon icon="ph:play-duotone" />
    </div>
</template>


<script setup>
import { usePlayer } from '@/Stores/player'

const player = usePlayer();

const props = defineProps({
    src: String
})
</script>
