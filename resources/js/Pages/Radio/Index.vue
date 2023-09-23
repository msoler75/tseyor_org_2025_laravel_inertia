
<template>
    <div class="container mx-auto py-20">
        <h1>Radio Tseyor</h1>

        <div v-if="error" class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ error }}</span>
        </div>

        <AudioPlayer v-else :music="music" @ended="recargar" class="card bg-base-100 shadow w-[600px]" />

    </div>
</template>


<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'

import { usePlayer } from '@/Stores/player'

const player = usePlayer()

defineOptions({ layout: AppLayout })

const props = defineProps({
    estado: {},
    error: {}
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

