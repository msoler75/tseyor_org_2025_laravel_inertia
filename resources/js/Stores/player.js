import { ref } from "vue";
import { createGlobalState } from "@vueuse/core";

export const usePlayer = createGlobalState(() => {
  // elemento HTML 5 de audio
  var audio = null

  // variables reactivas

  const music = ref(null);
  const state = ref("stopped");
  const radioMode = ref(false);
  const closed = ref(true);
  const mini = ref(true);
  const autoplay = ref(true)
  const duration = ref(0)
  const currentTime = ref(0)


  // variables computadas

  const src = computed(()=>audio.src)
  const title = computed(()=>music.value?music.value.title:null)
  const artist = computed(()=>music.value?music.value.artist:null)
  const audioElem = computed(()=>audio)

  /**
   * Métodos
   */

  // crea un objeto de audio HTML5
  function init() {
    if(!document) return

    audio =new Audio()
    audio.autoplay = autoplay.value; // Suponiendo que `autoplay` es una variable definida previamente
    audio.className = "hidden";

    audio.addEventListener("loadedmetadata", loadedmetadata);
    audio.addEventListener("canplay", canplay);
    audio.addEventListener("error", error);
    // audio.addEventListener("progress", progress);
    audio.addEventListener("timeupdate", timeupdate);
    audio.addEventListener("ended", ended);
    audio.addEventListener("play", onPlay);
    audio.addEventListener("paused", paused);
    audio.addEventListener("waiting", waiting);
    audio.addEventListener("abort", abort);

    audio.preload = "auto";
    // audio.currentTime = startTime; // Suponiendo que `startTime` es una variable definida previamente

    // Agregar el elemento audio al DOM
    // document.body.appendChild(audio);
  }

  function play(newMusic, isRadio) {
    console.log('player.play')
    music.value = newMusic;
    radioMode.value = !!isRadio;
    closed.value = false;


    audio.src = newMusic.src
    audio.play()
  }

  function pause() {
    audio.pause()
    state.value = 'paused'
  }

    // alterna entre play y pausa
  function playPause()
  {
    if(audio.paused)
    {
        audio.play()
        state.value = "playing";
    }
    else {
        audio.pause()
        state.value = "paused";
    }
  }

  function close() {
    audio.pause()
    state.value = 'stopped'
    closed.value = true
  }


  const stepForward = () => {
    if (!audio.paused && !audio.ended) {
        // audio.pause()
        audio.currentTime += 30;
        // audio.play()
    } else {
        audio.currentTime += 30;
    }
};

const stepBackward = () => {
    audio.currentTime -= 30;
};



  /**
   * Eventos
   */
  const abort = (ev) => {
    if (process.env.NODE_ENV === "development") console.log("abort", ev);
  };

  const error = (ev) => {
    if (process.env.NODE_ENV === "development") console.log("error", ev);
  };

  const loadedmetadata = (ev) => {
    if (process.env.NODE_ENV === "development")
      console.log("loadedmetadata", ev);
  };

  const canplay = (ev) => {
    if (process.env.NODE_ENV === "development") console.log("canplay", ev);

    duration.value = audio.duration;
    currentTime.value = audio.currentTime;
    if (autoplay.value) audio.play();
  };

  const onPlay = (ev) => {
    if (process.env.NODE_ENV === "development") console.log("play", ev);
    state.value = "playing";
  };

  const paused = (ev) => {
    if (process.env.NODE_ENV === "development") console.log("paused", ev);
    state.value = "paused";
  };

  const ended = (ev) => {
    if (process.env.NODE_ENV === "development") console.log("ended", ev);
    state.value = "stopped";
  };

  /* const progress = (ev) => {
    if (process.env.NODE_ENV === 'development') console.log("progress", ev);
    // duration.value = audio.value ? audio.value.duration : 0;
}; */

  const timeupdate = (ev) => {
    // if (process.env.NODE_ENV === 'development') console.log("timeupdate", ev);
    currentTime.value = audio.currentTime;
  };

  const waiting = (ev) => {
    if (process.env.NODE_ENV === "development") console.log("waiting", ev);
  };

  return { music, audioElem, state, radioMode, closed, mini, duration, currentTime, src,title, artist, init, play, pause, playPause, close, stepForward, stepBackward };
});
