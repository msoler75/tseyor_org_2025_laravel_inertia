#!/usr/bin/env bash
#
# actualizar-php-cve.sh — parche de seguridad PHP para el servidor tseyor (Ubuntu 24.04)
# CVE-2026-109618 / CVE-2026-109619 (RCE crítico, advisory PHP 21/10/2026)
#
# Uso:
#   sudo ./actualizar-php-cve.sh              # modo interactivo (pregunta antes)
#   sudo ./actualizar-php-cve.sh -y           # sin preguntar
#   ./actualizar-php-cve.sh --solo-verificar  # solo diagnostica (no necesita sudo)
#
# NOTA: tomá un snapshot en el panel de Contabo ANTES de ejecutarlo si hay parche.
#
set -euo pipefail

SOLO_VERIFICAR=0
CONFIRMAR=0
for arg in "$@"; do
    case "$arg" in
        --solo-verificar) SOLO_VERIFICAR=1 ;;
        -y|--yes) CONFIRMAR=1 ;;
        *) echo "Argumento desconocido: $arg"; exit 2 ;;
    esac
done

log()  { echo -e "\n==> $*"; }
ok()   { echo "    OK: $*"; }
aviso(){ echo "    AVISO: $*"; }
die()  { echo "    ERROR: $*" >&2; exit 1; }

# --- 0. Solo verificar -------------------------------------------------------
if [[ "$SOLO_VERIFICAR" -eq 1 ]]; then
    log "Diagnóstico (sin modificar nada)"
    echo "    PHP CLI: $(php -v 2>/dev/null | head -1 || echo 'no encontrado')"
    for sock in /run/php/php*-fpm.sock; do
        [[ -S "$sock" ]] && echo "    FPM activo: $sock"
    done
    log "Consultando política de paquetes (si hay sudo, refresca lists)..."
    if [[ $EUID -eq 0 ]]; then
        apt-get update -qq
    else
        echo "    (sin sudo: se usa la lista cacheada; para refrescar: sudo apt-get update)"
    fi
    log "Versión instalada vs candidata (php8.3-fpm):"
    apt-cache policy php8.3-fpm | sed -n '1,5p'
    INST=$(dpkg-query -W -f '${Version}' php8.3-fpm 2>/dev/null || echo '-')
    CAND=$(apt-cache policy php8.3-fpm | awk '/Candidate:/{print $2}')
    if [[ "$INST" == "$CAND" ]]; then
        echo
        echo ">>> Instalada == candidata ($INST). Si es .04.13 o superior, ya incluye el fix"
        echo ">>>    de CVE-2026-109618/109619. Verificá con: dpkg-query -W php8.3-fpm"
    elif [[ "$CAND" != "(none)" ]]; then
        echo
        echo ">>> HAY PARCHE DISPONIBLE ($INST -> $CAND). Ejecutá: sudo $0"
    else
        echo
        echo ">>> No se pudo determinar la versión candidata (lista cacheada vieja)."
    fi
    exit 0
fi

# --- 1. Requisitos -----------------------------------------------------------
log "Verificando entorno"
[[ $EUID -eq 0 ]] || die "Este script debe correr con sudo."
command -v apt-get >/dev/null || die "No se encontró apt-get (esto es un sistema Debian/Ubuntu)."

if ! grep -q "VERSION_ID=\"24.04\"" /etc/os-release 2>/dev/null; then
    aviso "El SO no es Ubuntu 24.04 — revisá manualmente el advisory."
fi

# Solo paquetes realmente instalados (status 'ii'): el glob 'php8.*' también
# matchea paquetes removidos-con-config (un) y virtuales; apt aborta con esos.
mapfile -t PKGS < <(dpkg-query -W -f='${Package} ${db:Status-Abbrev}\n' 'php8.*' 2>/dev/null \
    | awk '$2=="ii"{print $1}')
[[ ${#PKGS[@]} -gt 0 ]] || die "No hay paquetes php8.* instalados."

INST=$(dpkg-query -W -f '${Version}' php8.3-fpm 2>/dev/null || echo '-')
log "Paquetes PHP instalados (${#PKGS[@]}): ${PKGS[*]}"
echo "    Versión actual php8.3-fpm: $INST"

log "Verificando si Ubuntu ya publicó el parche (apt-get update)..."
apt-get update -qq
CAND=$(apt-cache policy php8.3-fpm | awk '/Candidate:/{print $2}')

if [[ "$CAND" == "(none)" ]]; then
    die "No se pudo determinar la versión candidata. Reintentá: sudo apt-get update && $0"
fi

if [[ "$INST" == "$CAND" ]]; then
    # Instalada == candidata: o ya está parcheado, o Ubuntu aún no publica.
    # Importante: los backports de Ubuntu NO cambian lo que muestra php -v
    # (sigue "8.3.6"); la revisión del paquete (.13+) es la que trae el fix.
    log "Ya estás en la última versión publicada por Ubuntu: $INST"
    echo "    Si es igual o superior a 8.3.6-0ubuntu0.24.04.13, ya incluye el fix"
    echo "    de CVE-2026-109618/109619 (verificá con: dpkg-query -W php8.3-fpm)."
    echo "    Si es .04.12 o anterior, Ubuntu aún no publicó el parche — reintentá más tarde."
    exit 0
fi

log "PARCHE DISPONIBLE: $INST -> $CAND"

# --- 2. Aviso de snapshot ----------------------------------------------------
cat <<'EOF'

    ┌──────────────────────────────────────────────────────────────┐
    │  ANTES DE CONTINUAR: tomá un SNAPSHOT en el panel de Contabo  │
    │  (VPS → Snapshots → Create Snapshot). Tardás un minuto.      │
    └──────────────────────────────────────────────────────────────┘
EOF

if [[ "$CONFIRMAR" -ne 1 ]]; then
    read -r -p "¿Ya tenés el snapshot (o querés arriesgarte) y continuamos? [s/N] " resp
    [[ "$resp" == "s" || "$resp" == "S" ]] || die "Cancelado por el usuario. No se modificó nada."
fi

# --- 3. Backup de la BD (best effort, no bloquea) ----------------------------
log "Backup de la base de datos (best effort)"
if [[ -f /home/dev_901/tseyor/artisan ]]; then
    (cd /home/dev_901/tseyor && sudo -u www-data php artisan db:backup --force 2>/dev/null) \
        && ok "db:backup ejecutado" \
        || aviso "db:backup no corrió (no bloquea el upgrade; el snapshot de Contabo cubre el sistema)"
else
    aviso "No se encontró artisan — se confía en el snapshot de Contabo."
fi

# --- 4. Actualización --------------------------------------------------------
log "Instalando parche (apt-get install --only-upgrade)"
DEBIAN_FRONTEND=noninteractive apt-get install -y --only-upgrade "${PKGS[@]}"
ok "Paquetes actualizados"

# --- 5. Reinicio de PHP-FPM --------------------------------------------------
log "Reiniciando PHP-FPM (esto corta requests en vuelo ~1-2 s)"
if systemctl list-unit-files | grep -q '^php8\..*-fpm\.service'; then
    for svc in php8.*-fpm.service; do
        systemctl restart "$svc"
        ok "$svc reiniciado"
    done
else
    aviso "No se encontró servicio php-fpm — reiniciá manualmente tu servidor web."
fi

# --- 6. Verificación ---------------------------------------------------------
log "Verificación"
NUEVA=$(php -v | head -1)
NUEVA_PKG=$(dpkg-query -W -f '${Version}' php8.3-fpm 2>/dev/null || echo '-')
echo "    CLI: $NUEVA"
echo "    Paquete php8.3-fpm: $NUEVA_PKG (antes: $INST)"
# Ojo: los backports de Ubuntu NO cambian el número de php -v (sigue "8.3.6");
# la revisión del paquete es la que trae el fix. Verificamos contra dpkg.
[[ "$NUEVA_PKG" != "$INST" ]] || die "El paquete sigue en la versión vieja ($INST)."

for sock in /run/php/php8*-fpm.sock; do
    if [[ -S "$sock" ]]; then
        ok "socket FPM activo: $sock"
        svc="php${sock#/run/php/php}"; svc="${svc%.sock}"
        systemctl is-active --quiet "$svc" && ok "$svc activo" || die "$svc NO activo"
    fi
done
# Caddy usa php_fastcgi 127.0.0.1:9000 → verificar ese puerto
if command -v ss >/dev/null; then
    ss -ltn 2>/dev/null | grep -q ':9000 ' && ok "FPM escuchando en 127.0.0.1:9000 (target de Caddy)" \
        || aviso "No se ve el :9000 — revisá php8.3-fpm y el pool www.conf"
fi

HTTP=$(curl -s -o /dev/null -m 10 -w '%{http_code}' http://127.0.0.1/ || echo 000)
echo "    HTTP local: $HTTP"
[[ "$HTTP" == "200" || "$HTTP" == "301" || "$HTTP" == "302" ]] \
    && ok "El sitio responde" \
    || aviso "El sitio no devolvió 2xx/3xx localmente — revisá Caddy y php-fpm: systemctl status php8.3-fpm"

# Señales de compromiso rápidas (advisory)
log "Chequeo rápido de higiene (informativo)"
find /home/dev_901/tseyor/public -name "*.php" -mmin -60 2>/dev/null | head -5 | grep . \
    && aviso "Hay .php modificados en los últimos 60 min en public/ — revisalos." \
    || ok "Sin .php recién modificados en public/."
ps -eo user,pid,pcpu,comm --sort=-pcpu | head -4

log "LISTO. Versión final: $NUEVA"
echo "    Si algo salió mal: snapshot de Contabo → Restore."
