#!/bin/bash

# Script para verificar carga del sistema sin usar fork (evita "Resource temporarily unavailable")
# Lee directamente de /proc para obtener información de carga y procesos
# Imprime a stdout; redirige desde cron con >> para appendar al log

# Función para obtener el comando del proceso hijo si es un script .sh
get_child_cmd() {
    local pid="$1"
    local original_cmd="$2"
    # Si es bash ejecutando un .sh, buscar hijos
    if [[ "$original_cmd" == "/bin/bash "* && "$original_cmd" == *".sh "* ]]; then
        # Leer hijos desde /proc/<pid>/task/<pid>/children
        local children_file="/proc/$pid/task/$pid/children"
        if [ -f "$children_file" ]; then
            local children
            read -r children < "$children_file"
            for child_pid in $children; do
                if [ -f "/proc/$child_pid/cmdline" ]; then
                    local child_cmd=$(tr '\0' ' ' < "/proc/$child_pid/cmdline")
                    if [ -n "$child_cmd" ] && [[ "$child_cmd" != "/bin/bash "* ]]; then
                        echo "$child_cmd"
                        return
                    fi
                fi
            done
        fi
    fi
    echo "$original_cmd"
}

echo "=== Información de Carga del Sistema ==="
echo "Fecha: $(date)"

# Carga del sistema (equivalente a uptime)
if [ -f /proc/loadavg ]; then
    LOADAVG=$(cat /proc/loadavg)
    echo "Carga promedio (1m, 5m, 15m): $LOADAVG"
else
    echo "No se puede leer /proc/loadavg"
fi

# Número de procesos corriendo
if [ -d /proc ]; then
    PROC_COUNT=0
    for dir in /proc/[0-9]*; do
        PROC_COUNT=$((PROC_COUNT + 1))
    done
    echo "Procesos activos: $PROC_COUNT"
else
    echo "No se puede acceder a /proc"
fi

# Memoria (lee de /proc/meminfo)
if [ -f /proc/meminfo ]; then
    while read -r line; do
        case $line in
            MemTotal:*) MEM_TOTAL=${line#*: } ;;
            MemFree:*) MEM_FREE=${line#*: } ;;
        esac
    done < /proc/meminfo
    MEM_TOTAL=${MEM_TOTAL%% *}
    MEM_FREE=${MEM_FREE%% *}
    MEM_USED=$((MEM_TOTAL - MEM_FREE))
    echo "Memoria total: ${MEM_TOTAL} KB"
    echo "Memoria usada: ${MEM_USED} KB"
    echo "Memoria libre: ${MEM_FREE} KB"
else
    echo "No se puede leer /proc/meminfo"
fi

# CPU (lee de /proc/stat)
if [ -f /proc/stat ]; then
    read -r CPU_LINE < /proc/stat
    set -- $CPU_LINE
    shift  # skip 'cpu'
    CPU_TOTAL=0
    for val; do
        CPU_TOTAL=$((CPU_TOTAL + val))
    done
    CPU_IDLE=$5
    CPU_USED=$((CPU_TOTAL - CPU_IDLE))
    echo "CPU total ticks: $CPU_TOTAL"
    echo "CPU idle ticks: $CPU_IDLE"
    echo "CPU used ticks: $CPU_USED"
else
    echo "No se puede leer /proc/stat"
fi

# Lista de procesos (equivalente simplificado a ps aux)
echo ""
echo "=== Lista de Procesos (PID, Usuario, Memoria RSS KB, Comando) ==="
if [ -d /proc ] && [ -f /etc/passwd ]; then
    echo "PID     Usuario          RSS KB    Comando"
    echo "------------------------------------------"
    count=0
    for pid in /proc/[0-9]*; do
        pid=$(basename "$pid")
        if [ -f "/proc/$pid/status" ] && [ -f "/proc/$pid/cmdline" ] && [ "$count" -lt 20 ]; then
            # Leer UID del proceso (usando read para evitar subshells)
            while IFS=: read -r key value; do
                if [ "$key" = "Uid" ]; then
                    read -r uid _ <<< "$value"
                    break
                fi
            done < "/proc/$pid/status"
            # Mapear UID a usuario
            while IFS=: read -r u rest; do
                if [ "$u" = "$uid" ]; then
                    user=$u
                    break
                fi
            done < /etc/passwd
            [ -z "$user" ] && user="$uid"
            # Leer memoria RSS
            while IFS=: read -r key value; do
                if [ "$key" = "VmRSS" ]; then
                    read -r rss _ <<< "$value"
                    break
                fi
            done < "/proc/$pid/status"
            [ -z "$rss" ] && rss="0"
            # Leer comando
            cmd=$(tr '\0' ' ' < "/proc/$pid/cmdline")
            # Obtener comando del hijo si es un script .sh
            cmd=$(get_child_cmd "$pid" "$cmd")
            cmd=${cmd:0:100}
            [ -z "$cmd" ] && cmd="[kernel]"
            printf "%-7s %-15s %-9s %s\n" "$pid" "$user" "$rss" "$cmd"
            count=$((count + 1))
        fi
    done
else
    echo "No se puede acceder a /proc o /etc/passwd"
fi

echo "=== Fin del reporte ==="
