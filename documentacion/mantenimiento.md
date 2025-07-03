# Mantenimiento

Este archivo contiene información útil para el mantenimiento y la supervisión de fallos de la web de Tseyor.

## Logs

*   Revisar los logs del servidor para detectar errores y problemas.
*   Verificar el log de la base de datos de Muular Electrónico.
*   Revisar el error de `Intervention\Image\Exceptions\GeometryException` en los logs.

## Caché

*   Limpiar la caché: `php artisan cache:clear`
*   Limpiar la configuración: `php artisan config:clear`
*   Limpiar el page-cache: `php artisan page-cache:clear`

## Otros

*   Panel para ver mensajes recibidos en `notificaciones@tseyor.org`
*   Revisar correos de oficinas `@tseyor.org`
*   Comprobar posibles hackeos de invitación

## Despliegue

Para información detallada sobre el proceso de despliegue, estructura de carpetas y scripts, consulta el archivo [`despliegue.md`](./despliegue.md).

### [Ver índice de documentación](./index.md)
