# Instrucciones de Mantenimiento y Supervisión de Fallos

Este archivo contiene información útil para el mantenimiento y la supervisión de fallos de la web de Tseyor.

## Logs

*   Revisar los logs del servidor para detectar errores y problemas.
*   Verificar el log de la base de datos de Muular Electrónico.
*   Revisar el error de `Intervention\Image\Exceptions\GeometryException` en los logs.

## Caché

*   Limpiar la caché: `php artisan cache:clear`
*   Limpiar la configuración: `php artisan config:clear`
*   Limpiar el page-cache: `php artisan page-cache:clear`

## Seguridad

*   Recomendaciones generales:

    *   Mantener dependencias actualizadas
    *   Usar HTTPS en producción
    *   Revisar y limitar permisos de archivos y carpetas
    *   No exponer información sensible en logs ni errores
    *   Usar variables de entorno para credenciales

*   Prácticas específicas:

    *   Revisar autenticación y autorización en rutas y controladores
    *   Validar y sanear entradas de usuario
    *   Revisar configuración de CORS y cookies
    *   Limitar intentos de login y proteger endpoints sensibles

*   Integración de middleware de seguridad avanzada

## Trucos

*   Para testar envío de mails, iniciar servicios de pruebas: `mailhog.cmd`
*   Si hay errores constantes de CSRF en `dev.tseyor.org`, borrar todas las cookies de `.tseyor.org`.

## Otros

*   Panel para ver mensajes recibidos en `notificaciones@tseyor.org`
*   Revisar correos de oficinas `@tseyor.org`
*   Comprobar posibles hackeos de invitación

Para información sobre el proceso de despliegue, consulta el archivo [`_DEPLOYMENT.md`](_DEPLOYMENT.md).
