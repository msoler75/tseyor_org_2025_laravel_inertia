
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Radio Tseyor</h1>


        <AudioPlayer :music="music" />

    </div>
</template>


<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    estado: {}
});

const music = computed(() => {
    return {
        src: props.estado.audio_actual,
        title: 'ererer',
        artist: 'Radio Tseyor',
        startAt: props.estado.tiempo_sistema - props.estado.arranco_en
    }
})
</script>

