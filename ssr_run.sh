#!/bin/bash

LOG_FILE="ssr__output.log"
MAX_RETRIES=12
RETRY_DELAY=3

# Función para registrar con timestamp
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "Iniciando servicio SSR..."

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
