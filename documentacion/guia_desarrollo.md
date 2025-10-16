# Guía de Desarrollo

Este archivo contiene información útil para el mantenimiento y la supervisión de fallos de la web de Tseyor.

## Instrucciones para configurar el entorno de desarrollo del proyecto Tseyor.org 2025.

### Requisitos
- PHP >= 8.1
- Composer
- Node.js >= 18
- npm o yarn
- Docker (opcional para servicios como MailHog)

### Pasos
1. Clona el repositorio
2. Instala dependencias backend: `composer install`
3. Instala dependencias frontend: `npm install`
4. Copia `.env.example` a `.env` y configura variables
5. Ejecuta migraciones: `php artisan migrate`
6. Inicia el servidor de desarrollo: `php artisan serve` y `npm run dev`
7. (Opcional) Para testar envío de mails, inicia servicios de pruebas: `mailhog.cmd`

### Notas
- Consulta arquitectura.md para entender la estructura
- Usa tareas_pendientes.md para ver tareas pendientes

## Logs

*   Revisar los logs del servidor para detectar errores y problemas.
*   Verificar el log de la base de datos de Muular Electrónico.
*   Revisar el error de `Intervention\Image\Exceptions\GeometryException` en los logs.

## Caché

*   Limpiar la caché: `php artisan cache:clear`
*   Limpiar la configuración: `php artisan config:clear`
*   Limpiar el page-cache: `php artisan page-cache:clear`

## Trucos

*   Para testar envío de mails, iniciar servicios de pruebas: `mailhog.cmd`
*   Si hay errores constantes de CSRF en `dev.tseyor.org`, borrar todas las cookies de `.tseyor.org`.
*   Para deshabilitar analytics (Microsoft Clarity y Google Analytics) en desarrollo o testing, añade `?noanalytics` al final de la URL.

## Otros

*   Panel para ver mensajes recibidos en `notificaciones@tseyor.org`
*   Revisar correos de oficinas `@tseyor.org`

Para información sobre:
- **Mantenimiento, logs, caché, trucos y otros**: consulta el archivo [`mantenimiento.md`](./mantenimiento.md)
- **Seguridad**: consulta el archivo [`mantenimiento.md`](./mantenimiento.md)
- **Despliegue y estructura de carpetas**: consulta el archivo [`despliegue.md`](./despliegue.md)

## Trucos

*   Para testar envío de mails, iniciar servicios de pruebas: `mailhog.cmd`
*   Si hay errores constantes de CSRF en `dev.tseyor.org`, borrar todas las cookies de `.tseyor.org`.

### [Ver índice de documentación](./index.md)
