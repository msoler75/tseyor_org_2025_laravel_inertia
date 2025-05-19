#!/bin/bash

DEPLOY_USER=${DEPLOY_USER:-}
if [ -z "$DEPLOY_USER" ]; then
  echo "ERROR: La variable de entorno DEPLOY_USER no está definida."
  exit 1
fi

BASEDIR="/home/$DEPLOY_USER/tseyor.org"
ARTISAN="$BASEDIR/current/artisan"
LOGDIR="$BASEDIR/shared/storage/logs"
LOGFILE="$LOGDIR/queue-worker.log"
COMMAND="php $ARTISAN queue:restart"
VERBOSE=true

# Buscar el primer PID del worker (sin head ni subprocesos extra)
PIDS=$(pgrep -u "$DEPLOY_USER" -f 'artisan queue:work')
PID=""
for p in $PIDS; do
  PID=$p
  break
done

if [ -n "$PID" ]; then
  echo "Intentando detener el proceso $PID de manera ordenada..."
  $COMMAND
  while kill -0 $PID 2>/dev/null; do
    echo "Esperando a que el trabajador termine el trabajo actual..."
    sleep 5
  done
  echo "Daemon detenido exitosamente."
else
  echo "No se encontró ningún worker ejecutándose."
fi
