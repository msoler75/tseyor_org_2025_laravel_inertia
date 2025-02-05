import { webApiAvailable, AudioProcessor } from "./audioProcessor.js";

const player = reactive({
  url: null,
  audio: null,
  video: null,
  music: null,
  state: "stopped",
  radioMode: false,
  audioClosed: true,
  videoClosed: true,
  mini: true,
  expanded: false,
  autoplay: true,
  duration: 0,
  currentTime: 0,
  requiereInteraccion: false,
  isVideo: false,
  checkingVideo: false,
  audioProcessor: null,
  audioProcessingEnabled: true,

  init() {
    if (!window || !window.Audio) return;

    this.audio = new Audio();
    this.audio.preload = "auto";
    this.audio.autoplay = this.autoplay;

    if(false)
    if ( webApiAvailable()) {
      // La Web Audio API está disponible
      this.audioProcessor = new AudioProcessor();
      this.audioProcessor.setupAudioSource(this.audio);
      this.audioProcessor.startAGC();
      //this.audioProcessor.applyFixedGain(5);
    } else {
      console.warn(
        "Web Audio API no está disponible. Usando funcionalidades básicas de audio."
      );
    }

    // Attach event listeners with more comprehensive state management
    this._attachEventListeners();
  },

  _attachEventListeners() {
    const events = {
      loadedmetadata: this._handleLoadedMetadata.bind(this),
      canplay: this._handleCanPlay.bind(this),
      error: this._handleError.bind(this),
      timeupdate: this._handleTimeUpdate.bind(this),
      ended: this._handleEnded.bind(this),
      play: this._handlePlay.bind(this),
      pause: this._handlePause.bind(this),
      waiting: this._handleWaiting.bind(this),
      stalled: this._handleStalled.bind(this),
    };

    Object.entries(events).forEach(([event, handler]) => {
      this.audio.addEventListener(event, handler);
    });
  },

  _determineState() {
    if (this.checkingVideo) return "loading";
    const media = this.isVideo ? this.video : this.audio;
    if (!media) return "stopped";

    if (media.error) return "error";
    if (media.ended) return "ended";
    if (media.paused) return "paused";
    if (media.seeking) return "seeking";
    if (media.readyState < media.HAVE_FUTURE_DATA) return "loading";

    this.requiereInteraccion = false;
    return "playing";
  },

  _handleLoadedMetadata(event) {
    this.duration = this.audio.duration || 0;
    this.currentTime = this.audio.currentTime || 0;
    this._updateState();
    this._logDevEvent("loadedmetadata", event);
  },

  _handleCanPlay(event) {
    this._updateState();

    // Autoplay logic
    if (this.isVideo) return;
    if (
      this.autoplay &&
      (this.state === "stopped" || this.state === "loading")
    ) {
      this.audio.play().catch((err) => {
        console.error("Autoplay failed:", err);
        this.state = "error";
      });
    }

    this._logDevEvent("canplay", event);
  },

  _handleError(event) {
    this.state = "error";
    this._logDevEvent("error", event);
  },

  _handleTimeUpdate(event) {
    this.currentTime = this.audio.currentTime;
    this._updateState();
  },

  _handleEnded(event) {
    this.state = "ended";
    this._logDevEvent("ended", event);
  },

  _handlePlay(event) {
    this._updateState();
    this._logDevEvent("play", event);
    window.dispatchEvent(
      new CustomEvent("player-playing", {
        detail: { url: this.url, title: this.music.title },
      })
    );
  },

  _handlePause(event) {
    this._updateState();
    this._logDevEvent("pause", event);
    window.dispatchEvent(
      new CustomEvent("player-paused", {
        detail: { url: this.url },
      })
    );
  },

  _handleWaiting(event) {
    this.state = "loading";
    this._logDevEvent("waiting", event);
  },

  _handleStalled(event) {
    this.state = "error";
    this._logDevEvent("stalled", event);
  },

  _updateState() {
    this.state = this._determineState();
  },

  _logDevEvent(eventName, event) {
    if (process.env.NODE_ENV === "development") {
      console.log(eventName, event);
    }
  },

  // verifica si se puede reproducir
  isPlayable(src) {
    const audioFormats = "mp3|wav|ogg|oga|flac|aac|m4a|wma|aiff|amr|opus";
    const videoFormats = "mp4|webm|ogv|mov|avi|wmv|flv|mkv|m4v";
    const regex = new RegExp(`\\.(${audioFormats}|${videoFormats})$`, "i");
    return regex.test(src);
  },

  // Verifica si el archivo es de video
  async checkVideo(url) {
    if (!url.match(/\.(mp4|webm|mkv|mov|avi|wmv|flv|m4v)$/i)) return false;

    // Si fetch no está disponible, asumimos que es video
    if (!window.fetch) {
      console.warn(
        "fetch no está disponible, asumiendo que el archivo es de video"
      );
      return Promise.resolve(true);
    }

    const that = this;
    this.checkingVideo = true;
    return new Promise((resolve, reject) => {
      const video = document.createElement("video");
      let timeoutId;

      const cleanup = () => {
        clearTimeout(timeoutId);
        URL.revokeObjectURL(video.src);
      };

      fetch(url)
        .then((response) => response.blob())
        .then((blob) => {
          const videoUrl = URL.createObjectURL(blob);
          video.src = videoUrl;

          video.onloadedmetadata = () => {
            URL.revokeObjectURL(videoUrl);
            that.checkingVideo = false;
            video.pause();
            resolve(video.videoWidth > 0);
          };

          video.onerror = () => {
            URL.revokeObjectURL(videoUrl);
            console.error("Error al cargar el video");
            that.checkingVideo = false;
            resolve(false);
          };

          // Añadir timeout
          timeoutId = setTimeout(() => {
            cleanup();
            console.warn("Timeout al cargar los metadatos del video");
            that.checkingVideo = false;
            video.pause();
            resolve(true);
          }, 8000); // 8 segundos de timeout, ajusta según necesidades
        })
        .catch((error) => {
          console.error("Error al obtener el archivo:", error);
          that.checkingVideo = false;
          resolve(false);
        });
    });
  },

  async play(src, title, options = {}) {
    if (src.match(/\.(mp4|webm|ogv|mkv|mov|avi|wmv|flv|m4v)$/i)) {
      this.audio.pause();
      const hasVideo = await player.checkVideo(src);
      if (!hasVideo) {
        player.playAudio(src, title, options);
      } else {
        // lo abre en una nueva pestaña
        player.playVideo(src, title, options);
      }
      return;
    }
    // si es un audio:
    else if (player.isPlayable(src)) {
      player.playAudio(src, title, options);
    }
  },

  playVideo(src, title, options = {}) {
    /*if (!this.video) {
      console.error("Video no inicializado");
      return;
    }*/
    console.log("playVideo", src);

    if (!this.isVideo) this.close();
    this.autoplay = false;
    this.isVideo = true;
    this.videoClosed = false;

    // pausamos el video previo, si lo hubiera
    if (this.video && !this.video.paused) this.video.pause();

    // try {
    this.url = src;
    if (this.video) {
      this.video.src = src;
      this.video.play();
    }
    this._updateState();
  },

  playAudio(src, title, options = {}) {
    if (!this.audio) {
      console.error("Audio no inicializado");
      return;
    }

    console.log("playAudio", src);

    if (this.isVideo) this.close();
    this.isVideo = false;

    const { artist, isRadio } = options;

    this.music = { src, title, artist };
    this.radioMode = !!isRadio;
    this.audioClosed = false;

    try {
      this.url = src;
      this.audio.src = src;
      // Iniciar o reanudar el AudioContext aquí
      if (
        this.audioProcessor &&
        this.audioProcessor.audioContext.state === "suspended"
      ) {
        this.audioProcessor.audioContext.resume().then(() => {
          this.audio
            .play()
            .then(() => {
              //activamos el procesamiento de audio solo para la radio
              if (isRadio && !this.audioProcessingEnabled)
                this.toggleAudioProcessing();
              if (!isRadio && this.audioProcessingEnabled)
                this.toggleAudioProcessing();
            })
            .catch((err) => {
              if (err.name === "NotAllowedError") {
                console.warn(
                  "Error de reproducción automática: Se requiere interacción del usuario"
                );
                // Aquí puedes manejar específicamente este error
                this.state = "error";
                this.requiereInteraccion = true;
              } else {
                console.error("Error de reproducción:", err);
                this.state = "error";
              }
            });
        });
      } else
        this.audio
          .play()
          .then(() => {
            //activamos el procesamiento de audio solo para la radio
            if (isRadio && !this.audioProcessingEnabled)
              this.toggleAudioProcessing();
            if (!isRadio && this.audioProcessingEnabled)
              this.toggleAudioProcessing();
          })
          .catch((err) => {
            if (err.name === "NotAllowedError") {
              console.warn(
                "Error de reproducción automática: Se requiere interacción del usuario"
              );
              // Aquí puedes manejar específicamente este error
              this.state = "error";
              this.requiereInteraccion = true;
            } else {
              console.error("Error de reproducción:", err);
              this.state = "error";
            }
          });
    } catch (err) {
      console.error("Error en la configuración de reproducción:", err);
      this.state = "error";
    }
  },

  pause() {
    if (this.audio && !this.audio.paused) {
      this.audio.pause();
    }
  },

  playPause() {
    try {
      if (this.audio.paused) {
        this.audio.play();
      } else {
        this.audio.pause();
      }
    } catch (e) {
      console.error("playPause interrupted", e);
      this.state = "error";
    }
  },

  close() {
    if (this.isVideo) {
      if (this.video) {
        this.video.pause();
        this.video.currentTime = 0;
      }
      this.state = "stopped";
      this.videoClosed = true;
      this.isVideo = false;
    }
    if (this.audio) {
      this.audio.pause();
      this.audio.currentTime = 0;
      this.music = null;
      this.state = "stopped";
      this.audioClosed = true;
    }
  },

  stepForward(seconds = 30) {
    if (!this.audio) return;

    // Validaciones de seguridad
    const currentTime = this.audio.currentTime || 0;
    const duration = this.audio.duration || 0;

    // Verificar que los valores sean números finitos
    if (!Number.isFinite(currentTime) || !Number.isFinite(duration)) {
      console.warn("Invalid audio time values", { currentTime, duration });
      return;
    }

    const newTime = Math.min(currentTime + seconds, duration);

    // Otra validación adicional
    if (Number.isFinite(newTime)) {
      try {
        this.audio.currentTime = newTime;
      } catch (error) {
        console.error("Error setting currentTime:", error);
      }
    } else {
      console.warn("Calculated new time is not a finite number");
    }
  },

  // Mismo enfoque para stepBackward
  stepBackward(seconds = 30) {
    if (!this.audio) return;

    const currentTime = this.audio.currentTime || 0;
    const duration = this.audio.duration || 0;

    if (!Number.isFinite(currentTime) || !Number.isFinite(duration)) {
      console.warn("Invalid audio time values", { currentTime, duration });
      return;
    }

    const newTime = Math.max(currentTime - seconds, 0);

    if (Number.isFinite(newTime)) {
      try {
        this.audio.currentTime = newTime;
      } catch (error) {
        console.error("Error setting currentTime:", error);
      }
    } else {
      console.warn("Calculated new time is not a finite number");
    }
  },
  toggleAudioProcessing() {
    if (!this.audioProcessor) return;

    try {
      if (!this.audioProcessingEnabled) {
        this.audioProcessor.connectSource();
      } else {
        this.audioProcessor.disconnectSource();
      }
      this.audioProcessingEnabled = !this.audioProcessingEnabled;
      console.log(
        "Audio processing:",
        this.audioProcessingEnabled ? "ON" : "OFF"
      );
    } catch (error) {
      console.error("Error al togglear el procesamiento de audio:", error);
    }
  },
});

export default function usePlayer() {
  return player;
}

function installEvents() {
  // Escuchamos el evento que emitimos desde Blade
  window.addEventListener("player-play", (event) => {
    if (!event.detail) return;
    player.play(event.detail.url, event.detail.title);
  });

  // Escuchamos el evento que emitimos desde Blade
  window.addEventListener("player-continue", (event) => {
    player.playPause();
  });

  // Escuchamos el evento que emitimos desde Blade
  window.addEventListener("player-pause", (event) => {
    player.pause();
  });
}

// Initialize player on module load
player.init();

installEvents();

// activación/desactivación de procesamiento de audio por teclado
document.addEventListener("keydown", _handleKeyPress);

function _handleKeyPress(event) {
  // Combina Ctrl + Shift + P para alternar el procesamiento de audio
  if (event.altKey && event.shiftKey && event.key === "P") {
    player.toggleAudioProcessing();
    event.preventDefault(); // Evitar que la combinación de teclas haga otra cosa
  }
}
