# Mantenimiento del Sistema

Esta guía proporciona información práctica para el mantenimiento diario y resolución de problemas comunes en la plataforma Tseyor.org.

## 📋 Tareas de Mantenimiento Rutinario

### Revisión de Logs

Los logs son fundamentales para detectar y diagnosticar problemas. Ubicaciones importantes:

```bash
# Logs de Laravel (errores de aplicación)
tail -f storage/logs/laravel.log

# Logs del servidor web (nginx/apache)
tail -f /var/log/nginx/error.log

# Logs de workers de colas
tail -f storage/logs/worker.log
```

**Errores comunes a vigilar:**
- Errores de base de datos (conexión, consultas lentas)
- Excepciones no capturadas en controladores
- Errores de memoria (memory exhausted)
- Problemas con procesamiento de archivos multimedia

### Gestión de Caché

El sistema utiliza múltiples capas de caché que deben mantenerse:

```bash
# Limpiar toda la caché de aplicación
php artisan cache:clear

# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de rutas
php artisan route:clear

# Limpiar caché de vistas
php artisan view:clear

# Limpiar page-cache (HTML estático)
php artisan page-cache:clear

# Regenerar optimizaciones
php artisan optimize
```

**Cuándo limpiar la caché:**
- Después de cambios en archivos `.env`
- Tras actualizar configuración de Laravel
- Si hay comportamiento inconsistente en la aplicación
- Después de un despliegue (se hace automáticamente con scripts)

### Gestión de Colas y Workers

Los workers procesan trabajos asíncronos. Monitoreo y control:

```bash
# Ver estado de los workers
ps aux | grep "queue:work"

# Reiniciar workers (detener y dejar que systemd los reinicie)
php artisan queue:restart

# Ver trabajos fallidos
php artisan queue:failed

# Reintentar trabajos fallidos
php artisan queue:retry all

# Limpiar trabajos fallidos antiguos
php artisan queue:flush
```

**Trabajos importantes del sistema:**
- Procesamiento de audio (conversión de formatos)
- Envío masivo de correos (boletines, notificaciones)
- Generación de PDFs
- Notificaciones de inscripciones

Ver más detalles en [`colas_y_trabajos.md`](./colas_y_trabajos.md).

## 🔍 Monitoreo y Supervisión

### Base de Datos

```bash
# Verificar conexión a la base de datos
php artisan db:monitor

# Ver tamaño de tablas importantes
php artisan db:show

# Ejecutar optimizaciones de tablas
php artisan db:optimize
```

**Tablas críticas a monitorear:**
- `users`: Usuarios registrados
- `inscripciones`: Sistema de gestión de cursos
- `muular_transacciones`: Registro de moneda virtual
- `jobs`: Cola de trabajos pendientes

### Muular Electrónico

El sistema de moneda virtual requiere supervisión especial:

```bash
# Verificar log de transacciones
tail -f storage/logs/muular.log

# Revisar inconsistencias en saldos
php artisan muular:verificar
```

**Tareas de mantenimiento:**
- Revisar transacciones sospechosas o duplicadas
- Verificar que los saldos sean coherentes
- Monitorear usuarios con saldo negativo

### Correos Electrónicos

```bash
# Ver correos en cola de envío
php artisan queue:work --queue=emails

# Revisar rebotes de correo
grep "Undelivered Mail" storage/logs/laravel.log
```

**Puntos de verificación:**
- Revisar mensajes recibidos en `notificaciones@tseyor.org`
- Verificar correos de oficinas `@tseyor.org`
- Comprobar tasa de entrega de boletines

### Almacenamiento

```bash
# Ver espacio en disco utilizado
df -h

# Ver tamaño de directorio de storage
du -sh storage/

# Ver archivos grandes en almacén
du -h almacen/medios/ | sort -rh | head -20

# Limpiar archivos temporales antiguos
php artisan storage:clean
```

## ⚠️ Resolución de Problemas Comunes

### El sitio está lento

1. Verificar uso de CPU y memoria: `htop`
2. Revisar consultas lentas en logs de base de datos
3. Limpiar caché: `php artisan cache:clear`
4. Verificar que workers estén funcionando
5. Revisar tamaño de la cola de trabajos

### Error 500 en producción

1. Revisar `storage/logs/laravel.log`
2. Verificar permisos de `storage/` y `bootstrap/cache/`
3. Verificar conexión a base de datos
4. Comprobar variables de entorno en `.env`

### Los workers no procesan trabajos

1. Verificar que los workers estén corriendo: `ps aux | grep queue:work`
2. Reiniciar workers: `php artisan queue:restart`
3. Verificar conexión a Redis: `redis-cli ping`
4. Revisar logs de errores: `storage/logs/worker.log`

### Problemas con imágenes

Si aparece `Intervention\Image\Exceptions\GeometryException`:
1. Verificar que GD o Imagick estén instalados
2. Comprobar permisos de escritura en `storage/app/public/`
3. Revisar tamaño y formato de imágenes subidas

### Búsqueda no funciona

```bash
# Reindexar contenido de búsqueda
php artisan scout:import "App\Models\Comunicado"
php artisan scout:import "App\Models\Noticia"
php artisan scout:import "App\Models\Entrada"

# Limpiar índices
php artisan tntsearch:import
```

## 🔐 Seguridad

### Verificaciones de seguridad rutinarias

- Comprobar intentos de acceso no autorizado en logs
- Revisar solicitudes masivas de cambio de contraseña (posible trolling)
- Verificar integridad de archivos críticos
- Monitorear intentos de hackeo en sistema de invitaciones

Ver más en [`seguridad.md`](./seguridad.md).

## 🚀 Modo Mantenimiento

Para poner el sitio en mantenimiento durante actualizaciones críticas:

```bash
# Activar modo mantenimiento
php artisan down --secret="token-secreto"

# Desactivar modo mantenimiento
php artisan up
```

El modo mantenimiento muestra la vista `resources/views/mantenimiento.blade.php`.

Acceso durante mantenimiento: `https://tseyor.org/token-secreto`

## 📚 Referencias

- [Despliegue y Deploy](./despliegue.md): Proceso completo de despliegue
- [Gestión de Colas](./colas_y_trabajos.md): Detalles sobre trabajos asíncronos
- [Scripts y Utilidades](./scripts_utiles.md): Scripts útiles del proyecto
- [Seguridad](./seguridad.md): Políticas de seguridad
- [TODO.md](../TODO.md): Tareas pendientes de desarrollo
- [TASKS.md](../TASKS.md): Tareas completadas y en progreso

---

### [Ver índice de documentación](./index.md)
