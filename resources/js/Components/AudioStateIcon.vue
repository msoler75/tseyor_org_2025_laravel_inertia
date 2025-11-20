<template>
    <span v-if="player.music?.src == src" class="w-10 flex justify-center">
        <Icon v-show="player.state == 'stopped' || player.state == 'ended' " icon="ph:play-duotone" />
        <Icon v-show="player.state == 'paused'" icon="ph:play-pause-duotone" />
        <!-- <Spinner v-show="player.state == 'loading'" /> -->
        <img v-show="player.state == 'playing'" style="width: 1em" src="/almacen/medios/iconos/wave.gif"
            class="mix-blend-color-burn">
        <Icon v-show="player.state == 'error'" icon="ph:warning-circle-duotone" />
        <!-- <Icon v-show="!['stopped', 'paused', 'loading', 'playing', 'error'].includes(player.state)"
            icon="ph:question-mark" /> -->
        <Spinner v-show="!['stopped', 'paused', 'playing', 'error', 'ended'].includes(player.state)" />
    </span>
    <span v-else class="w-10 flex justify-center">
        <Icon icon="ph:play-duotone" />
    </span>
</template>


<script setup>
import usePlayer from '@/Stores/player'

const player = usePlayer();

const props = defineProps({
    src: String
})
</script>
