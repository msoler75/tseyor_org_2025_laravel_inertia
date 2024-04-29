const player = reactive({
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
    console.log(
      "play. state:",
      this.state,
      "audio:",
      this.audio.paused ? "paused" : this.audio.playing ? "playing" : this.audio
    );
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
    console.log(
      "playPause. state:",
      this.state,
      "audio:",
      this.audio.paused ? "paused" : this.audio.playing ? "playing" : this.audio
    );
    try {
      if (this.state == "waiting") {
        this.audio.pause();
        this.state = "paused";
      } else if (this.audio.paused || this.state=="waiting") {
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
    // this.audio.src = null;
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
  player.state = "error";
}

function onLoadedMetadata(ev) {
  if (process.env.NODE_ENV === "development") console.log("loadedmetadata", ev);
}

function onAbort(ev) {
  if (process.env.NODE_ENV === "development") console.log("abort", ev);
}

function onCanplay(ev) {
  if (process.env.NODE_ENV === "development") console.log("canplay", ev);

  console.log(
    "onCanPlay. state:",
    player.state,
    "audio:",
    player.audio.paused ? "paused" : player.audio.playing ? "playing" : player.audio
  );


  // player.state = "canplay";
  player.duration = player.audio.duration;
  player.currentTime = player.audio.currentTime;
  /*if (player.autoplay) {
    player.state = "playing";
    player.audio.play();
  }*/

  if(player.state == 'playing' || player.state =='waiting') {
    console.log('go continue playing')
    player.audio.play();
    player.state = "playing";
    }
  else if (player.state!='paused' && player.autoplay) {
    console.log('go Autoplay')
    player.audio.play();
    player.state = "playing";
    }

}


function onPlay(ev) {
  if (process.env.NODE_ENV === "development") console.log("play", ev);
  player.state = "playing";
}

function onPaused(ev) {
  if (process.env.NODE_ENV === "development") console.log("paused", ev);
  player.state = "paused";
}

function onEnded(ev) {
  if (process.env.NODE_ENV === "development") console.log("ended", ev);
  player.state = "stopped";
}

function onTimeupdate(ev) {
  player.currentTime = player.audio.currentTime;
}

function onWaiting(ev) {
  if (process.env.NODE_ENV === "development") console.log("waiting", ev);
  player.state = "waiting";
}

export default function usePlayer() {
  return player;
}

console.log("player.init");
player.init();
