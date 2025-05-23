#!/bin/bash

# --- Configuración inicial ---
DEPLOY_USER=${DEPLOY_USER:-}
if [ -z "$DEPLOY_USER" ]; then
  echo "ERROR: La variable de entorno DEPLOY_USER no está definida."
  exit 1
fi

ENDPOINT_URL=https://tseyor.org/boletines/enviar-pendientes
BASEDIR="/home/$DEPLOY_USER/tseyor.org"
LOGDIR="$BASEDIR/shared/storage/logs"
LOGFILE="$LOGDIR/boletines.log"

# --- Validación de argumentos ---
if [ $# -ne 1 ]; then
  echo "Uso: $0 <TOKEN>"
  echo "Atención: falta de argumentos en boletin_enviar_pendientes.sh" >> "$LOGFILE"
  exit 2
fi
TOKEN="$1"

# --- Ejecutar CURL ---
URL="$ENDPOINT_URL"
echo "[ $(date '+%Y-%m-%d %H:%M:%S') ] Ejecutando CURL a $URL" >> "$LOGFILE"
curl -s -X POST -H "X-Boletin-Token: $TOKEN" "$URL" >> "$LOGFILE" 2>&1
RESULT=$?
if [ $RESULT -eq 0 ]; then
  echo "[ $(date '+%Y-%m-%d %H:%M:%S') ] Solicitud completada correctamente."
  echo "[ $(date '+%Y-%m-%d %H:%M:%S') ] Solicitud completada correctamente." >> "$LOGFILE"
else
  echo "[ $(date '+%Y-%m-%d %H:%M:%S') ] Error en la solicitud CURL (código $RESULT)."
  echo "[ $(date '+%Y-%m-%d %H:%M:%S') ] Error en la solicitud CURL (código $RESULT)." >> "$LOGFILE"
fi
