#!/bin/bash

BASEDIR="/home/dh_xjtdnz/tseyor.xyz"
ARTISAN="$BASEDIR/current/artisan"
LOCKFILE="$BASEDIR/shared/_queue-worker.lock"
LOGDIR="$BASEDIR/shared/storage/logs"
LOGFILE="$LOGDIR/queue-worker.log"
COMMAND="php $ARTISAN queue:restart"
VERBOSE=true

if [ -e $LOCKFILE ]; then
  PID=$(cat $LOCKFILE)
  echo "Intentando detener el proceso $PID de manera ordenada..."

  # Terminar procesarmiento de colas, avisamos a laravel
  $COMMAND 

  # Esperar a que el proceso termine su trabajo actual
  while kill -0 $PID 2>/dev/null; do
    echo "Esperando a que el trabajador termine el trabajo actual..."
    sleep 5
  done

  echo "Daemon detenido exitosamente."
  rm -f $LOCKFILE
else
  echo "No se encontró el archivo de bloqueo. El daemon puede no estar en ejecución."
fi