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
COMMAND="php $ARTISAN queue:work --once --sleep=3 --tries=3 --timeout=60"
VERBOSE=true

# Verifica si se pasó el argumento -q o --quietly
for arg in "$@"; do
  if [ "$arg" == "-q" ] || [ "$arg" == "--quietly" ]; then
    VERBOSE=false
    break
  fi
done

# Verifica si el script ya está en ejecución
if [ -e $LOCKFILE ] && kill -0 $(cat $LOCKFILE); then
  echo "El proceso ya está en ejecución."
  exit
fi

# Verificar si el modo VERBOSE está activado
if [ "$VERBOSE" = true ]; then
  echo "Modo VERBOSE"
fi

# Crear el archivo de log si no existe
mkdir -p $LOGDIR
touch $LOGFILE

# Configura un trap para manejar SIGTERM
trap "SHOULD_STOP=true" SIGTERM

# Guarda el PID del script en el archivo de bloqueo
echo $$ > $LOCKFILE

# Variable para controlar el bucle
SHOULD_STOP=false

# Función para manejar las colas
run_queue_worker() {
  while true; do
    if [ "$SHOULD_STOP" = true ]; then
      echo "Deteniendo el proceso de manera ordenada..."
      break
    fi

    # Ejecutar el comando para procesar un solo trabajo
    if [ "$VERBOSE" = true ]; then
      $COMMAND 2>&1 | tee -a $LOGFILE
    else
      $COMMAND >> $LOGFILE 2>&1
    fi

    # Capturar el código de salida
    EXIT_CODE=$?

    # Si no hay trabajos, duerme por un tiempo más largo
    if [ $EXIT_CODE -eq 0 ]; then
      if [ "$VERBOSE" = true ]; then
        echo "Esperando..."
      fi
      sleep 10
    fi
  done
}

# Ejecuta la función
run_queue_worker

# Elimina el archivo de bloqueo al terminar
rm -f $LOCKFILE
