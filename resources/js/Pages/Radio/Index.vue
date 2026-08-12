<template>
    <Page>

        <PageHeader>
            <h1 class="hidden">Radio Tseyor</h1>

            <div class="flex justify-between items-center mb-20">
                <Back :href="route('biblioteca')">Biblioteca</Back>
                <div class="flex gap-2">
                    <Share />
                    <AdminLinks modelo="radio-item" necesita="administrar contenidos" />
                </div>
            </div>
        </PageHeader>

        <PageWide>

            <Hero title="" :srcImage="ui.theme.isDark ? darkLogo : lightLogo" srcWidth="1117" srcHeight="801"
                textClass="px-7 gap-4"
                imageSideClass="max-h-[360px]">

                <!-- Selector de emisoras -->
                <h3 class="text-center" v-if="!emisoraActiva">Elige una emisora:</h3>

                <div class="flex flex-wrap gap-3 justify-center w-full shrink-0">
                    <div v-for="emisora of emisoras" :key="emisora"
                        class="rounded-lg transition duration-200"
                        :class="emisoraActiva === emisora
                            ? 'border-primary border-b-4 font-bold'
                            : ''">
                        <button v-if="emisoraCargando === emisora"
                            class="p-4 block cursor-wait bg-base-100 border-spinner">
                            {{ emisora }}
                        </button>
                        <button v-else-if="emisoraActiva === emisora"
                            class="p-4 block cursor-default bg-base-100">
                            {{ emisora }}
                        </button>
                        <button v-else class="p-4 block bg-base-100 hover:bg-secondary hover:text-secondary-content"
                            @click="cambiarEmisora(emisora)">
                            {{ emisora }}
                        </button>
                    </div>
                </div>

                <!-- Player (solo cuando hay emisora activa) -->
                <div v-if="emisoraActiva" class="mt-16 my-8 p-4 sm:p-8 flex flex-col justify-center gap-5 lg:gap-10 card bg-base-100 shadow-xl">

                    <!-- Loading state -->
                    <div v-if="emisoraCargando && !estado" class="flex justify-center py-8">
                        <span class="loading loading-spinner loading-lg text-primary"></span>
                    </div>

                    <!-- Player content -->
                    <template v-else-if="estado">

                    <!-- Botón "Escuchar" cuando se cierra el reproductor -->
                    <div class="flex justify-center">
                        <div v-if="mostrarBotonEscuchar">
                            <button class="w-64 btn btn-primary btn-lg" @click="volverAEscuchar">
                                <Icon icon="ph:play-circle-duotone" class="mr-2 transform scale-200" />
                                Escuchar
                            </button>
                        </div>
                        <div v-else class="w-64 btn btn-lg text-lg border-opacity-0 text-center"
                            @click="player.playPause">
                            <div class="btn btn-secondary rounded-full flex justify-center items-center p-1 text-4xl transform scale-75">
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

                    <!-- Enlace al contenido original -->
                    <div v-if="estado.contenido_url" class="text-center">
                        <Link :href="estado.contenido_url" class="btn btn-sm btn-ghost gap-2">
                            <Icon icon="ph:book-open-duotone" />
                            Ver contenido original
                        </Link>
                    </div>

                    </template>
                </div>

            </Hero>

            <div v-if="error" class="alert alert-error">
                <span>{{ error }}</span>
            </div>

        </PageWide>

        <RadioInstallButton />

        <PageFooter>
            <Comentarios url="radio" />
        </PageFooter>

    </Page>
</template>


<script setup>
const ui = useUi()
const player = ui.player
player.autoplay = true

const { isDark } = useTheme()

const base = '/almacen/medios/logos/radio_tseyor'
const lightLogo = base + '.png'
const darkLogo = base + '_dark.png'

const selectors = useSelectors()

const props = defineProps({
    emisoras: {},
});

// Estado
const emisoraActiva = ref(selectors.emisoraRadio || null)
const emisoraCargando = ref(null)
const estado = ref(null)
const error = ref(null)
const mostrarBotonEscuchar = ref(false)
const audioEstabaPreviamenteAbierto = ref(false)

const music = computed(() => {
    if (!estado.value || !estado.value.audio_actual) return null
    return {
        src: estado.value.audio_actual.url,
        title: estado.value.audio_actual.titulo,
        artist: 'Radio Tseyor',
        startAt: estado.value.posicion_actual
    }
})

// Cargar emisora guardada al montar
onMounted(async () => {
    if (emisoraActiva.value) {
        await cargarEmisora(emisoraActiva.value)
    }
})

// Cambiar emisora (sin recargar página)
async function cambiarEmisora(emisora) {
    error.value = null
    emisoraCargando.value = emisora

    try {
        const response = await axios.get(route('radio.api', emisora))
        emisoraActiva.value = emisora
        emisoraCargando.value = null
        estado.value = response.data
        selectors.emisoraRadio = emisora

        // Actualizar URL sin recargar (history pushState)
        window.history.pushState({}, '', route('radio.emisora', emisora))

        // Reproducir
        if (music.value) {
            playItem(music.value)
        }

        // Escuchar cuando termine el audio para recargar siguiente
        player.audio.removeEventListener('ended', onAudioEnded)
        player.audio.addEventListener('ended', onAudioEnded)
    } catch (e) {
        emisoraCargando.value = null
        error.value = 'Error al cargar la emisora'
        console.error(e)
    }
}

// Cargar emisora (para emisora guardada)
async function cargarEmisora(emisora) {
    try {
        const response = await axios.get(route('radio.api', emisora))
        estado.value = response.data
        selectors.emisoraRadio = emisora

        if (music.value) {
            playItem(music.value)
        }

        player.audio.removeEventListener('ended', onAudioEnded)
        player.audio.addEventListener('ended', onAudioEnded)
    } catch (e) {
        error.value = 'Error al cargar la emisora'
        console.error(e)
    }
}

function onAudioEnded() {
    if (emisoraActiva.value) {
        cargarEmisora(emisoraActiva.value)
    }
}

function playItem(audio) {
    if (!audio) return

    if (player.music?.src == audio.src) {
        switch (player.state) {
            case 'waiting':
            case 'playing':
            case 'paused':
                break
            default:
                player.play(audio.src, audio.title, { artist: audio.artist, isRadio: true })
        }
    } else {
        player.play(audio.src, audio.title, { artist: audio.artist, isRadio: true })
    }

    if (Math.abs(player.audio.currentTime - audio.startAt) > 4)
        player.audio.currentTime = audio.startAt
}

function volverAEscuchar() {
    if (music.value) {
        playItem(music.value)
    }
    mostrarBotonEscuchar.value = false
    audioEstabaPreviamenteAbierto.value = true
}

// Watcher para detectar cuándo se cierra el reproductor
watch(() => player.audioClosed, (nuevoEstado, estadoAnterior) => {
    if (nuevoEstado === true && estadoAnterior === false && audioEstabaPreviamenteAbierto.value) {
        mostrarBotonEscuchar.value = true
    } else if (nuevoEstado === false) {
        mostrarBotonEscuchar.value = false
        audioEstabaPreviamenteAbierto.value = true
    }
})

onBeforeUnmount(() => {
    player.audio.removeEventListener('ended', onAudioEnded)
})

</script>

<style scoped>
.border-spinner {
    position: relative;
    z-index: 0;
    overflow: hidden;
}
.border-spinner::before {
    content: '';
    position: absolute;
    z-index: -2;
    left: -50%;
    top: -50%;
    width: 200%;
    height: 200%;
    background-repeat: no-repeat;
    background-size: 50% 50%;
    background-position: 0 0, 100% 0, 100% 100%, 0 100%;
    background-image:
        linear-gradient(var(--color-primary, #3b82f6), var(--color-secondary, #3b82f6)),
        linear-gradient(var(--color-secondary, #3b82f6), var(--color-primary, #3b82f6)),
        linear-gradient(var(--color-primary, #3b82f6), var(--color-info, #3b82f6)),
        linear-gradient(var(--color-info, #3b82f6), var(--color-primary, #3b82f6));
    animation: rotate-border 1s linear infinite;
}
.border-spinner::after {
    content: '';
    position: absolute;
    z-index: -1;
    left: 3px;
    top: 3px;
    width: calc(100% - 6px);
    height: calc(100% - 6px);
    background: inherit;
    border-radius: inherit;
}
@keyframes rotate-border {
    to { transform: rotate(1turn); }
}
</style>
