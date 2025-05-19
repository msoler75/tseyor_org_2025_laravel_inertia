#!/bin/bash

DEPLOY_USER=${DEPLOY_USER:-}
if [ -z "$DEPLOY_USER" ]; then
  echo "ERROR: La variable de entorno DEPLOY_USER no está definida."
  exit 1
fi

# Usa solo pgrep, sin head ni pipelines
PID="$(pgrep -u "$DEPLOY_USER" -f 'artisan queue:work')"

if [ -n "$PID" ]; then
  for p in $PID; do
    echo "$p"
    break
  done
else
  echo "0"
fi
