<template>
    <Page class="text-center">

        <div class="flex justify-between items-center mb-20">
            <span/>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="radioitem" necesita="administrar contenidos" />
            </div>
        </div>

        isDark::{{ isDark }}
        <Hero title="" :srcImage="isDark?'/almacen/medios/logos/radio_tseyor_dark.png':'/almacen/medios/logos/radio_tseyor.png'"
        class="py-8! lg:py-20!"
        textClass="p-7 gap-4">
            <div class="flex flex-wrap gap-3 justify-center w-full shrink-0">
                <div v-for="emisora of emisoras" :key="emisora" class="bg-base-100 rounded-lg shadow-2xs">
                    <div v-if="estado.emisora == emisora" class="border-primary border-b-4 p-4 font-bold">{{ emisora }}
                    </div>
                    <Link v-else class="p-4 block" :href="route('radio.emisora', emisora)">{{ emisora }}</Link>
                </div>
            </div>

            <!-- Botón "Escuchar" cuando se cierra el reproductor -->
            <div class="mt-16 mb-8 flex justify-center">
                <div v-if="mostrarBotonEscuchar" >
                    <button class="btn btn-primary btn-lg" @click="volverAEscuchar">
                        <Icon icon="ph:play-circle-duotone" class="mr-2 transform scale-200" />
                        Escuchar
                    </button>
                </div>
                <div v-else class="btn btn-lg text-lg border-opacity-0 text-center">Escuchando:</div>
            </div>

            <div class="mb-24 text-center">
                <h3 class="min-h-[4rem]">{{ music.title }}</h3>
            </div>

        </Hero>

        <div v-if="error" class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ error }}</span>
        </div>



        <Comentarios :url="route('radio.emisora', selectors.emisoraRadio)" />

    </Page>
</template>


<script setup>
import { router } from '@inertiajs/vue3';

import usePlayer from '@/Stores/player'

import useSelectors from '@/Stores/selectors'
import { useTheme } from '@/Stores/theme'

const { isDark } = useTheme();

const selectors = useSelectors()

const player = usePlayer()

// Estado para controlar cuándo mostrar el botón "Escuchar"
const mostrarBotonEscuchar = ref(false)
const audioEstabaPreviamenteAbierto = ref(false)


const props = defineProps({
    estado: {},
    emisoras: {},
    error: {}
});

selectors.emisoraRadio = props.estado.emisora

const music = computed(() => {
    return {
        src: props.estado.audio_actual.url,
        title: props.estado.audio_actual.titulo,
        artist: 'Radio Tseyor',
        startAt: props.estado.posicion_actual
    }
})

function recargar() {
    router.reload({
        only: ['estado'],
        onSuccess: () => {
            playItem(music.value)
        }
    })
}

function volverAEscuchar() {
    playItem(music.value)
    mostrarBotonEscuchar.value = false
    audioEstabaPreviamenteAbierto.value = true
}

onMounted(() => {
    player.audio.addEventListener('ended', recargar)
    playItem(music.value)
    audioEstabaPreviamenteAbierto.value = !player.audioClosed
})

onBeforeUnmount(() => {
    player.audio.removeEventListener('ended', recargar)
})

// Watcher para detectar cuándo se cierra el reproductor de audio
watch(() => player.audioClosed, (nuevoEstado, estadoAnterior) => {
    // Si el audio se cerró y anteriormente estaba abierto, mostrar el botón "Escuchar"
    if (nuevoEstado === true && estadoAnterior === false && audioEstabaPreviamenteAbierto.value) {
        mostrarBotonEscuchar.value = true
    }
    // Si el audio se abre, ocultar el botón "Escuchar"
    else if (nuevoEstado === false) {
        mostrarBotonEscuchar.value = false
        audioEstabaPreviamenteAbierto.value = true
    }
})


function playItem(audio) {
    const titulo = audio.title

    if (player.music?.src == audio.src) {
        switch (player.state) {
            /*case 'error':
                player.play()
                break*/
            case 'waiting':
            case 'playing':
            case 'paused':
                // player.playPause()
                break
            default:
                player.play(audio.src, titulo, { artist: audio.categoria })
        }
    }
    // nuevo audio
    else player.play(audio.src, titulo)

    if (Math.abs(player.audio.currentTime - audio.startAt) > 4)
        player.audio.currentTime = audio.startAt
}



</script>
