const player = reactive({
    audio: null,
    music: null,
    state: "stopped",
    radioMode: false,
    closed: true,
    mini: true,
    expanded: false,
    autoplay: true,
    duration: 0,
    currentTime: 0,

    init() {
      if (!window || !window.Audio) return;

      this.audio = new Audio();
      this.audio.preload = "auto";
      this.audio.autoplay = this.autoplay;

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
        stalled: this._handleStalled.bind(this)
      };

      Object.entries(events).forEach(([event, handler]) => {
        this.audio.addEventListener(event, handler);
      });
    },

    _determineState() {
      if (!this.audio) return "stopped";

      if (this.audio.error) return "error";
      if (this.audio.ended) return "ended";
      if (this.audio.paused) return "paused";
      if (this.audio.seeking) return "seeking";
      if (this.audio.readyState < this.audio.HAVE_FUTURE_DATA) return "loading";

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
      if (this.autoplay && (this.state === "stopped" || this.state === "loading")) {
        this.audio.play().catch(err => {
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
    },

    _handlePause(event) {
      this._updateState();
      this._logDevEvent("pause", event);
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

    isPlayable(src) {
      return /\.(mp3|mp4|ogg|wav|flac|aac|wma|aiff|amr|opus)$/i.test(src);
    },

    play(src, title, options = {}) {
      if (!this.audio) {
        console.error("Audio no inicializado");
        return;
      }

      const { artist, isRadio } = options;

      this.music = { src, title, artist };
      this.radioMode = !!isRadio;
      this.closed = false;

      try {
        this.audio.src = src;
        this.audio.play().catch(err => {
          console.error("Play failed:", err);
          this.state = "error";
        });
      } catch (err) {
        console.error("Play setup failed:", err);
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
      if (this.audio) {
        this.audio.pause();
        this.audio.currentTime = 0;
        this.music = null;
        this.state = "stopped";
        this.closed = true;
      }
    },

    stepForward(seconds = 30) {
        if (!this.audio) return;

        // Validaciones de seguridad
        const currentTime = this.audio.currentTime || 0;
        const duration = this.audio.duration || 0;

        // Verificar que los valores sean números finitos
        if (!Number.isFinite(currentTime) || !Number.isFinite(duration)) {
          console.warn('Invalid audio time values', { currentTime, duration });
          return;
        }

        const newTime = Math.min(currentTime + seconds, duration);

        // Otra validación adicional
        if (Number.isFinite(newTime)) {
          try {
            this.audio.currentTime = newTime;
          } catch (error) {
            console.error('Error setting currentTime:', error);
          }
        } else {
          console.warn('Calculated new time is not a finite number');
        }
      },

      // Mismo enfoque para stepBackward
      stepBackward(seconds = 30) {
        if (!this.audio) return;

        const currentTime = this.audio.currentTime || 0;
        const duration = this.audio.duration || 0;

        if (!Number.isFinite(currentTime) || !Number.isFinite(duration)) {
          console.warn('Invalid audio time values', { currentTime, duration });
          return;
        }

        const newTime = Math.max(currentTime - seconds, 0);

        if (Number.isFinite(newTime)) {
          try {
            this.audio.currentTime = newTime;
          } catch (error) {
            console.error('Error setting currentTime:', error);
          }
        } else {
          console.warn('Calculated new time is not a finite number');
        }
      }
  });

  export default function usePlayer() {
    return player;
  }

  // Initialize player on module load
  player.init();
