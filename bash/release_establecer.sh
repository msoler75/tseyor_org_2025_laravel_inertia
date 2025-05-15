#!/bin/bash

DEPLOY_USER=${DEPLOY_USER:-}
if [ -z "$DEPLOY_USER" ]; then
  echo "ERROR: La variable de entorno DEPLOY_USER no está definida."
  exit 1
fi

# Directorio base del proyecto
BASE_DIR="/home/$DEPLOY_USER/tseyor.org"

# Función para mostrar el uso correcto del script
mostrar_uso() {
    echo "Uso: $0 <número_de_release>"
    exit 1
}

# Verificar si se proporcionó un argumento
if [ $# -ne 1 ]; then
    mostrar_uso
fi

# Verificar si el argumento es un número
if ! [[ $1 =~ ^[0-9]+$ ]]; then
    echo "Error: El argumento debe ser un número."
    mostrar_uso
fi

# Número de release proporcionado como argumento
RELEASE_NUM=$1

# Ruta completa a la release especificada
RELEASE_PATH="$BASE_DIR/releases/$RELEASE_NUM"

# Verificar si la release especificada existe
if [ ! -d "$RELEASE_PATH" ]; then
    echo "Error: La release $RELEASE_NUM no existe."
    exit 1
fi

# Eliminar el enlace simbólico existente
unlink "$BASE_DIR/current"

# Crear el nuevo enlace simbólico
ln -s "$RELEASE_PATH" "$BASE_DIR/current"

# Limpiar cachés de la nueva release activa
cd "$BASE_DIR/current" || exit
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Mensaje final
echo "El enlace 'current' ahora apunta a releases/$RELEASE_NUM"
echo "Cachés limpiadas en la nueva release activa."
