#!/bin/bash

DEPLOY_USER=${DEPLOY_USER:-}
if [ -z "$DEPLOY_USER" ]; then
  echo "ERROR: La variable de entorno DEPLOY_USER no está definida."
  exit 1
fi

BASEDIR="/home/$DEPLOY_USER/tseyor.org"
LOCKFILE="$BASEDIR/shared/_queue-worker.lock"

# Función para buscar procesos queue:work del usuario
find_queue_work_pid() {
  ps -u "$DEPLOY_USER" -o pid=,args= | grep '[a]rtisan queue:work' | awk '{print $1}' | head -n1
}

# Verifica si el archivo de bloqueo existe
if [ -e "$LOCKFILE" ]; then
  PID=$(cat "$LOCKFILE")
  # Verifica si el proceso con el PID almacenado está en ejecución y es queue:work
  if ps -p "$PID" -o args= | grep -q 'artisan queue:work'; then
    echo "$PID"
    exit 0
  fi
fi

# Si no, busca manualmente el proceso
PID=$(find_queue_work_pid)
if [ -n "$PID" ]; then
  echo "$PID"
else
  echo "0"
fi
exit 0
