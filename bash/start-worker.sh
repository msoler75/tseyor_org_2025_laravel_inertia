#!/bin/bash

BASEDIR="/home/dh_xjtdnz/tseyor.xyz"
ARTISAN="$BASEDIR/current/artisan"
LOCKFILE="$BASEDIR/shared/_queue-worker.lock"
LOGDIR="$BASEDIR/shared/storage/logs"
LOGFILE="$LOGDIR/queue-worker.log"
COMMAND="php $ARTISAN queue:work --sleep=3 --tries=3 --timeout=60"
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

# Crear el archivo de log si no existe
mkdir -p $LOGDIR
touch $LOGFILE

# Guarda el PID del script en el archivo de bloqueo
echo $$ > $LOCKFILE

# Función para manejar las colas
run_queue_worker() {
  # Ejecutar el comando para procesar trabajos continuamente
  if [ "$VERBOSE" = true ]; then
    $COMMAND 2>&1 | tee -a $LOGFILE
  else
    $COMMAND >> $LOGFILE 2>&1
  fi
}

# Ejecuta la función
run_queue_worker

# Elimina el archivo de bloqueo al terminar
rm -f $LOCKFILE