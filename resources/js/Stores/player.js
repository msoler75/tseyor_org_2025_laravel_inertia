const state = reactive({
  audio: null,
  music: null,
  state: "stopped",
  radioMode: false,
  closed: true,
  mini: true,
  autoplay: true,
  duration: 0,
  currentTime: 0,
  init() {
    console.log("player.init()...");
    if (!document) return;

    this.audio = new Audio();
    this.audio.autoplay = this.autoplay;
    this.audio.className = "hidden";

    this.audio.addEventListener("loadedmetadata", onLoadedMetadata);
    this.audio.addEventListener("canplay", onCanplay);
    this.audio.addEventListener("error", onError);
    this.audio.addEventListener("timeupdate", onTimeupdate);
    this.audio.addEventListener("ended", onEnded);
    this.audio.addEventListener("play", onPlay);
    this.audio.addEventListener("paused", onPaused);
    this.audio.addEventListener("waiting", onWaiting);
    this.audio.addEventListener("abort", onAbort);

    this.audio.preload = "auto";

    console.log("player.init() ended!");
  },
  isPlayable(src) {
    console.log("isPlayable?", src);
    return src.match(/\.(mp3|mp4|ogg|wav|flac|aac|wma|aiff|amr|opus)$/i);
  },
  play(src, title, options = {}) {
    if (!this.audio) {
      console.error("audio no inicializado");
      return;
    }
    const { artist, isRadio } = options;
    if (process.env.NODE_ENV === "development") console.log("player.play");
    this.music = { src, title, artist };
    this.radioMode = !!isRadio;
    this.closed = false;

    this.audio.src = src;
    this.audio.play();
  },
  pause() {
    this.audio.pause();
    this.state = "paused";
  },
  playPause() {
    try {
      if (this.audio.paused) {
        this.audio.play();
        this.state = "playing";
      } else {
        this.audio.pause();
        this.state = "paused";
      }
    } catch (e) {
      console.error("playPause interrupted", e);
      this.state = "error";
    }
  },
  close() {
    this.audio.pause();
    this.audio.src = null;
    this.music = null;
    this.state = "stopped";
    this.closed = true;
  },
  stepForward() {
    if (!this.audio.paused && !this.audio.ended) {
      this.audio.currentTime += 30;
    } else {
      this.audio.currentTime += 30;
    }
  },
  stepBackward() {
    this.audio.currentTime -= 30;
  },
});

function onError(ev) {
  if (process.env.NODE_ENV === "development") console.log("error", ev);
  state.state = "error";
}

function onLoadedMetadata(ev) {
  if (process.env.NODE_ENV === "development") console.log("loadedmetadata", ev);
}

function onAbort(ev) {
  if (process.env.NODE_ENV === "development") console.log("abort", ev);
}

function onCanplay(ev) {
  if (process.env.NODE_ENV === "development") console.log("canplay", ev);

  state.duration = state.audio.duration;
  state.currentTime = state.audio.currentTime;
  if (state.autoplay) state.audio.play();
}

function onPlay(ev) {
  if (process.env.NODE_ENV === "development") console.log("play", ev);
  state.state = "playing";
}

function onPaused(ev) {
  if (process.env.NODE_ENV === "development") console.log("paused", ev);
  state.state = "paused";
}

function onEnded(ev) {
  if (process.env.NODE_ENV === "development") console.log("ended", ev);
  state.state = "stopped";
}

function onTimeupdate(ev) {
  state.currentTime = state.audio.currentTime;
}

function onWaiting(ev) {
  if (process.env.NODE_ENV === "development") console.log("waiting", ev);
}

export default function usePlayer() {
  return state;
}
