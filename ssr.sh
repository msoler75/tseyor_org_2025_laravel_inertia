#!/bin/bash

LOG_FILE_SSR="storage/logs/ssr.log"


# Mostrar uso del script
usage() {
    echo "Uso: $0 {start|stop|status}"
    exit 1
}

# Verificar si el proceso SSR está en ejecución
check_status() {
    SSR_PROCESS=$(pgrep -f "php artisan inertia:start-ssr")
    if [ -n "$SSR_PROCESS" ]; then
        echo "El servidor SSR está funcionando. PID(s): $SSR_PROCESS"
    else
        echo "El servidor SSR no está en ejecución."
    fi
}

# Iniciar el proceso SSR
start_ssr() {
    SSR_PROCESS=$(pgrep -f "php artisan inertia:start-ssr")
    if [ -n "$SSR_PROCESS" ]; then
        echo "El servidor SSR ya está en ejecución. PID(s): $SSR_PROCESS"
    else
        echo "Iniciando el servidor SSR..."
        php artisan inertia:start-ssr >> "$LOG_FILE_SSR" 2>&1 &
        echo "Servidor SSR iniciado."
    fi
}

# Detener el proceso SSR
stop_ssr() {
    echo "Deteniendo el servidor SSR..."
    SSR_PROCESS=$(pgrep -f "php artisan inertia:start-ssr")
    if [ -n "$SSR_PROCESS" ]; then
        echo "$SSR_PROCESS" | xargs kill -9
        echo "Servidor SSR detenido. PID(s): $SSR_PROCESS"
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
    status)
        check_status
        ;;
    *)
        usage
        ;;
esac
