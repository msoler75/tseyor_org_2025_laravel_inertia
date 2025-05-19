#!/bin/bash

DEPLOY_USER=${DEPLOY_USER:-}
if [ -z "$DEPLOY_USER" ]; then
  echo "ERROR: La variable de entorno DEPLOY_USER no está definida."
  exit 1
fi


BASEDIR="/home/$DEPLOY_USER/tseyor.org"
ARTISAN="$BASEDIR/current/artisan"
SCRIPT_DIR="$BASEDIR/current/bash"

# Reinicia el worker llamando a los scripts de stop y start, sin subprocesos extra

echo "Deteniendo el worker..."
$SCRIPT_DIR/worker-stop.sh
echo "Iniciando el worker..."
$SCRIPT_DIR/worker-start.sh -q
