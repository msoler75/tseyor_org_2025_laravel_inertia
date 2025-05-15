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

## Despliegue

*   Estructura recomendada de carpetas en producción:

    ```
    /tseyor.orgyor.org
    ├── release_crear.sh
    ├── release_establecer.sh
    ├── shared
    │   ├── .env
    │   └── storage/
    ├── releases
    │   ├── 1/
    │   ├── 2/
    │   └── ...
    └── current -> releases/2/
    ```

*   Proceso de despliegue (nueva release):

    1.  Definir usuario de despliegue: `export DEPLOY_USER=tu_usuario`
    2.  Ejecutar el script de creación de release: `./release_crear.sh`
    3.  Actualizar el enlace simbólico 'current': `./release_establecer.sh <número_release>`

*   Notas importantes:

    *   No editar archivos directamente en `releases/`.
    *   El archivo `.env` y la carpeta `storage` son compartidos.
    *   Para rollback: ejecutar `./release_establecer.sh <número_release>`.
    *   Asegurarse de que los scripts y carpetas tengan los permisos adecuados.
    *   Limpiar cachés de views y rutas tras cada cambio de release.
    *   Revisar logs y estado de workers tras cada despliegue.
    *   Añadir instrucciones para mover los scripts `release_crear.sh` y `release_establecer.sh` a la raíz del sitio web tras cada actualización.
    *   Documentar el proceso de rollback usando `release_establecer.sh`.

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

*   Instalar `Laravel-Abuse-IP`
*   Integración de middleware de seguridad avanzada

## Trucos

*   Para testar envío de mails, iniciar servicios de pruebas: `mailhog.cmd`
*   Si hay errores constantes de CSRF en `dev.tseyor.org`, borrar todas las cookies de `.tseyor.org`.

## Otros

*   Panel para ver mensajes recibidos en `notificaciones@tseyor.org`
*   Revisar correos de oficinas `@tseyor.org`
*   Comprobar posibles hackeos de invitación
