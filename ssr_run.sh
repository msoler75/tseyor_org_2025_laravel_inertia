#!/bin/bash

LOG_FILE="ssr__output.log"
MAX_RETRIES=12
RETRY_DELAY=3

# Función para registrar con timestamp
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

#!/bin/bash

# Ruta a los scripts
CHECK_SSR="./ssr_check.sh"

# Verificar si el servidor SSR está en ejecución
if [ "$($CHECK_SSR)" = "1" ]; then
    log "El servidor SSR está funcionando correctamente."
else
    log "El servidor SSR no está en ejecución. Iniciando..."

    while true; do
        # Ejecutar el comando y registrar salida
        php artisan inertia:start-ssr | tee -a "$LOG_FILE"
        EXIT_CODE=${PIPESTATUS[0]}

        if [ $EXIT_CODE -ne 0 ]; then
            log "Error detectado (Código: $EXIT_CODE). Reiniciando en $RETRY_DELAY segundos..."
            sleep $RETRY_DELAY

            # Lógica de reintentos con backoff
            ((RETRY_ATTEMPT++))
            if [ $RETRY_ATTEMPT -ge $MAX_RETRIES ]; then
                log "Máximo de reintentos alcanzado ($MAX_RETRIES). Deteniendo..."
                exit 1
            fi
        else
            RETRY_ATTEMPT=0
        fi
    done


fi
