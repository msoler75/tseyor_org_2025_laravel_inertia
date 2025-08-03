#!/bin/bash

DEPLOY_USER=${DEPLOY_USER:-}
if [ -z "$DEPLOY_USER" ]; then
  echo "ERROR: La variable de entorno DEPLOY_USER no está definida."
  exit 1
fi

# Directorio base del proyecto
BASE_DIR="/home/$DEPLOY_USER/tseyor.org"
REPOSITORY="https://github.com/msoler75/tseyor_org_2025_laravel_inertia"

# Entrar en la carpeta releases
cd "$BASE_DIR/releases" || exit

# Obtener el número de la última release
LAST_RELEASE=$(ls -d [0-9]* | sort -n | tail -1)

# Calcular el número de la nueva release
NEW_RELEASE=$((LAST_RELEASE + 1))

# Crear la nueva carpeta de release
mkdir "$NEW_RELEASE"
echo "Nueva carpeta de release creada: $NEW_RELEASE"
cd "$NEW_RELEASE" || exit

# Clonar el repositorio
git clone --branch main --single-branch $REPOSITORY .

# Crear symlink del archivo .env
ln -s "$BASE_DIR/shared/.env" .env

# Crear symlink de la carpeta storage
mv storage storage_old
ln -s "$BASE_DIR/shared/storage" storage

# Crear el directorio bootstrap/cache si no existe
echo "Verificando el directorio bootstrap/cache..."
mkdir -p bootstrap/cache
chmod -R 775 bootstrap/cache

# Instalar dependencias con composer
composer install --no-dev

# Generar el symbolic link public
# php artisan storage:link

# Migraciones
php artisan migrate

# Generar rutas ziggy
php artisan ziggy:generate

# Actualizar el enlace "current" a la nueva release
# ln -sf "$BASE_DIR/releases/$NEW_RELEASE" "$BASE_DIR/current"

chmod u+x bash/*.sh

# Mostrar la carpeta donde se instaló la nueva release

echo "=========================================="
echo "Despliegue completado. Nueva release: $NEW_RELEASE"
echo "La nueva release se encuentra en: $BASE_DIR/releases/$NEW_RELEASE"

echo "-----------------------------------------"
echo "En la carpeta 'bash' se encuentran los scripts actualizados para control de releases. Copialos en esta carpeta para mantenerte actualizado. Puedes usar el comando:"
echo "> cp -r $BASE_DIR/releases/$NEW_RELEASE/bash/release* ."

# Mostrar los siguientes pasos para actualizar a la nueva release

echo "-----------------------------------------"
echo "- Los siguientes pasos que debes realizar son:"
echo "./release_establecer.sh $NEW_RELEASE"
echo "-----------------------------------------"
echo "- Desde pc de desarrollo, ejecuta:"
echo "npm run build-all"
echo "php artisan deploy:nodemodules"
echo "php artisan deploy:front"
echo "php artisan deploy:ssr"


