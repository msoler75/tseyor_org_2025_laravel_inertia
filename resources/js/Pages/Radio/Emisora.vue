<template>
    <Page>

        <PageHeader>
            <h1 class="hidden">Radio Tseyor - {{ estado.emisora }}</h1>
        <div class="flex justify-between items-center mb-20">
            <span/>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="radioitem" necesita="administrar contenidos" />
            </div>
        </div>
        </PageHeader>

         <PageWide>

        <Hero title="" :srcImage="isDark?'/almacen/medios/logos/radio_tseyor_dark.png':'/almacen/medios/logos/radio_tseyor.png'"
        class="py-8! lg:py-20!"
        textClass="lg:p-7 gap-4">
            <div class="flex flex-wrap gap-3 justify-center w-full shrink-0">
                <div v-for="emisora of emisoras" :key="emisora" class="bg-base-100 rounded-lg shadow-2xs overflow-hidden">
                    <div v-if="estado.emisora == emisora" class="border-primary border-b-4 p-4 font-bold">{{ emisora }}
                    </div>
                    <Link v-else class="p-4 block hover:bg-secondary hover:text-secondary-content" :href="route('radio.emisora', emisora)">{{ emisora }}</Link>
                </div>
            </div>

            <div class="mt-16 my-8 p-4 sm:p-8 flex flex-col justify-center gap-5 lg:gap-10 card bg-base-100 shadow-xl">

                <!-- Botón "Escuchar" cuando se cierra el reproductor -->
                <div class="flex justify-center">
                    <div v-if="mostrarBotonEscuchar" >
                        <button class="w-64 btn btn-primary btn-lg" @click="volverAEscuchar">
                            <Icon icon="ph:play-circle-duotone" class="mr-2 transform scale-200" />
                            Escuchar
                        </button>
                    </div>
                    <div v-else class="w-64 btn btn-lg text-lg border-opacity-0 text-center"
                    @click="player.playPause">
                        <div class="btn btn-secondary rounded-full flex justify-center items-center p-1 text-4xl transform scale-75"
                        >
                            <AudioStateIcon :src="player.music?.src" class="rounded-full overflow-hidden" />
                        </div>
                        <span class="w-32">
                            {{ player.state == 'paused' ? 'PAUSADO' : 'ESCUCHANDO' }}
                        </span>
                    </div>
                </div>

                <div class="text-center">
                    <h3>{{ music.title }}</h3>
                </div>

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


        </PageWide>


        <PageFooter>
            <Comentarios :url="route('radio.emisora', selectors.emisoraRadio)" />
        </PageFooter>

    </Page>
</template>


<script setup>
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
