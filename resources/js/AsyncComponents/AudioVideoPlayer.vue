<template>
    <div class="audio-player select-none">

        <Modal modal-class="video-player select-none" :show="!player.videoClosed" centered
            max-width="3xl">
            <video ref="myvideo" :width="width" :height="height" autoplay controls>
                <source :src="player.url">
            </video>
            <div class="flex justify-center items-center p-5">
                <button class="btn btn-sm btn-primary" @click="player.close()">Cerrar</button>
            </div>
        </Modal>


        <div v-if="player.mini" v-show="!player.audioClosed" @mouseleave="collapsePlayer"
        @mousemove="activatePlayer"
            class="max-w-[24rem] xs:max-w-[32rem] sm:max-w-[42rem] rounded-tl-3xl fixed bottom-0 right-0 z-50 bg-base-100 border-gray-400 dark:border-white border-t border-l overflow-hidden">
            <div v-if="player.expanded" class="p-2 xs:hidden">
                <TextAnimation :text="player.music?.title + (player.music?.artist ? ' ' + player.music.artist : '')"
                    class="ml-1 transform duration-300" />
            </div>
            <div v-if="player.expanded" class="p-2 pt-0 xs:pt-2">
                <input type="range" min="0" :max="player.duration"
                    v-model="player.currentTime" class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer"
                    @input="onSeek" ref="progressEl" />
            </div>
            <div class="mx-auto flex justify-between items-center" :class="[player.expanded ? 'w-full gap-3' : 'pr-4',
            player.requiereInteraccion ? 'pointer-events-none' : ''
            ]">
                <button type="button"
                    class="btn btn-secondary rounded-full flex justify-center items-center p-1 text-4xl transform scale-75"
                    :class="player.expanded ? '' : 'mr-1'" @click="player.playPause"
                    :title="player.state == 'error' ? 'Error al cargar el audio' : ''">
                    <AudioStateIcon :src="player.music?.src" class="rounded-full overflow-hidden" />
                </button>

                <TextAnimation :text="player.music?.title + (player.music?.artist ? ' ' + player.music.artist : '')"
                    class="hidden xs:block transform duration-300" :class="player.expanded ? '' : 'w-0'"
                     />

                <div class="flex justify-end gap-1 min-w-34 font-mono transform scale-y-150"
                    >
                    <span>{{ formatTime(player.currentTime) }}</span>
                    /
                    <span>{{ formatTime(player.duration) }}</span>
                </div>


                <button type="button" @click="player.stepBackward(30)" class="transform scale-75 duration-300"
                    :class="player.expanded ? 'w-[34px] ml-auto' : 'w-0 overflow-hidden'"
                    title="Retroceder 30 segundos">
                    <svg width="34" height="39" fill="none">
                        <path
                            d="M12.878 26.12c1.781 0 3.09-1.066 3.085-2.515.004-1.104-.665-1.896-1.824-2.075v-.068c.912-.235 1.505-.95 1.5-1.93.005-1.283-1.048-2.379-2.727-2.379-1.602 0-2.89.968-2.932 2.387h1.274c.03-.801.784-1.287 1.64-1.287.892 0 1.475.541 1.471 1.346.004.844-.673 1.398-1.64 1.398h-.738v1.074h.737c1.21 0 1.91.614 1.91 1.491 0 .848-.738 1.424-1.765 1.424-.946 0-1.683-.486-1.734-1.262H9.797c.055 1.424 1.317 2.395 3.08 2.395zm7.734.025c2.016 0 3.196-1.645 3.196-4.504 0-2.838-1.197-4.488-3.196-4.488-2.003 0-3.196 1.645-3.2 4.488 0 2.855 1.18 4.5 3.2 4.504zm0-1.138c-1.18 0-1.892-1.185-1.892-3.366.004-2.174.716-3.371 1.892-3.371 1.172 0 1.888 1.197 1.888 3.37 0 2.182-.712 3.367-1.888 3.367z"
                            fill="currentColor" />
                        <path d="M1 22c0 8.837 7.163 16 16 16s16-7.163 16-16S25.837 6 17 6" stroke="currentColor"
                            stroke-width="1.5" />
                        <path d="M17 0L9 6l8 6V0z" fill="currentColor" />
                    </svg>
                </button>

                <button type="button" @click="player.stepForward(30)" class="transform scale-75 duration-300"
                    :class="player.expanded ? 'w-[34px] ml-auto' : 'w-0 overflow-hidden'"
                    title="Avanzar 30 segundos">
                    <svg width="34" height="39" fill="none">
                        <path
                            d="M12.878 26.12c1.781 0 3.09-1.066 3.085-2.515.004-1.104-.665-1.896-1.824-2.075v-.068c.912-.235 1.505-.95 1.5-1.93.005-1.283-1.048-2.379-2.727-2.379-1.602 0-2.89.968-2.932 2.387h1.274c.03-.801.784-1.287 1.64-1.287.892 0 1.475.541 1.471 1.346.004.844-.673 1.398-1.64 1.398h-.738v1.074h.737c1.21 0 1.91.614 1.91 1.491 0 .848-.738 1.424-1.765 1.424-.946 0-1.683-.486-1.734-1.262H9.797c.055 1.424 1.317 2.395 3.08 2.395zm7.734.025c2.016 0 3.196-1.645 3.196-4.504 0-2.838-1.197-4.488-3.196-4.488-2.003 0-3.196 1.645-3.2 4.488 0 2.855 1.18 4.5 3.2 4.504zm0-1.138c-1.18 0-1.892-1.185-1.892-3.366.004-2.174.716-3.371 1.892-3.371 1.172 0 1.888 1.197 1.888 3.37 0 2.182-.712 3.367-1.888 3.367z"
                            fill="currentColor" />
                        <path d="M33 22c0 8.837-7.163 16-16 16S1 30.837 1 22 8.163 6 17 6" stroke="currentColor"
                            stroke-width="1.5" />
                        <path d="M17 0l8 6-8 6V0z" fill="currentColor" />
                    </svg>
                </button>

                <a download target="_blank" :href="player.music?.src" title="Descargar audio"
                    class="text-2xl transform duration-300"
                    :class="player.expanded ? 'w-[34px] ml-auto' : 'w-0 overflow-hidden'" >
                    <Icon icon="ph:download-duotone" />
                </a>

                <span title="Cerrar">
                    <Icon icon="ph:x-bold" v-if="player.expanded" class="text-3xl cursor-pointer pr-2"
                        @click="player.close()" />
                </span>

            </div>
        </div>
        <div v-show="!player.mini">
            <div class="p-5 pb-2 lg:p-7 lg:pb-3">
                <div class="flex items-center space-x-3.5 sm:space-x-5 lg:space-x-3.5 xl:space-x-5">
                    <div class="min-w-0 flex-auto">
                        <!-- <p class="text-lime-600 dark:text-lime-400 text-sm sm:text-base lg:text-sm xl:text-base font-semibold uppercase">
          <abbr title="Episode">Ep.</abbr> 128
        </p> -->
                        <p
                            class="text-black dark:text-white text-base sm:text-xl lg:text-base xl:text-xl font-semibold truncate">
                            {{ player.title }}
                        </p>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-base sm:text-lg lg:text-base xl:text-lg font-medium">
                            {{ player.artist }}
                        </p>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="relative overflow-hidden">
                        <progress :max="player.duration" :value="player.currentTime" class="w-full h-6 rounded-md"
                            @click="progressClickHandler" ref="progressEl" />
                    </div>
                    <div class="text-gray-500 dark:text-gray-400 flex justify-between text-sm font-medium tabular-nums">
                        <div>{{ formatTime(player.currentTime) }}</div>
                        <div>{{ formatTime(player.duration) }}</div>
                    </div>
                </div>
            </div>
            <div class="lg:rounded-b-xl pb-6 px-1 sm:px-3 lg:px-1 xl:px-3 flex justify-between items-center">
                <button type="button"
                    class="w-12 mx-auto border border-gray-400 rounded-md text-sm font-medium py-0.5 px-2 text-gray-500 dark:border-gray-600 dark:text-gray-400"
                    @click="changePlaySpeed">
                    {{ speeds[playSpeedIdx].label }}x
                </button>
                <!--
    <button type="button" class="mx-auto">
      <svg width="24" height="24" fill="none">
        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
      </svg>
    </button>
    -->
                <!--
    <button type="button" class="hidden sm:block lg:hidden xl:block mx-auto">
      <svg width="17" height="18">
        <path d="M0 0h2v18H0V0zM4 9l13-9v18L4 9z" fill="currentColor" />
      </svg>
    </button>
    -->
                <button type="button" class="mx-auto" @click="player.stepBackward(30)">
                    <svg width="34" height="39" fill="none">
                        <path
                            d="M12.878 26.12c1.781 0 3.09-1.066 3.085-2.515.004-1.104-.665-1.896-1.824-2.075v-.068c.912-.235 1.505-.95 1.5-1.93.005-1.283-1.048-2.379-2.727-2.379-1.602 0-2.89.968-2.932 2.387h1.274c.03-.801.784-1.287 1.64-1.287.892 0 1.475.541 1.471 1.346.004.844-.673 1.398-1.64 1.398h-.738v1.074h.737c1.21 0 1.91.614 1.91 1.491 0 .848-.738 1.424-1.765 1.424-.946 0-1.683-.486-1.734-1.262H9.797c.055 1.424 1.317 2.395 3.08 2.395zm7.734.025c2.016 0 3.196-1.645 3.196-4.504 0-2.838-1.197-4.488-3.196-4.488-2.003 0-3.196 1.645-3.2 4.488 0 2.855 1.18 4.5 3.2 4.504zm0-1.138c-1.18 0-1.892-1.185-1.892-3.366.004-2.174.716-3.371 1.892-3.371 1.172 0 1.888 1.197 1.888 3.37 0 2.182-.712 3.367-1.888 3.367z"
                            fill="currentColor" />
                        <path d="M1 22c0 8.837 7.163 16 16 16s16-7.163 16-16S25.837 6 17 6" stroke="currentColor"
                            stroke-width="1.5" />
                        <path d="M17 0L9 6l8 6V0z" fill="currentColor" />
                    </svg>
                </button>
                <button type="button"
                    class="btn btn-primary mx-auto rounded-full  w-12 h-12 flex justify-center items-center"
                    @click="player.playPause">
                    <Icon v-show="player.state == 'stopped'" icon="ph:play-duotone" class="transform scale-150" />
                    <Icon v-show="player.state == 'paused'" icon="ph:play-pause-duotone" class="transform scale-150" />
                    <Icon v-show="player.state == 'playing'" icon="ph:pause-duotone" class="transform scale-150" />
                </button>
                <button type="button" class="mx-auto" @click="player.stepForward(30)">
                    <svg width="34" height="39" fill="none">
                        <path
                            d="M12.878 26.12c1.781 0 3.09-1.066 3.085-2.515.004-1.104-.665-1.896-1.824-2.075v-.068c.912-.235 1.505-.95 1.5-1.93.005-1.283-1.048-2.379-2.727-2.379-1.602 0-2.89.968-2.932 2.387h1.274c.03-.801.784-1.287 1.64-1.287.892 0 1.475.541 1.471 1.346.004.844-.673 1.398-1.64 1.398h-.738v1.074h.737c1.21 0 1.91.614 1.91 1.491 0 .848-.738 1.424-1.765 1.424-.946 0-1.683-.486-1.734-1.262H9.797c.055 1.424 1.317 2.395 3.08 2.395zm7.734.025c2.016 0 3.196-1.645 3.196-4.504 0-2.838-1.197-4.488-3.196-4.488-2.003 0-3.196 1.645-3.2 4.488 0 2.855 1.18 4.5 3.2 4.504zm0-1.138c-1.18 0-1.892-1.185-1.892-3.366.004-2.174.716-3.371 1.892-3.371 1.172 0 1.888 1.197 1.888 3.37 0 2.182-.712 3.367-1.888 3.367z"
                            fill="currentColor" />
                        <path d="M33 22c0 8.837-7.163 16-16 16S1 30.837 1 22 8.163 6 17 6" stroke="currentColor"
                            stroke-width="1.5" />
                        <path d="M17 0l8 6-8 6V0z" fill="currentColor" />
                    </svg>
                </button>
                <a download target="_blank" :href="player.music?.src"
                    class="mx-auto flex justify-center items-center text-3xl" title="Descargar audio">
                    <Icon icon="ph:download-duotone" />
                </a>
            </div>
        </div>
    </div>
</template>


<script setup>
import usePlayer from '@/Stores/player'

const player = usePlayer()
const myvideo = ref(null)
const width = ref(1080)
const height = ref(768)

// expansión de audioplayer

var timerToCollapse = null

function activatePlayer() {
    player.expanded = true
    clearTimeout(timerToCollapse)
    timerToCollapse = setTimeout(() => {
        player.expanded = false
    }, 10000)
}

function collapsePlayer() {
    clearTimeout(timerToCollapse)
    timerToCollapse = setTimeout(() => {
        player.expanded = false
    }, 7000)
}


// velocidades

const speeds = [
    {
        label: "0.5",
        speed: 0.5
    },
    {
        label: "0.75",
        speed: 0.75
    },
    {
        label: "1.0",
        speed: 1
    },
    {
        label: "1.25",
        speed: 1
    },
    {
        label: "1.5",
        speed: 1.5
    },
    {
        label: "1.75",
        speed: 1.75
    },
    {
        label: "2.0",
        speed: 2
    }
];
const playSpeedIdx = ref(2);
// const startTime = computed(() => props.music && props.music.startAt ? props.music.startAt : 0);

const progressEl = ref(null)
const progressClickHandler = (e) => {
    const sliderLeft = e.offsetX;
    const width = progressEl.value ? progressEl.value.width || progressEl.value.offsetWidth : 0.000001;
    const tempPercent = sliderLeft / width;
    player.audioElem.value.currentTime = tempPercent * player.duration.value;
};

const changePlaySpeed = () => {
    playSpeedIdx.value = (playSpeedIdx.value + 1) % speeds.length;
    audio.value.playbackRate = speeds[playSpeedIdx.value].speed;
};

const formatTime = (ts) => {
    var hours = Math.floor(ts / 60 / 60);
    var minutes = Math.floor(ts / 60) - hours * 60;
    var seconds = Math.floor(ts % 60);
    return hours > 0
        ? hours.toString().padStart(2, "0") +
        ":" +
        minutes.toString().padStart(2, "0") +
        ":" +
        seconds.toString().padStart(2, "0")
        : minutes.toString().padStart(2, "0") + ":" + seconds.toString().padStart(2, "0");
};

onMounted(() => {
    console.log('AudioPlayer Mounted')
    player.video = myvideo.value
    width.value=Math.min(1080, screen.width)
    height.value=Math.min(768, screen.height)
})


var habilitarRange = false
watch(()=>player.expanded, (expanded) => {
    if (expanded) {
        habilitarRange = false
        setTimeout(() => { // para evitar misclicks al expandir el reproductor
            habilitarRange = true
        }, 1000)
    }
})

function onSeek(e) {
    activatePlayer()
    if(!habilitarRange) return
    player.seek(Number(e.target.value))
}
</script>

<style scoped>
progress[value]::-webkit-progress-value {
    background-image: -webkit-linear-gradient(-45deg,
            transparent 33%,
            rgba(0, 0, 0, 0.1) 33%,
            rgba(0, 0, 0, 0.1) 66%,
            transparent 66%),
        -webkit-linear-gradient(top, rgba(255, 255, 255, 0.25), rgba(0, 0, 0, 0.25)),
        -webkit-linear-gradient(left, #09c, #f44);

    border-radius: 3px;
    background-size: 70px 40px, 100% 100%, 100% 100%;
}

@media screen and (max-width: 470px) {}
</style>
