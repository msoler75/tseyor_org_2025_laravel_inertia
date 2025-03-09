#!/bin/bash

# Nombre del script que ejecuta el SSR
RUN_SSR_SCRIPT="ssr_run.sh"

# Función para verificar si el proceso está en ejecución
check_ssr() {
    if pgrep -f "$RUN_SSR_SCRIPT" > /dev/null
    then
        echo "1"
        return 1
    else
        echo "0"
        return 0
    fi
}

# Ejecutar la verificación
check_ssr

# Salir con el código de estado apropiado
exit $?
