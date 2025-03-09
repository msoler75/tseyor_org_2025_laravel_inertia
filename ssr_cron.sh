#!/bin/bash

# Ruta a los scripts
CHECK_SSR="./ssr_check.sh"
RUN_SSR="./ssr_run.sh"

# Verificar si el servidor SSR está en ejecución
if ! $CHECK_SSR; then
    echo "El servidor SSR no está en ejecución. Iniciando..."
    $RUN_SSR
else
    echo "El servidor SSR está funcionando correctamente."
fi
