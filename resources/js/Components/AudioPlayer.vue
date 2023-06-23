<template>
    <div class="select-none">
      <audio
        :autoplay="autoplay"
        :src="src"
        ref="audio"
        class="hidden"
        @loadedmetadata="loadedmetadata"
        @canplay="canplay"
        @error="error"
        @progress="progress"
        @timeupdate="timeupdate"
        @ended="ended"
        @play="play"
        @paused="paused"
        @waiting="waiting"
        @abort="abort"
        preload="auto"
      />
      <div
        class="p-5 pb-2 lg:p-7 lg:pb-3"
      >
        <div
          class="flex items-center space-x-3.5 sm:space-x-5 lg:space-x-3.5 xl:space-x-5"
        >
          <div class="min-w-0 flex-auto">
            <!-- <p class="text-lime-600 dark:text-lime-400 text-sm sm:text-base lg:text-sm xl:text-base font-semibold uppercase">
          <abbr title="Episode">Ep.</abbr> 128
        </p> -->
            <h2
              class="text-black dark:text-white text-base sm:text-xl lg:text-base xl:text-xl font-semibold truncate"
            >
              {{ title }}
            </h2>
            <p
              class="text-gray-500 dark:text-gray-400 text-base sm:text-lg lg:text-base xl:text-lg font-medium"
            >
              {{ artist }}
            </p>
          </div>
        </div>
        <div class="space-y-2">
          <div class="relative overflow-hidden">
            <progress
              :max="duration"
              :value="currentTime"
              ref="progress"
              class="w-full h-6 rounded-md"
            />
          </div>
          <div
            class="text-gray-500 dark:text-gray-400 flex justify-between text-sm font-medium tabular-nums"
          >
            <div>{{ formatTime(currentTime) }}</div>
            <div>{{ formatTime(duration) }}</div>
          </div>
        </div>
      </div>
      <div
        class=" text-black dark:bg-gray-900 dark:text-white lg:rounded-b-xl pb-6 px-1 sm:px-3 lg:px-1 xl:px-3 flex justify-between items-center"
      >
        <button
          type="button"
          class="w-12 mx-auto border border-gray-400 rounded-md text-sm font-medium py-0.5 px-2 text-gray-500 dark:border-gray-600 dark:text-gray-400"
          @click="changePlaySpeed"
        >
          {{ this.speeds[this.playSpeedIdx].label }}x
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
        <button type="button" class="mx-auto" @click="stepBackward">
          <svg width="34" height="39" fill="none">
            <path
              d="M12.878 26.12c1.781 0 3.09-1.066 3.085-2.515.004-1.104-.665-1.896-1.824-2.075v-.068c.912-.235 1.505-.95 1.5-1.93.005-1.283-1.048-2.379-2.727-2.379-1.602 0-2.89.968-2.932 2.387h1.274c.03-.801.784-1.287 1.64-1.287.892 0 1.475.541 1.471 1.346.004.844-.673 1.398-1.64 1.398h-.738v1.074h.737c1.21 0 1.91.614 1.91 1.491 0 .848-.738 1.424-1.765 1.424-.946 0-1.683-.486-1.734-1.262H9.797c.055 1.424 1.317 2.395 3.08 2.395zm7.734.025c2.016 0 3.196-1.645 3.196-4.504 0-2.838-1.197-4.488-3.196-4.488-2.003 0-3.196 1.645-3.2 4.488 0 2.855 1.18 4.5 3.2 4.504zm0-1.138c-1.18 0-1.892-1.185-1.892-3.366.004-2.174.716-3.371 1.892-3.371 1.172 0 1.888 1.197 1.888 3.37 0 2.182-.712 3.367-1.888 3.367z"
              fill="currentColor"
            />
            <path
              d="M1 22c0 8.837 7.163 16 16 16s16-7.163 16-16S25.837 6 17 6"
              stroke="currentColor"
              stroke-width="1.5"
            />
            <path d="M17 0L9 6l8 6V0z" fill="currentColor" />
          </svg>
        </button>
        <button
          type="button"
          class="btn btn-primary mx-auto rounded-full  w-12 h-12 flex justify-center items-center"
          @click="playPauseBtn"
        >
          <Icon :icon="playStateIcon" class="text-3xl transform scale-150" />
        </button>
        <button type="button" class="mx-auto" @click="stepForward">
          <svg width="34" height="39" fill="none">
            <path
              d="M12.878 26.12c1.781 0 3.09-1.066 3.085-2.515.004-1.104-.665-1.896-1.824-2.075v-.068c.912-.235 1.505-.95 1.5-1.93.005-1.283-1.048-2.379-2.727-2.379-1.602 0-2.89.968-2.932 2.387h1.274c.03-.801.784-1.287 1.64-1.287.892 0 1.475.541 1.471 1.346.004.844-.673 1.398-1.64 1.398h-.738v1.074h.737c1.21 0 1.91.614 1.91 1.491 0 .848-.738 1.424-1.765 1.424-.946 0-1.683-.486-1.734-1.262H9.797c.055 1.424 1.317 2.395 3.08 2.395zm7.734.025c2.016 0 3.196-1.645 3.196-4.504 0-2.838-1.197-4.488-3.196-4.488-2.003 0-3.196 1.645-3.2 4.488 0 2.855 1.18 4.5 3.2 4.504zm0-1.138c-1.18 0-1.892-1.185-1.892-3.366.004-2.174.716-3.371 1.892-3.371 1.172 0 1.888 1.197 1.888 3.37 0 2.182-.712 3.367-1.888 3.367z"
              fill="currentColor"
            />
            <path
              d="M33 22c0 8.837-7.163 16-16 16S1 30.837 1 22 8.163 6 17 6"
              stroke="currentColor"
              stroke-width="1.5"
            />
            <path d="M17 0l8 6-8 6V0z" fill="currentColor" />
          </svg>
        </button>
        <!--
    <button type="button" class="hidden sm:block lg:hidden xl:block mx-auto">
      <svg width="17" height="18" viewBox="0 0 17 18" fill="none">
        <path d="M17 0H15V18H17V0Z" fill="currentColor" />
        <path d="M13 9L0 0V18L13 9Z" fill="currentColor" />
      </svg>
    </button>
    -->

        <a download target="_blank" :href="src" class="mx-auto flex justify-center items-center text-3xl" title="Descargar audio">
          <Icon icon="ph:download-duotone" />
        </a>
      </div>
    </div>
  </template>

  <script>

  export default {
    props: {
      music: {
        type: Object,
        required: true
      },
      autoplay: {
        type: Boolean,
        required: false,
        default: true
      }
    },
    data() {
      return {
        audio: null,
        progressEl: null,
        speeds: [
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
        ],
        playSpeedIdx: 2,
        icons: {
          stopped: "ph:play-duotone",
          paused: "ph:play-pause-duotone",
          playing: "ph:pause-duotone"
        },
        playState: "stopped",
        currentTimeVar: 0,
        durationVar: 0
      };
    },
    computed: {
      playStateIcon() {
        return this.icons[this.playState];
      },
      src() {
        return this.music && this.music.src ? this.music.src : "";
      },
      currentTime() {
        // return this.$refs.audio?this.$refs.audio.currentTime:0
        return this.currentTimeVar;
      },
      duration() {
        // return this.$refs.audio?this.$refs.audio.duration:0
        return this.durationVar;
      },
      title() {
        return this.music && this.music.title ? this.music.title : "";
      },
      artist() {
        return this.music && this.music.artist ? this.music.artist : "";
      }
    },
    watch: {
      src(value) {}
    },
    mounted() {
      this.audio = this.$refs.audio;
      this.progressEl = this.$refs.progress;
      const that = this;
      this.progressEl.onclick = e => {
        const sliderLeft = e.offsetX;
        const width = that.progressEl
          ? that.progressEl.width || that.progressEl.offsetWidth
          : 0.000001;
        const tempPercent = sliderLeft / width;
        that.audio.currentTime = tempPercent * that.audio.duration;
        that.currentTimeVar = tempPercent * that.audio.duration;
      };
    },
    methods: {
      // EVENTS
      abort(ev) {
        if(process.env.NODE_ENV === 'development') console.log("abort", ev);
        this.$emit("abort", ev);
      },
      error(ev) {
        if(process.env.NODE_ENV === 'development') console.log("error", ev);
        this.$emit("error", ev);
      },
      loadedmetadata(ev) {
        if(process.env.NODE_ENV === 'development') console.log("loadedmetadata", ev);
      },
      canplay(ev) {
        if(process.env.NODE_ENV === 'development') console.log("canplay", ev);
        this.$emit("canplay", ev);
        this.durationVar = this.audio.duration;
        this.currentTimeVar = this.audio.currentTime;
        if (this.autoplay) this.audio.play();
      },
      play(ev) {
        if(process.env.NODE_ENV === 'development') console.log("play", ev);
        this.$emit("play", ev);
        this.playState = "playing";
      },
      paused(ev) {
        if(process.env.NODE_ENV === 'development') console.log("paused", ev);
        this.$emit("paused", ev);
        this.playState = "paused";
      },
      ended(ev) {
        if(process.env.NODE_ENV === 'development') console.log("ended", ev);
        this.$emit("ended", ev);
        this.playState = "stopped";
      },
      progress(ev) {
        if(process.env.NODE_ENV === 'development') console.log("progress", ev);
        this.$emit("progress", ev);
        this.durationVar = this.$refs.audio ? this.$refs.audio.duration : 0;
      },
      timeupdate(ev) {
        if(process.env.NODE_ENV === 'development') console.log("timeupdate", ev);
        this.$emit("timeupdate", ev);
        this.currentTimeVar = this.audio.currentTime;
      },
      waiting(ev) {
        if(process.env.NODE_ENV === 'development') console.log("waiting", ev);
        this.$emit("waiting", ev);
      },
      // ACTIONS
      playPauseBtn() {
        if (this.audio.paused) {
          this.audio.play();
          this.playState = "playing";
        } else {
          this.audio.pause();
          this.playState = "paused";
        }
      },
      stepForward() {
        if (this.playState === "playing") {
          //this.audio.pause()
          this.audio.currentTime += 30;
          // this.audio.play()
        } else this.audio.currentTime += 30;
      },
      stepBackward() {
        this.audio.currentTime -= 30;
      },
      changePlaySpeed() {
        this.playSpeedIdx = (this.playSpeedIdx + 1) % this.speeds.length;
        this.audio.playbackRate = this.speeds[this.playSpeedIdx].speed;
      },
      // FORMAT
      formatTime(ts) {
        var hours = Math.floor(ts / 60 / 60);
        var minutes = Math.floor(ts / 60) - hours * 60;
        var seconds = Math.floor(ts % 60);
        return hours > 0
          ? hours.toString().padStart(2, "0") +
              ":" +
              minutes.toString().padStart(2, "0") +
              ":" +
              seconds.toString().padStart(2, "0")
          : minutes.toString().padStart(2, "0") +
              ":" +
              seconds.toString().padStart(2, "0");
      }
    }
  };
  </script>

  <style scoped>
  progress[value]::-webkit-progress-value {
    background-image: -webkit-linear-gradient(
        -45deg,
        transparent 33%,
        rgba(0, 0, 0, 0.1) 33%,
        rgba(0, 0, 0, 0.1) 66%,
        transparent 66%
      ),
      -webkit-linear-gradient(top, rgba(255, 255, 255, 0.25), rgba(0, 0, 0, 0.25)),
      -webkit-linear-gradient(left, #09c, #f44);

    border-radius: 3px;
    background-size: 70px 40px, 100% 100%, 100% 100%;
  }
  </style>
