#!/bin/bash

DEPLOY_USER=${DEPLOY_USER:-}
if [ -z "$DEPLOY_USER" ]; then
  echo "ERROR: La variable de entorno DEPLOY_USER no está definida."
  exit 1
fi


HOME="/home/$DEPLOY_USER"
BASEDIR="/home/$DEPLOY_USER/tseyor.org"
ARTISAN="$BASEDIR/current/artisan"
LOCKFILE="$BASEDIR/shared/_queue-worker.lock"
LOGDIR="$BASEDIR/shared/storage/logs"
LOGFILE="$LOGDIR/ssr.log"
COMMAND_CLASSIC="php $ARTISAN inertia:start-ssr"
COMMAND="node $BASEDIR/current/bootstrap/ssr/ssr.js"
NODE_VERSION="18.20.2"
NVM_DIR="$HOME/.nvm"

# Mostrar uso del script
usage() {
    echo "Uso: $0 {start|stop|restart|status}"
    exit 1
}


# Verificar si el proceso SSR está en ejecución
check_status() {
    SSR_PROCESS=$(pgrep -f "$COMMAND")
    if [ -n "$SSR_PROCESS" ]; then
        echo "El servidor SSR está funcionando. PID(s): $SSR_PROCESS"
    else
        echo "El servidor SSR no está en ejecución."
    fi
}

# Iniciar el proceso SSR
start_ssr() {
    SSR_PROCESS=$(pgrep -f "$COMMAND")
    if [ -n "$SSR_PROCESS" ]; then
        echo "El servidor SSR ya está en ejecución. PID(s): $SSR_PROCESS"
    else

        # --- Cargar nvm y activar versión Node específica ---
        . "$NVM_DIR/nvm.sh"
        nvm use $NODE_VERSION > /dev/null
        # --------------------------------------------------

        echo "Iniciando el servidor SSR..."
        nohup $COMMAND >> "$LOGFILE" 2>&1 &
        echo "Servidor SSR iniciado."
    fi
}

# Detener el proceso SSR
stop_ssr() {
    echo "Deteniendo el servidor SSR..."
    if pkill -f "$COMMAND"; then
        echo "Servidor SSR detenido."
    else
        echo "No se encontraron procesos del servidor SSR en ejecución."
    fi
}

# Validar argumentos
if [ $# -ne 1 ]; then
    usage
fi

case "$1" in
    start)
        start_ssr
        ;;
    stop)
        stop_ssr
        ;;
    restart)
        stop_ssr
        start_ssr
        ;;
    status)
        check_status
        ;;
    *)
        usage
        ;;
esac
