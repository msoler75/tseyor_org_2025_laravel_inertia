// audioProcessor.js

export class AudioProcessor {

    constructor() {
        if (!webApiAvailable()) {
            console.warn("Web Audio API no está disponible. Usando funcionalidades básicas de audio.");
            return;
        }
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();

        if (!this.audioContext) {
            console.warn("No se ha podido inicializar el procesamiento de audio");
            return;
        }

        this.source = null;
        this.compressor = this.audioContext.createDynamicsCompressor();
        this.gainNode = this.audioContext.createGain();
        this.analyser = this.audioContext.createAnalyser();
        this.isProcessingEnabled = true;

        // Configurar el compresor
        this.compressor.threshold.setValueAtTime(-24, this.audioContext.currentTime);
        this.compressor.knee.setValueAtTime(30, this.audioContext.currentTime);
        this.compressor.ratio.setValueAtTime(12, this.audioContext.currentTime);
        this.compressor.attack.setValueAtTime(0.003, this.audioContext.currentTime);
        this.compressor.release.setValueAtTime(0.25, this.audioContext.currentTime);

        // Configurar el analizador
        this.analyser.fftSize = 256;

        // Conectar la cadena de procesamiento
        this.compressor.connect(this.gainNode);
        this.gainNode.connect(this.analyser);
    }

    setupAudioSource(audioElement) {
        if (!this.source) {
            this.source = this.audioContext.createMediaElementSource(audioElement);

            // Conectar inicialmente a través de la cadena de procesamiento
            this.source.connect(this.compressor);
            this.analyser.connect(this.audioContext.destination);

            // Iniciar AGC por defecto
            this.startAGC();
        }
        return this.source;
    }

    connectSource() {
        if (!this.source || this.isProcessingEnabled) return;

        try {
            // Desconectar del destino directo
            this.source.disconnect();

            // Reconectar a través de la cadena de procesamiento
            this.source.connect(this.compressor);
            this.analyser.connect(this.audioContext.destination);

            this.isProcessingEnabled = true;
            this.startAGC();
            console.log("Procesamiento de audio activado");
        } catch (error) {
            console.error("Error al conectar procesamiento:", error);
        }
    }

    disconnectSource() {
        if (!this.source || !this.isProcessingEnabled) return;

        try {
            // Desconectar toda la cadena
            this.source.disconnect();
            this.analyser.disconnect();

            // Conectar directamente al destino
            this.source.connect(this.audioContext.destination);

            this.isProcessingEnabled = false;
            console.log("Procesamiento de audio desactivado");
        } catch (error) {
            console.error("Error al desconectar procesamiento:", error);
        }
    }


  // control automático de ganancia
  startAGC(baseGain) {
    const bufferLength = this.analyser.frequencyBinCount;
    const dataArray = new Uint8Array(bufferLength);

    const updateGain = () => {
      this.analyser.getByteFrequencyData(dataArray);
      const average = dataArray.reduce((a, b) => a + b) / bufferLength;

      // Aumentamos significativamente la ganancia base
      if (!baseGain) baseGain = 0;

      // Calculamos un targetGain más agresivo
      const targetGain = baseGain * Math.pow(128 / (average + 1), 2);

      const currentGain = this.gainNode.gain.value;

      // Aumentamos la velocidad de ajuste para cambios más rápidos
      const newGain = currentGain + (targetGain - currentGain) * 0.1;

      // Limitamos el máximo de ganancia a un valor más alto pero controlado
      const maxGain = 8;
      this.gainNode.gain.setValueAtTime(
        Math.max(1, Math.min(maxGain, newGain)),
        this.audioContext.currentTime
      );

      // Aplicamos un compresor más agresivo
      this.compressor.threshold.setValueAtTime(
        -30,
        this.audioContext.currentTime
      );
      this.compressor.ratio.setValueAtTime(20, this.audioContext.currentTime);
      this.compressor.knee.setValueAtTime(10, this.audioContext.currentTime);
      this.compressor.attack.setValueAtTime(
        0.001,
        this.audioContext.currentTime
      );
      this.compressor.release.setValueAtTime(
        0.1,
        this.audioContext.currentTime
      );

      requestAnimationFrame(updateGain);
    };

    updateGain();
  }

  applyFixedGain() {
    const gainInDecibels = 20;
    const gainFactor = Math.pow(10, gainInDecibels / 20);
    this.gainNode.gain.setValueAtTime(
      gainFactor,
      this.audioContext.currentTime
    );
  }
}

export const webApiAvailable = () => {
  return !!(window.AudioContext || window.webkitAudioContext);
};
