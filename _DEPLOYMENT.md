# DEPLOYMENT.md

Procedimientos y scripts para el despliegue del proyecto Tseyor.org 2025.

## Despliegue manual
1. Subir archivos al servidor
2. Instalar dependencias: `composer install` y `npm install`
3. Configurar variables de entorno `.env`
4. Ejecutar migraciones: `php artisan migrate --force`
5. Compilar assets: `npm run build`
6. Limpiar cachés: `php artisan config:cache` y `php artisan route:cache`
7. Reiniciar workers y servicios necesarios


## Notas
- Consulta SETUP.md para la configuración inicial
- Verifica logs y estado de workers tras el despliegue
