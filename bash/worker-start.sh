#!/bin/bash

DEPLOY_USER=${DEPLOY_USER:-}
if [ -z "$DEPLOY_USER" ]; then
  echo "ERROR: La variable de entorno DEPLOY_USER no está definida."
  exit 1
fi

BASEDIR="/home/$DEPLOY_USER/tseyor.org"
ARTISAN="$BASEDIR/current/artisan"
LOCKFILE="$BASEDIR/shared/_queue-worker.lock"
LOGDIR="$BASEDIR/shared/storage/logs"
LOGFILE="$LOGDIR/queue-worker.log"
COMMAND="php $ARTISAN queue:work --queue=default,audio_processing --sleep=3 --tries=10 --timeout=120"
VERBOSE=true

# Verifica si se pasó el argumento -q o --quietly
for arg in "$@"; do
  if [ "$arg" == "-q" ] || [ "$arg" == "--quietly" ]; then
    VERBOSE=false
    break
  fi
done

# Crear el archivo de log si no existe
mkdir -p $LOGDIR
touch $LOGFILE

# Ejecutar el worker con flock para evitar concurrencia
if [ "$VERBOSE" = true ]; then
  (
    flock -n 9 || { echo "El proceso ya está en ejecución."; exit 1; }
    $COMMAND 2>&1 | tee -a $LOGFILE
  ) 9>$LOCKFILE
else
  (
    flock -n 9 || { echo "El proceso ya está en ejecución."; exit 1; }
    $COMMAND >> $LOGFILE 2>&1
  ) 9>$LOCKFILE &
  disown
fi
