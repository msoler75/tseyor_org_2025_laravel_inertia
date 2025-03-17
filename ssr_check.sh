#!/bin/bash

RUN_SSR_SCRIPT="ssr_run.sh"

check_ssr() {
    if pgrep -f "$RUN_SSR_SCRIPT" > /dev/null
    then
        echo "1"
        return 1  # Proceso encontrado, éxito
    else
        echo "0"
        return 0  # Proceso no encontrado, fallo
    fi
}

check_ssr
exit $?
