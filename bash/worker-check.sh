#!/bin/bash

DEPLOY_USER=${DEPLOY_USER:-}
if [ -z "$DEPLOY_USER" ]; then
  echo "ERROR: La variable de entorno DEPLOY_USER no está definida."
  exit 1
fi

BASEDIR="/home/$DEPLOY_USER/tseyor.org"
LOCKFILE="$BASEDIR/shared/_queue-worker.lock"

# Verifica si el archivo de bloqueo existe
if [ -e $LOCKFILE ]; then
  PID=$(cat $LOCKFILE)

  # Verifica si el proceso con el PID almacenado está en ejecución
  if kill -0 $PID 2>/dev/null; then
    echo "$PID"
    exit 0
  else
    echo "0"
    exit 0
  fi
else
  echo "0"
  exit 0
fi
