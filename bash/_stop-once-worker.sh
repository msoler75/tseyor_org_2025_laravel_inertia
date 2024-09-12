#!/bin/bash

BASEDIR="/home/REMOVED_USER/tseyor.org"
LOCKFILE="$BASEDIR/shared/_queue-worker.lock"

if [ -e $LOCKFILE ]; then
  PID=$(cat $LOCKFILE)
  echo "Intentando detener el proceso $PID de manera ordenada..."

  # Enviar señal de terminación
  kill $PID

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