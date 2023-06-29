
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Radio Tseyor</h1>

        <AudioPlayer :music="music" @ended="recargar" />

    </div>
</template>


<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    estado: {}
});

const music = computed(() => {
    return {
        src: props.estado.audio_actual,
        title: props.estado.audio_actual.substr(props.estado.audio_actual.lastIndexOf('/') + 1),
        artist: 'Radio Tseyor',
        startAt: props.estado.tiempo_sistema - props.estado.arranco_en
    }
})

function recargar() {
    router.reload({
        only: ['estado']
    })
}


</script>

