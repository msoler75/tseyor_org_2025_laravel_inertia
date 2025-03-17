#!/bin/bash

LOG_FILE="ssr_stop.log"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# 1. Detener servicio SSR
log "Deteniendo servicio SSR..."
php artisan inertia:stop-ssr

# 2. Matar procesos relacionados
log "Buscando procesos SSR..."
SSR_PIDS=$(pgrep -f "ssr_run.sh")

if [ -n "$SSR_PIDS" ]; then
    log "Terminando procesos padre (ssr_run.sh) y su árbol..."
    kill -TERM -- -$(ps -o pgid= $SSR_PIDS | grep -o '[0-9]*') 2>/dev/null
    sleep 2
fi

# 3. Eliminar procesos huérfanos
log "Limpiando procesos residuales..."
pkill -f "inertia:start-ssr"
pkill -f "node.*ssr"

log "Verificación final:"
pgrep -af "ssr_run.sh|inertia:start-ssr|node" || log "No hay procesos residuales"
